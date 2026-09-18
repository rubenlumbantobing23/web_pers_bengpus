<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Personel;
use App\Models\MarriageApplication;
use App\Models\MarriagePartner;
use App\Models\MarriageDocumentType;
use App\Models\MarriageDocument;
use Illuminate\Support\Facades\DB;

class MarriageApplicationApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser;
    protected $personel;
    protected $partner;
    protected $application;
    protected $docTypeAnggota;
    protected $docTypePasanganASN;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->normalUser = User::factory()->create(['role' => 'anggota']);

        $this->personel = Personel::create([
            'user_id' => $this->normalUser->id,
            'nama' => 'Test Anggota',
            'jenis_kelamin' => 'Pria',
            'status_pernikahan' => 'Belum Menikah',
            'nrp_nip' => '123456',
            'pangkat_golongan' => 'Serda',
            'jabatan' => 'Bintara',
            'satuan_bagian' => 'Bagian Umum',
        ]);

        $this->docTypeAnggota = MarriageDocumentType::create([
            'code' => 'SURAT_KETERANGAN_BELUM_MENIKAH',
            'name' => 'Surat Keterangan Belum Menikah',
            'category' => 'PERSYARATAN',
            'owner_type' => 'ANGGOTA',
            'is_active' => true,
        ]);

        $this->docTypePasanganASN = MarriageDocumentType::create([
            'code' => 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN',
            'name' => 'Surat Keterangan Dinas',
            'category' => 'PERSYARATAN',
            'owner_type' => 'PASANGAN',
            'is_active' => true,
        ]);
        
        $this->application = MarriageApplication::create([
            'user_id' => $this->normalUser->id,
            'personel_id' => $this->personel->id,
            'jenis_kelamin_anggota' => 'Pria',
            'peran_anggota' => 'suami',
            'tanggal_pengajuan' => now(),
            'tanggal_rencana_nikah' => now()->addDays(30),
            'tempat_nikah' => 'Jakarta',
            'alamat_nikah' => 'Jl. Test',
            'kelurahan_nikah' => 'Kel',
            'kecamatan_nikah' => 'Kec',
            'kabupaten_nikah' => 'Kab',
            'provinsi_nikah' => 'Prov',
            'alamat_domisili' => 'Jl. Test',
            'kelurahan_domisili' => 'Kel',
            'kecamatan_domisili' => 'Kec',
            'kabupaten_domisili' => 'Kab',
            'provinsi_domisili' => 'Prov',
            'kua_tujuan' => 'KUA Test',
            'status' => MarriageApplication::STATUS_DIAJUKAN,
        ]);

        $this->partner = MarriagePartner::create([
            'marriage_application_id' => $this->application->id,
            'peran' => 'istri',
            'nama' => 'Test Pasangan',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => now()->subYears(20),
            'pekerjaan' => 'PNS',
            'status_pekerjaan' => 'ASN',
            'agama' => 'Islam',
            'suku' => 'Jawa',
            'alamat' => 'Jl. Test',
            'kelurahan' => 'Kel',
            'kecamatan' => 'Kec',
            'kabupaten' => 'Kab',
            'provinsi' => 'Prov',
            'bapak_nama' => 'Bapak',
            'bapak_agama' => 'Islam',
            'bapak_pekerjaan' => 'PNS',
            'bapak_alamat' => 'Jl. Test',
            'ibu_nama' => 'Ibu',
            'ibu_agama' => 'Islam',
            'ibu_pekerjaan' => 'Ibu RT',
            'ibu_alamat' => 'Jl. Test',
        ]);
    }

    public function test_admin_can_approve_pengajuan_awal()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.marriage_applications.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI,
        ]);

        $response->assertRedirect();
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_PENGAJUAN_DISETUJUI, $this->application->status);
        $this->assertDatabaseHas('marriage_status_histories', [
            'marriage_application_id' => $this->application->id,
            'status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI,
        ]);
    }

    public function test_admin_can_reject_pengajuan_awal_with_reason()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.marriage_applications.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_DITOLAK,
            'catatan' => 'Dokumen buram',
        ]);

        $response->assertRedirect();
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DITOLAK, $this->application->status);
        $this->assertEquals('Dokumen buram', $this->application->catatan_admin);
    }

    public function test_admin_reject_pengajuan_awal_without_reason_fails()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.marriage_applications.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_DITOLAK,
            'catatan' => '',
        ]);

        $response->assertSessionHas('error');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DIAJUKAN, $this->application->status);
    }

    public function test_user_can_resubmit_after_ditolak()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DITOLAK]);

        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.submit', $this->application->id));

        $response->assertRedirect();
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DIAJUKAN, $this->application->status);
    }

    public function test_letter_generation_blocked_before_pengajuan_disetujui()
    {
        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.generate_letter', $this->application->id), [
            'type_code' => 'SURAT_KETERANGAN_BELUM_MENIKAH'
        ]);
        
        $response->assertSessionHas('error');
    }

    public function test_letter_generation_allowed_after_pengajuan_disetujui()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI]);
        
        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.generate_letter', $this->application->id), [
            'type_code' => 'SURAT_KETERANGAN_BELUM_MENIKAH'
        ]);
        
        $response->assertSessionMissing('Surat pengantar hanya dapat digenerate setelah pengajuan awal disetujui Admin.');
    }

    public function test_admin_cannot_verify_if_documents_missing()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.marriage_applications.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_DIVERIFIKASI,
        ]);

        $response->assertSessionHas('error');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_PENGAJUAN_DISETUJUI, $this->application->status);
    }

    public function test_admin_can_verify_if_documents_complete()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI]);

        MarriageDocument::create([
            'marriage_application_id' => $this->application->id,
            'marriage_document_type_id' => $this->docTypeAnggota->id,
            'pihak' => 'Anggota',
            'jenis_dokumen' => $this->docTypeAnggota->name,
            'nama_dokumen' => $this->docTypeAnggota->name,
            'file_path' => 'test.pdf',
            'file_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'DITERIMA'
        ]);

        MarriageDocument::create([
            'marriage_application_id' => $this->application->id,
            'marriage_document_type_id' => $this->docTypePasanganASN->id,
            'pihak' => 'Pasangan',
            'jenis_dokumen' => $this->docTypePasanganASN->name,
            'nama_dokumen' => $this->docTypePasanganASN->name,
            'file_path' => 'test2.pdf',
            'file_name' => 'test2.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'DITERIMA'
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.marriage_applications.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_DIVERIFIKASI,
        ]);

        $response->assertSessionHas('success');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DIVERIFIKASI, $this->application->status);
    }

    public function test_final_approval_changes_personel_status_transactionally()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DIVERIFIKASI]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.marriage_applications.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_DISETUJUI,
        ]);

        $response->assertSessionHas('success');
        $this->application->refresh();
        $this->personel->refresh();
        
        $this->assertEquals(MarriageApplication::STATUS_DISETUJUI, $this->application->status);
        $this->assertEquals('Menikah', $this->personel->status_pernikahan);
    }

    public function test_menikah_personel_cannot_create_application()
    {
        $this->personel->update(['status_pernikahan' => 'Menikah']);

        $response = $this->actingAs($this->normalUser)->get(route('user.pengajuan_nikah.create'));
        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error');
    }
}
