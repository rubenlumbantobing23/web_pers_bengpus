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
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MarriageRequirementUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'MarriageDocumentTypeSeeder']);
        Storage::fake('private');
    }

    public function test_user_can_upload_and_replace_document()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $personel = Personel::create([
            'user_id' => $user->id,
            'nama' => 'Test Personel',
            'nrp_nip' => '1234567890',
            'pangkat_golongan' => 'Letda',
            'jabatan' => 'Pama',
            'satuan_bagian' => 'Bengpus',
            'jenis_kelamin' => 'Pria'
        ]);
        
        $application = MarriageApplication::create([
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
            'status'               => 'DRAFT',
        ]);

        $docType = MarriageDocumentType::where('code', 'KK_CALON_PASANGAN')->first();

        // 1. Initial Upload
        $file1 = UploadedFile::fake()->create('document1.pdf', 100, 'application/pdf');
        
        $response1 = $this->actingAs($user)->post(route('user.pengajuan_nikah.upload_document', $application->id), [
            'pihak' => 'Pasangan',
            'marriage_document_type_id' => $docType->id,
            'jenis_dokumen' => $docType->code,
            'file' => $file1
        ]);
        
        if (!$response1->getSession()->has('success')) {
            $response1->dumpSession();
        }
        $response1->assertSessionHas('success');
        
        $this->assertDatabaseHas('marriage_documents', [
            'marriage_application_id' => $application->id,
            'marriage_document_type_id' => $docType->id,
            'status_verifikasi' => 'BELUM_DIPERIKSA',
        ]);
        
        $doc = $application->documents()->where('marriage_document_type_id', $docType->id)->first();
        Storage::disk('private')->assertExists($doc->file_path);
        
        $oldPath = $doc->file_path;

        // 2. Replace Upload
        $this->travel(2)->seconds();
        $file2 = UploadedFile::fake()->create('document2.pdf', 200, 'application/pdf');
        
        $response2 = $this->actingAs($user)->post(route('user.pengajuan_nikah.upload_document', $application->id), [
            'pihak' => 'Pasangan',
            'marriage_document_type_id' => $docType->id,
            'jenis_dokumen' => $docType->code,
            'file' => $file2
        ]);
        
        $response2->assertSessionHas('success');
        
        // Assert only 1 document record still exists for this type
        $this->assertEquals(1, $application->documents()->where('marriage_document_type_id', $docType->id)->count());
        
        $doc->refresh();
        Storage::disk('private')->assertExists($doc->file_path);
        
        // Assert old file deleted
        if (Storage::disk('private')->exists($oldPath)) {
            dump("OLD PATH STILL EXISTS: ", $oldPath);
            dump("NEW PATH: ", $doc->file_path);
        }
        Storage::disk('private')->assertMissing($oldPath);
        $this->assertNotEquals($oldPath, $doc->file_path);
    }
    
    public function test_user_cannot_access_others_document()
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        
        $personel = Personel::create([
            'user_id' => $user1->id,
            'nama' => 'Test Personel',
            'nrp_nip' => '1234567890',
            'pangkat_golongan' => 'Letda',
            'jabatan' => 'Pama',
            'satuan_bagian' => 'Bengpus',
            'jenis_kelamin' => 'Pria'
        ]);
        
        $application = MarriageApplication::create([
            'user_id'              => $user1->id,
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
            'status'               => 'DRAFT',
        ]);
        
        $docType = MarriageDocumentType::where('code', 'KK_CALON_PASANGAN')->first();
        $file = UploadedFile::fake()->create('document1.pdf', 100, 'application/pdf');
        
        $this->actingAs($user1)->post(route('user.pengajuan_nikah.upload_document', $application->id), [
            'pihak' => 'Pasangan',
            'marriage_document_type_id' => $docType->id,
            'jenis_dokumen' => $docType->code,
            'file' => $file
        ]);
        
        $doc = $application->documents()->first();
        
        // User 2 tries to download User 1's doc
        $response = $this->actingAs($user2)->get(route('user.pengajuan_nikah.download_document', [$application->id, $doc->id]));
        $response->assertStatus(404);
        
        // User 1 can download
        $response = $this->actingAs($user1)->get(route('user.pengajuan_nikah.download_document', [$application->id, $doc->id]));
        $response->assertStatus(200);
    }
}
