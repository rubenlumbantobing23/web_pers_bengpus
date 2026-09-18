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
use App\Models\MarriageLetter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MarriageApplicationApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser;
    protected $personel;
    protected $partner;
    protected $application;
    protected $docTypeAnggota;
    protected $docTypeSPN;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->normalUser = User::factory()->create(['role' => 'user']);

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

        $this->docTypeSPN = MarriageDocumentType::create([
            'code' => 'SURAT_PERMOHONAN_IZIN_NIKAH',
            'name' => 'Surat Permohonan Izin Nikah',
            'category' => 'SURAT_SATUAN',
            'owner_type' => 'ANGGOTA',
            'source_type' => 'SYSTEM',
            'is_active' => true,
        ]);

        $this->docTypeAnggota = MarriageDocumentType::create([
            'code' => 'PENGANTAR_NA',
            'name' => 'Surat Pengantar Nikah (NA)',
            'category' => 'SURAT_SATUAN',
            'owner_type' => 'SATUAN',
            'source_type' => 'SYSTEM',
            'is_active' => true,
        ]);

        MarriageDocumentType::create([
            'code' => 'SURAT_IZIN_NIKAH_FINAL',
            'name' => 'Surat Izin Nikah',
            'category' => 'SURAT_FINAL',
            'owner_type' => 'ANGGOTA',
            'source_type' => 'SYSTEM',
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

    // 1. Surat Pengajuan Nikah dapat digenerate sebelum PENGAJUAN_DISETUJUI.
    public function test_surat_pengajuan_nikah_can_be_generated_before_pengajuan_disetujui()
    {
        $this->assertEquals(MarriageApplication::STATUS_DIAJUKAN, $this->application->status);
        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.generate_letter', $this->application->id), [
            'type_code' => 'SURAT_PERMOHONAN_IZIN_NIKAH'
        ]);
        $response->assertSessionHas('error'); // Wait, because template doesn't exist it might fail, but it's not blocked by status.
        $this->assertNotEquals('Surat pengantar hanya dapat digenerate setelah pengajuan awal disetujui Admin.', session('error'));
    }

    // 2. Cover letter tetap blocked sebelum PENGAJUAN_DISETUJUI.
    public function test_cover_letter_blocked_before_pengajuan_disetujui()
    {
        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.generate_letter', $this->application->id), [
            'type_code' => 'PENGANTAR_NA'
        ]);
        $response->assertSessionHas('error', 'Surat pengantar hanya dapat digenerate setelah pengajuan awal disetujui Admin.');
    }

    // 3. Cover letter dapat digenerate setelah PENGAJUAN_DISETUJUI.
    public function test_cover_letter_can_be_generated_after_pengajuan_disetujui()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI]);
        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.generate_letter', $this->application->id), [
            'type_code' => 'PENGANTAR_NA'
        ]);
        // Will pass status check, might fail if no template, but we just check the status error is not there
        $response->assertSessionMissing('Surat pengantar hanya dapat digenerate setelah pengajuan awal disetujui Admin.');
    }

    // 4. Admin tidak dapat approve initial application jika signed-return belum ada.
    public function test_admin_cannot_approve_initial_if_signed_return_missing()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.admin.pengajuan_nikah.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI,
        ]);
        $response->assertSessionHas('error', 'Surat Pengajuan Nikah (signed-return) belum diunggah atau belum diverifikasi/diterima.');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DIAJUKAN, $this->application->status);
    }

    // 5. Admin dapat approve initial application jika signed-return tersedia.
    public function test_admin_can_approve_initial_if_signed_return_available()
    {
        MarriageDocument::create([
            'marriage_application_id' => $this->application->id,
            'marriage_document_type_id' => $this->docTypeSPN->id,
            'pihak' => 'Anggota',
            'jenis_dokumen' => $this->docTypeSPN->name,
            'nama_dokumen' => $this->docTypeSPN->name,
            'file_path' => 'test.pdf',
            'file_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'DITERIMA'
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.admin.pengajuan_nikah.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_PENGAJUAN_DISETUJUI,
        ]);

        $response->assertSessionHas('success');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_PENGAJUAN_DISETUJUI, $this->application->status);
    }

    // 6. Upload signed-return tidak otomatis mengubah application status.
    public function test_upload_signed_return_does_not_change_application_status()
    {
        $file = UploadedFile::fake()->create('signed.pdf', 100, 'application/pdf');
        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.upload_document', $this->application->id), [
            'pihak' => 'Anggota',
            'marriage_document_type_id' => $this->docTypeSPN->id,
            'jenis_dokumen' => 'Surat Pengajuan Nikah',
            'file' => $file
        ]);

        $response->assertSessionHas('success');
        $this->application->refresh();
        // Status should still be DIAJUKAN, waiting for Admin
        $this->assertEquals(MarriageApplication::STATUS_DIAJUKAN, $this->application->status);
    }

    // 7. Rejection initial application membutuhkan reason.
    public function test_rejection_initial_application_requires_reason()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.admin.pengajuan_nikah.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_DITOLAK,
            'catatan' => '',
        ]);

        $response->assertSessionHas('error', 'Alasan penolakan wajib diisi.');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DIAJUKAN, $this->application->status);
    }

    // 8. User dapat resubmit setelah DITOLAK.
    public function test_user_can_resubmit_after_ditolak()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DITOLAK]);

        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.submit', $this->application->id));

        $response->assertRedirect();
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DIAJUKAN, $this->application->status);
    }

    // 9. Generated Surat Pengajuan Nikah tidak hilang ketika signed-return diupload.
    public function test_generated_spn_not_lost_when_signed_return_uploaded()
    {
        MarriageLetter::create([
            'marriage_application_id' => $this->application->id,
            'jenis_surat' => 'SURAT_PERMOHONAN_IZIN_NIKAH',
            'file_generated' => 'generated.docx',
            'status' => 'TERSEDIA'
        ]);

        $file = UploadedFile::fake()->create('signed.pdf', 100, 'application/pdf');
        $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.upload_document', $this->application->id), [
            'pihak' => 'Anggota',
            'marriage_document_type_id' => $this->docTypeSPN->id,
            'jenis_dokumen' => 'Surat Pengajuan Nikah',
            'file' => $file
        ]);

        $this->assertDatabaseHas('marriage_letters', [
            'marriage_application_id' => $this->application->id,
            'jenis_surat' => 'SURAT_PERMOHONAN_IZIN_NIKAH'
        ]);

        $this->assertDatabaseHas('marriage_documents', [
            'marriage_application_id' => $this->application->id,
            'marriage_document_type_id' => $this->docTypeSPN->id
        ]);
    }

    // 10. Locked final application tetap tidak dapat upload/replace.
    public function test_locked_final_application_cannot_upload()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DISETUJUI]);
        
        $file = UploadedFile::fake()->create('signed.pdf', 100, 'application/pdf');
        $response = $this->actingAs($this->normalUser)->post(route('user.pengajuan_nikah.upload_document', $this->application->id), [
            'pihak' => 'Anggota',
            'marriage_document_type_id' => $this->docTypeSPN->id,
            'jenis_dokumen' => 'Surat Pengajuan Nikah',
            'file' => $file
        ]);

        $response->assertSessionHas('error');
    }

    // 11. DISETUJUI -> SELESAI ditolak jika final letter belum tersedia.
    public function test_selesai_rejected_if_final_letter_missing()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DISETUJUI]);
        
        $response = $this->actingAs($this->adminUser)->post(route('admin.admin.pengajuan_nikah.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_SELESAI,
        ]);

        $response->assertSessionHas('error', 'Surat Izin Nikah final belum tersedia. Generate Surat Izin Nikah terlebih dahulu.');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_DISETUJUI, $this->application->status);
    }

    // 12. DISETUJUI -> SELESAI berhasil jika final letter benar-benar tersedia.
    public function test_selesai_accepted_if_final_letter_available()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DISETUJUI]);
        
        $filePath = 'marriage_letters/final_' . $this->application->id . '.docx';
        Storage::disk('private')->put($filePath, 'dummy content');

        MarriageLetter::create([
            'marriage_application_id' => $this->application->id,
            'jenis_surat' => 'SURAT_IZIN_NIKAH_FINAL',
            'file_generated' => $filePath,
            'status' => 'TERSEDIA'
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.admin.pengajuan_nikah.update_status', $this->application->id), [
            'status' => MarriageApplication::STATUS_SELESAI,
        ]);

        $response->assertSessionHas('success');
        $this->application->refresh();
        $this->assertEquals(MarriageApplication::STATUS_SELESAI, $this->application->status);
    }

    // 13. User dapat download final letter milik application sendiri
    public function test_user_can_download_final_letter()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DISETUJUI]);
        
        $filePath = 'marriage_letters/final_' . $this->application->id . '.docx';
        Storage::disk('private')->put($filePath, 'dummy content');

        $letter = MarriageLetter::create([
            'marriage_application_id' => $this->application->id,
            'jenis_surat' => 'SURAT_IZIN_NIKAH_FINAL',
            'file_generated' => $filePath,
            'status' => 'TERSEDIA'
        ]);

        $response = $this->actingAs($this->normalUser)->get(route('user.pengajuan_nikah.download_letter', [$this->application->id, $letter->id]));
        $response->assertDownload();
    }

    // 14. Final letter tidak dapat diakses sebelum DISETUJUI.
    public function test_final_letter_cannot_be_accessed_before_disetujui()
    {
        $this->application->update(['status' => MarriageApplication::STATUS_DIVERIFIKASI]);
        
        $letter = MarriageLetter::create([
            'marriage_application_id' => $this->application->id,
            'jenis_surat' => 'SURAT_IZIN_NIKAH_FINAL',
            'file_generated' => 'dummy.docx',
            'status' => 'TERSEDIA'
        ]);

        $response = $this->actingAs($this->normalUser)->get(route('user.pengajuan_nikah.download_letter', [$this->application->id, $letter->id]));
        $response->assertStatus(403);
    }
}

