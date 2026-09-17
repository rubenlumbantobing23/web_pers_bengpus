<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Personel;
use App\Models\MarriageApplication;
use App\Models\MarriagePartner;
use App\Models\MarriageDocumentType;
use App\Models\MarriageDocument;

class AdminMarriageVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MarriageDocumentTypeSeeder::class);
        Storage::fake('private');
    }

    private function createDummyApplication(User $user, $status = 'DIAJUKAN', $isASN = false)
    {
        $personel = Personel::create([
            'user_id' => $user->id,
            'nama' => 'Test Personel',
            'nrp_nip' => '1234567890' . $user->id,
            'pangkat_golongan' => 'Letda',
            'jabatan' => 'Pama',
            'satuan_bagian' => 'Bengpus',
            'jenis_kelamin' => 'Pria'
        ]);

        $app = MarriageApplication::create([
            'user_id'              => $user->id,
            'personel_id'          => $personel->id,
            'jenis_kelamin_anggota'=> 'Pria',
            'peran_anggota'        => 'suami',
            'tanggal_pengajuan'    => now(),
            'tanggal_rencana_nikah'=> now()->addMonth(),
            'tempat_nikah'         => '-',
            'alamat_nikah'         => '-',
            'kelurahan_nikah'      => '-',
            'kecamatan_nikah'      => '-',
            'kabupaten_nikah'      => '-',
            'provinsi_nikah'       => '-',
            'alamat_domisili'      => '-',
            'kelurahan_domisili'   => '-',
            'kecamatan_domisili'   => '-',
            'kabupaten_domisili'   => '-',
            'provinsi_domisili'    => '-',
            'kua_tujuan'           => '-',
            'status'               => $status,
        ]);

        MarriagePartner::create([
            'marriage_application_id' => $app->id,
            'peran'            => 'istri',
            'nama'             => 'Test Partner',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '2000-01-01',
            'pekerjaan'        => 'Pegawai',
            'status_pekerjaan' => $isASN ? 'ASN' : 'Non-ASN',
            'agama'            => 'Islam',
            'suku'             => 'Jawa',
            'alamat'           => '-',
            'kelurahan'        => '-',
            'kecamatan'        => '-',
            'kabupaten'        => '-',
            'provinsi'         => '-',
            'bapak_nama'       => '-',
            'bapak_agama'      => '-',
            'bapak_pekerjaan'  => '-',
            'bapak_alamat'     => '-',
            'ibu_nama'         => '-',
            'ibu_agama'        => '-',
            'ibu_pekerjaan'    => '-',
            'ibu_alamat'       => '-',
        ]);

        return $app;
    }

    private function createDocument($application, $code, $status = 'BELUM_DIPERIKSA')
    {
        $dt = MarriageDocumentType::where('code', $code)->first();
        return MarriageDocument::create([
            'marriage_application_id'   => $application->id,
            'marriage_document_type_id' => $dt->id,
            'pihak'                     => in_array($dt->owner_type, ['ANGGOTA', 'ORANG_TUA_ANGGOTA']) ? 'Anggota' : 'Pasangan',
            'jenis_dokumen'             => $dt->code,
            'nama_dokumen'              => $dt->name,
            'file_path'                 => 'dummy.pdf',
            'file_name'                 => 'dummy.pdf',
            'mime_type'                 => 'application/pdf',
            'file_size'                 => 100,
            'status_verifikasi'         => $status,
            'uploaded_at'               => now(),
        ]);
    }

    // 1. Admin dapat melihat application.
    public function test_admin_can_view_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);

        $response = $this->actingAs($admin)->get(route('admin.admin.pengajuan_nikah.show', $app->id));
        $response->assertStatus(200);
        $response->assertSee('Data Anggota');
    }

    // 2. Admin dapat melihat requirement yang BELUM ADA.
    public function test_admin_sees_belum_ada_placeholder_for_missing_documents()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);

        $response = $this->actingAs($admin)->get(route('admin.admin.pengajuan_nikah.show', $app->id));
        $response->assertSee('BELUM ADA');
    }

    // 3. Admin dapat melihat dokumen yang sudah upload.
    public function test_admin_can_see_uploaded_documents()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);
        $this->createDocument($app, 'KK_CALON_PASANGAN');

        $response = $this->actingAs($admin)->get(route('admin.admin.pengajuan_nikah.show', $app->id));
        $response->assertSee('KK_CALON_PASANGAN');
        $response->assertSee('BELUM DIPERIKSA');
    }

    // 4. Admin dapat menerima dokumen.
    public function test_admin_can_accept_document()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);
        $doc = $this->createDocument($app, 'KK_CALON_PASANGAN');

        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.verify_document', $app->id), [
            'document_id' => $doc->id,
            'status' => 'DITERIMA'
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('DITERIMA', $doc->fresh()->status_verifikasi);
    }

    // 5. Admin dapat menolak dokumen.
    // 6. Penolakan menyimpan catatan.
    // 7. Penolakan mengubah application menjadi PERLU_PERBAIKAN.
    public function test_admin_can_reject_document_and_it_changes_application_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user, 'DIAJUKAN');
        $doc = $this->createDocument($app, 'KK_CALON_PASANGAN');

        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.verify_document', $app->id), [
            'document_id' => $doc->id,
            'status' => 'DITOLAK',
            'catatan' => 'Buram'
        ]);

        $doc->refresh();
        $app->refresh();

        $this->assertEquals('DITOLAK', $doc->status_verifikasi);
        $this->assertEquals('Buram', $doc->catatan_verifikasi);
        $this->assertEquals('PERLU_PERBAIKAN', $app->status);
    }

    // 8. User tidak dapat melakukan verify.
    public function test_user_cannot_verify_document()
    {
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);
        $doc = $this->createDocument($app, 'KK_CALON_PASANGAN');

        $response = $this->actingAs($user)->post(route('admin.admin.pengajuan_nikah.verify_document', $app->id), [
            'document_id' => $doc->id,
            'status' => 'DITERIMA'
        ]);

        $response->assertRedirect(); // RoleMiddleware redirects to dashboard or login
    }

    // 9. Admin tidak dapat verify document application lain.
    public function test_admin_cannot_verify_document_from_different_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $app1 = $this->createDummyApplication($user1);
        $app2 = $this->createDummyApplication($user2);
        
        $docFromApp2 = $this->createDocument($app2, 'KK_CALON_PASANGAN');

        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.verify_document', $app1->id), [
            'document_id' => $docFromApp2->id,
            'status' => 'DITERIMA'
        ]);

        $response->assertStatus(404);
    }

    // 10. Application tidak dapat menjadi DIVERIFIKASI jika masih ada requirement BELUM ADA.
    public function test_cannot_set_diverifikasi_if_documents_are_missing()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);

        // Intentionally NOT creating all required documents
        
        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.update_status', $app->id), [
            'status' => 'DIVERIFIKASI'
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('BELUM ADA', session('error'));
        $this->assertNotEquals('DIVERIFIKASI', $app->fresh()->status);
    }

    // 11. Application tidak dapat menjadi DIVERIFIKASI jika masih ada document BELUM_DIPERIKSA.
    public function test_cannot_set_diverifikasi_if_documents_are_unverified()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);

        // Create all required documents but set them to BELUM_DIPERIKSA
        $reqs = MarriageDocumentType::whereNotIn('category', ['SURAT_SATUAN', 'SURAT_FINAL'])->where('is_active', true)->get();
        foreach ($reqs as $r) {
            if ($r->code === 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN') continue;
            $this->createDocument($app, $r->code, 'BELUM_DIPERIKSA');
        }

        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.update_status', $app->id), [
            'status' => 'DIVERIFIKASI'
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('BELUM_DIPERIKSA', session('error'));
    }

    // 12. Application tidak dapat menjadi DIVERIFIKASI jika ada DITOLAK.
    public function test_cannot_set_diverifikasi_if_document_is_rejected()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user);

        $reqs = MarriageDocumentType::whereNotIn('category', ['SURAT_SATUAN', 'SURAT_FINAL'])->where('is_active', true)->get();
        foreach ($reqs as $r) {
            if ($r->code === 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN') continue;
            // set all to accepted except the first one
            $status = $r->code === 'KTP_CALON_PASANGAN' ? 'DITOLAK' : 'DITERIMA';
            $this->createDocument($app, $r->code, $status);
        }

        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.update_status', $app->id), [
            'status' => 'DIVERIFIKASI'
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('DITOLAK', session('error'));
    }

    // 13. Application dapat menjadi DIVERIFIKASI jika seluruh requirement wajib sudah DITERIMA.
    // 15. Conditional non-ASN tidak dianggap wajib.
    // 16. Generated cover letters tidak dihitung sebagai document upload.
    public function test_can_set_diverifikasi_if_all_required_documents_accepted()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        // non-ASN partner
        $app = $this->createDummyApplication($user, 'DIAJUKAN', false);

        $reqs = MarriageDocumentType::whereNotIn('category', ['SURAT_SATUAN', 'SURAT_FINAL'])->where('is_active', true)->get();
        foreach ($reqs as $r) {
            if ($r->code === 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN') continue; // Should not be required
            $this->createDocument($app, $r->code, 'DITERIMA');
        }

        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.update_status', $app->id), [
            'status' => 'DIVERIFIKASI'
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('DIVERIFIKASI', $app->fresh()->status);
    }

    // 14. Conditional ASN diperhitungkan.
    public function test_cannot_set_diverifikasi_if_asn_document_missing_for_asn_partner()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        // ASN partner
        $app = $this->createDummyApplication($user, 'DIAJUKAN', true);

        $reqs = MarriageDocumentType::whereNotIn('category', ['SURAT_SATUAN', 'SURAT_FINAL'])->where('is_active', true)->get();
        foreach ($reqs as $r) {
            if ($r->code === 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN') continue; // Intentionally skipped
            $this->createDocument($app, $r->code, 'DITERIMA');
        }

        $response = $this->actingAs($admin)->post(route('admin.admin.pengajuan_nikah.update_status', $app->id), [
            'status' => 'DIVERIFIKASI'
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('BELUM ADA', session('error'));
    }

    // 17. Re-upload dokumen tidak mengubah: PERLU_PERBAIKAN -> DIAJUKAN
    // 18. Dokumen hasil re-upload dapat diperiksa kembali Admin.
    public function test_reupload_resets_document_status_but_keeps_application_perlu_perbaikan()
    {
        $user = User::factory()->create(['role' => 'user']);
        $app = $this->createDummyApplication($user, 'PERLU_PERBAIKAN');
        
        $docType = MarriageDocumentType::where('code', 'KK_CALON_PASANGAN')->first();
        $doc = $this->createDocument($app, $docType->code, 'DITOLAK');
        $doc->update(['catatan_verifikasi' => 'Buram']);

        $file = UploadedFile::fake()->create('document_baru.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('user.pengajuan_nikah.upload_document', $app->id), [
            'pihak' => 'Pasangan',
            'marriage_document_type_id' => $docType->id,
            'jenis_dokumen' => $docType->code,
            'file' => $file
        ]);

        $doc->refresh();
        $app->refresh();

        $this->assertEquals('PERLU_PERBAIKAN', $app->status);
        $this->assertEquals('BELUM_DIPERIKSA', $doc->status_verifikasi);
        $this->assertNull($doc->catatan_verifikasi);
    }
}
