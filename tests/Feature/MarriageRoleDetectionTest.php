<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Personel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MarriageRoleDetectionTest extends TestCase
{
    use RefreshDatabase;

    private function createUserAndPersonel($gender)
    {
        $user = User::factory()->create(['role' => 'user']);
        if ($gender !== false) {
            Personel::create([
                'user_id' => $user->id,
                'nrp_nip' => '123456789' . rand(10, 99),
                'nama' => 'Test User',
                'pangkat_golongan' => 'Sertu',
                'jabatan' => 'Staff',
                'satuan_bagian' => 'Bengpuskomlekad',
                'jenis_kelamin' => $gender,
            ]);
        }
        return $user;
    }

    public function test_user_with_personel_pria_can_start_application_as_suami()
    {
        $user = $this->createUserAndPersonel('Pria');
        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertStatus(200);
        $response->assertSee('calon <strong style="color: var(--primary);">suami</strong>', false);
        $response->assertSee('calon <strong style="color: var(--primary);">istri</strong>', false);
    }

    public function test_user_with_personel_wanita_can_start_application_as_istri()
    {
        $user = $this->createUserAndPersonel('Wanita');
        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertStatus(200);
        $response->assertSee('calon <strong style="color: var(--primary);">istri</strong>', false);
        $response->assertSee('calon <strong style="color: var(--primary);">suami</strong>', false);
    }

    public function test_user_without_personel_cannot_start_application()
    {
        $user = $this->createUserAndPersonel(false); // No personel
        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error', 'Akun Anda belum terhubung dengan data Personel. Silakan hubungi Admin Personalia.');
    }

    public function test_personel_with_null_gender_cannot_start_application()
    {
        $user = $this->createUserAndPersonel(null);
        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error', 'Data jenis kelamin pada profil personel Anda belum lengkap. Silakan hubungi Admin Personalia.');
    }

    public function test_personel_with_invalid_gender_cannot_start_application()
    {
        $user = $this->createUserAndPersonel('Laki-Laki'); // Invalid strict mapping
        $response = $this->actingAs($user)->get(route('user.pengajuan_nikah.create'));
        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error', 'Nilai jenis kelamin Personel tidak valid. Harap hubungi Admin Personalia.');
    }
}
