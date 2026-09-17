<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Personel;
use App\Models\MarriageApplication;
use App\Models\MarriageDocumentType;
use App\Models\MarriageLetter;

class MarriageLetterGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed document types if not present
        if (MarriageDocumentType::count() === 0) {
            $this->artisan('db:seed', ['--class' => 'MarriageDocumentTypeSeeder']);
        }
        
        Storage::fake('private');
        
        // We shouldn't fake the disk where the templates are, but we fake 'private' where letters are generated.
        // We will rely on the real dummy templates that already exist in storage/app/templates/marriage/
    }

    public function test_user_without_personel_is_blocked()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_personel_without_gender_is_blocked()
    {
        $user = User::factory()->create(['role' => 'user']);
        Personel::create([
            'user_id' => $user->id,
            'jenis_personel' => 'militer',
            'kategori_personel' => 'Bintara',
            'nrp_nip' => '12345',
            'nama' => 'Test',
            'pangkat_golongan' => 'Serda',
            'jabatan' => 'Test Jab',
            'satuan_bagian' => 'Test Sat',
            'status_aktif' => true,
            'jenis_kelamin' => null
        ]);

        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_pria_becomes_suami()
    {
        $user = User::factory()->create(['role' => 'user']);
        Personel::create([
            'user_id' => $user->id,
            'jenis_personel' => 'militer',
            'kategori_personel' => 'Bintara',
            'nrp_nip' => '12345',
            'nama' => 'Test',
            'pangkat_golongan' => 'Serda',
            'jabatan' => 'Test Jab',
            'satuan_bagian' => 'Test Sat',
            'status_aktif' => true,
            'jenis_kelamin' => 'Pria'
        ]);

        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertOk();
        $response->assertSee('calon <strong style="color: var(--primary);">suami</strong>', false);
        $response->assertSee('Data istri'); // from role resolving
    }

    public function test_wanita_becomes_istri()
    {
        $user = User::factory()->create(['role' => 'user']);
        Personel::create([
            'user_id' => $user->id,
            'jenis_personel' => 'militer',
            'kategori_personel' => 'Bintara',
            'nrp_nip' => '12345',
            'nama' => 'Test',
            'pangkat_golongan' => 'Serda',
            'jabatan' => 'Test Jab',
            'satuan_bagian' => 'Test Sat',
            'status_aktif' => true,
            'jenis_kelamin' => 'Wanita'
        ]);

        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertOk();
        $response->assertSee('calon <strong style="color: var(--primary);">istri</strong>', false);
        $response->assertSee('Data suami');
    }

    public function test_generate_five_letters_and_download()
    {
        $user = User::factory()->create(['role' => 'user']);
        $personel = Personel::create([
            'user_id' => $user->id,
            'jenis_personel' => 'militer',
            'kategori_personel' => 'Bintara',
            'nrp_nip' => '12345',
            'nama' => 'Test',
            'pangkat_golongan' => 'Serda',
            'jabatan' => 'Test Jab',
            'satuan_bagian' => 'Test Sat',
            'status_aktif' => true,
            'jenis_kelamin' => 'Pria'
        ]);

        // Create application
        $application = MarriageApplication::create([
            'user_id' => $user->id,
            'personel_id' => $personel->id,
            'jenis_kelamin_anggota' => 'Pria',
            'peran_anggota' => 'suami',
            'tanggal_pengajuan' => now(),
            'tanggal_rencana_nikah' => now()->addMonth(),
            'tempat_nikah' => 'KUA',
            'alamat_nikah' => 'Alamat KUA',
            'kelurahan_nikah' => '-',
            'kecamatan_nikah' => '-',
            'kabupaten_nikah' => '-',
            'provinsi_nikah' => '-',
            'alamat_domisili' => 'Domisili 1',
            'kelurahan_domisili' => '-',
            'kecamatan_domisili' => '-',
            'kabupaten_domisili' => '-',
            'provinsi_domisili' => '-',
            'kua_tujuan' => '-',
            'status' => 'DRAFT'
        ]);

        $codes = ['PENGANTAR_NA', 'PENGANTAR_KESDAM', 'PENGANTAR_BINTALDAM', 'PENGANTAR_LITPERS', 'PENGANTAR_SKBD'];
        
        foreach ($codes as $code) {
            $this->assertDatabaseHas('marriage_document_types', ['code' => $code]);

            $response = $this->actingAs($user)->post(route('user.pengajuan_nikah.generate_letter', $application->id), [
                'type_code' => $code
            ]);
            
            $response->assertRedirect();
            $response->assertSessionHas('success');
            
            $this->assertDatabaseHas('marriage_letters', [
                'marriage_application_id' => $application->id,
                'jenis_surat' => $code,
                'status' => 'TERSEDIA'
            ]);
            
            $letter = MarriageLetter::where('jenis_surat', $code)->first();
            Storage::disk('private')->assertExists($letter->file_generated);

            // Download test
            $downloadResp = $this->actingAs($user)->get(route('user.pengajuan_nikah.download_letter', [$application->id, $letter->id]));
            $downloadResp->assertOk();
            
            // Generate again to ensure no duplicate
            $this->actingAs($user)->post(route('user.pengajuan_nikah.generate_letter', $application->id), [
                'type_code' => $code
            ]);
            
            $this->assertEquals(1, MarriageLetter::where('jenis_surat', $code)->count());
        }
    }

    public function test_authorization_application_ownership()
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $personel1 = Personel::create([
            'user_id' => $user1->id,
            'jenis_personel' => 'militer',
            'kategori_personel' => 'Bintara',
            'nrp_nip' => '12345',
            'nama' => 'Test',
            'pangkat_golongan' => 'Serda',
            'jabatan' => 'Test Jab',
            'satuan_bagian' => 'Test Sat',
            'status_aktif' => true,
            'jenis_kelamin' => 'Pria'
        ]);

        $user2 = User::factory()->create(['role' => 'user']);

        $application = MarriageApplication::create([
            'user_id' => $user1->id,
            'personel_id' => $personel1->id,
            'jenis_kelamin_anggota' => 'Pria',
            'peran_anggota' => 'suami',
            'tanggal_pengajuan' => now(),
            'tanggal_rencana_nikah' => now()->addMonth(),
            'tempat_nikah' => '-',
            'alamat_nikah' => '-',
            'kelurahan_nikah' => '-',
            'kecamatan_nikah' => '-',
            'kabupaten_nikah' => '-',
            'provinsi_nikah' => '-',
            'status' => 'DRAFT'
        ]);

        // User2 tries to access user1's application
        $response = $this->actingAs($user2)->get(route('user.pengajuan_nikah.show', $application->id));
        $response->assertStatus(404);
        
        $response = $this->actingAs($user2)->post(route('user.pengajuan_nikah.generate_letter', $application->id), [
            'type_code' => 'PENGANTAR_NA'
        ]);
        $response->assertStatus(404);
    }
}
