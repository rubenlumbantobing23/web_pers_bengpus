<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Personel;
use App\Models\OrganizationUnit;
use App\Models\OrganizationOfficialAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationStructureTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        if (!$this->admin) {
            $this->admin = User::factory()->create(['role' => 'admin']);
        }

        $this->regularUser = User::where('role', 'user')->first();
        if (!$this->regularUser) {
            $this->regularUser = User::factory()->create(['role' => 'user']);
        }
    }

    public function test_non_admin_is_redirected_from_organization_page()
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.organization.index'));
        $response->assertStatus(302);
    }

    public function test_admin_can_access_organization_page_and_render_view()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.organization.index'));
        $response->assertStatus(200);
        $response->assertSee('Konfigurasi Struktur & Pejabat');
        $response->assertSee('Struktur Organisasi');
        $response->assertSee('Pejabat Global');
        $response->assertSee('Riwayat Perubahan');
        $response->assertSee('btn-military');
    }

    public function test_admin_can_access_signers_page_and_render_view()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.signers.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Pejabat Penandatangan');
        $response->assertSee('Pengaturan Pejabat Aktif Markas');
    }

    public function test_admin_can_add_unit()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.organization.unit.store'), [
            'name' => 'UNIT TESTING BARU',
            'code' => 'UTB',
            'level' => 'unit',
            'sort_order' => 99,
        ]);

        $response->assertRedirect(route('admin.organization.index'));
        $this->assertDatabaseHas('organization_units', [
            'name' => 'UNIT TESTING BARU',
            'code' => 'UTB',
        ]);
    }

    public function test_admin_can_assign_official_and_it_records_history()
    {
        $unit = OrganizationUnit::create([
            'name' => 'BENGSISKOM TEST',
            'code' => 'BSK-T',
            'level' => 'unit',
            'is_active' => true,
        ]);

        $personel = Personel::create([
            'nama' => 'Budi Santoso',
            'nrp_nip' => '1234567890',
            'pangkat_golongan' => 'Mayor Cke',
            'korps' => 'Cke',
            'satuan_bagian' => 'Bengsiskom',
            'jabatan' => 'Kabid',
            'jenis_kelamin' => 'L',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.organization.assign'), [
            'organization_unit_id' => $unit->id,
            'personel_id' => $personel->id,
            'role' => 'kabagum',
        ]);

        $response->assertRedirect(route('admin.organization.index'));
        $this->assertDatabaseHas('organization_official_assignments', [
            'organization_unit_id' => $unit->id,
            'personel_id' => $personel->id,
            'role' => 'kabagum',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_assign_pasi_and_special_roles()
    {
        $unit = OrganizationUnit::create([
            'name' => 'SIPAM',
            'code' => 'SPM',
            'level' => 'subunit',
            'is_active' => true,
        ]);

        $personel = Personel::create([
            'nama' => 'Kapten Pam',
            'nrp_nip' => '99887766',
            'pangkat_golongan' => 'Kapten',
            'jabatan' => 'Pasipam',
            'jenis_personel' => 'militer',
            'status_aktif' => true,
        ]);

        $roles = [
            'pasipam', 'pasiops', 'pasipers', 'pasilog', 'pasirendal', 'pasituud', 'kagud', 'kaprim',
            'kabengsiskom', 'kabengsislek', 'kabengjaringan_tik', 'kabengintegrasi_power'
        ];

        foreach ($roles as $role) {
            $response = $this->actingAs($this->admin)->post(route('admin.organization.assign'), [
                'organization_unit_id' => $unit->id,
                'personel_id' => $personel->id,
                'role' => $role,
            ]);

            $response->assertRedirect(route('admin.organization.index'));
            $this->assertDatabaseHas('organization_official_assignments', [
                'organization_unit_id' => $unit->id,
                'personel_id' => $personel->id,
                'role' => $role,
                'is_active' => true,
            ]);
        }
    }

    public function test_only_perwira_are_listed_in_pejabat_dropdown()
    {
        $perwira = Personel::create([
            'nama' => 'Kapten Heri',
            'nrp_nip' => '11112222',
            'pangkat_golongan' => 'Kapten',
            'jabatan' => 'Perwira Ahli',
            'jenis_personel' => 'militer',
            'status_aktif' => true,
        ]);

        $bintara = Personel::create([
            'nama' => 'Serda Joko',
            'nrp_nip' => '33334444',
            'pangkat_golongan' => 'Serda',
            'jabatan' => 'Baur',
            'jenis_personel' => 'militer',
            'status_aktif' => true,
        ]);

        $tamtama = Personel::create([
            'nama' => 'Praka Hendra',
            'nrp_nip' => '55556666',
            'pangkat_golongan' => 'Praka',
            'jabatan' => 'Ta Gudang',
            'jenis_personel' => 'militer',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.organization.index'));
        $response->assertStatus(200);
        $response->assertSee('Kapten Heri');
        $response->assertDontSee('Serda Joko');
        $response->assertDontSee('Praka Hendra');

        $responseSigners = $this->actingAs($this->admin)->get(route('admin.signers.index'));
        $responseSigners->assertStatus(200);
        $responseSigners->assertSee('Kapten Heri');
        $responseSigners->assertDontSee('Serda Joko');
        $responseSigners->assertDontSee('Praka Hendra');
    }

    public function test_category_headers_cannot_be_assigned_officials_and_are_excluded_from_assignable_dropdown()
    {
        $pelaksana = OrganizationUnit::create([
            'name' => 'UNSUR PELAKSANA',
            'code' => 'UP',
            'level' => 'unit',
            'is_active' => true,
        ]);

        $pelayanan = OrganizationUnit::create([
            'name' => 'UNSUR PELAYANAN',
            'code' => 'UY',
            'level' => 'unit',
            'is_active' => true,
        ]);

        $personel = Personel::create([
            'nama' => 'Mayor Inf Agus',
            'nrp_nip' => '77889900',
            'pangkat_golongan' => 'Mayor',
            'jabatan' => 'Pamen Ahli',
            'jenis_personel' => 'militer',
            'status_aktif' => true,
        ]);

        // Trying to assign official to UNSUR PELAKSANA should fail with redirect error
        $response = $this->actingAs($this->admin)->post(route('admin.organization.assign'), [
            'organization_unit_id' => $pelaksana->id,
            'personel_id' => $personel->id,
            'role' => 'kabag',
        ]);

        $response->assertRedirect(route('admin.organization.index'));
        $response->assertSessionHas('error');

        // Check that page renders with "Tanpa Pejabat (Judul Kelompok)"
        $page = $this->actingAs($this->admin)->get(route('admin.organization.index'));
        $page->assertStatus(200);
        $page->assertSee('Tanpa Pejabat (Judul Kelompok)');
    }

    public function test_kelompok_pimpinan_has_kabeng_and_wakabeng_and_service_resolves_officials()
    {
        $pimpinan = OrganizationUnit::create([
            'name' => 'KELOMPOK PIMPINAN',
            'code' => 'PIMPINAN',
            'level' => 'unit',
            'is_active' => true,
        ]);

        $kabengUnit = OrganizationUnit::create([
            'name' => 'KABENG',
            'code' => 'KABENG',
            'parent_id' => $pimpinan->id,
            'level' => 'subunit',
            'is_active' => true,
        ]);

        $wakabengUnit = OrganizationUnit::create([
            'name' => 'WAKABENG',
            'code' => 'WAKABENG',
            'parent_id' => $pimpinan->id,
            'level' => 'subunit',
            'is_active' => true,
        ]);

        $kolonel = Personel::create([
            'nama' => 'Kolonel Cke Budi',
            'nrp_nip' => '11223344',
            'pangkat_golongan' => 'Kolonel',
            'jabatan' => 'Kabengpuskomlekad',
            'jenis_personel' => 'militer',
            'status_aktif' => true,
        ]);

        $letkol = Personel::create([
            'nama' => 'Letkol Cke Indra',
            'nrp_nip' => '55667788',
            'pangkat_golongan' => 'Letkol',
            'jabatan' => 'Wakabengpuskomlekad',
            'jenis_personel' => 'militer',
            'status_aktif' => true,
        ]);

        // Assign Kolonel as KABENG
        $this->actingAs($this->admin)->post(route('admin.organization.assign'), [
            'organization_unit_id' => $kabengUnit->id,
            'personel_id' => $kolonel->id,
            'role' => 'kabeng',
        ])->assertRedirect(route('admin.organization.index'));

        // Assign Letkol as WAKABENG
        $this->actingAs($this->admin)->post(route('admin.organization.assign'), [
            'organization_unit_id' => $wakabengUnit->id,
            'personel_id' => $letkol->id,
            'role' => 'wakabeng',
        ])->assertRedirect(route('admin.organization.index'));

        // Service should resolve both accurately
        $service = app(\App\Services\OrganizationStructureService::class);
        $this->assertEquals($kolonel->id, $service->getSignerPersonel('kabeng')->id);
        $this->assertEquals($letkol->id, $service->getSignerPersonel('wakabeng')->id);
        $this->assertEquals($letkol->id, $service->getSignerPersonel('waka')->id);

        // SignerOfficial table should also be auto-synced
        $this->assertDatabaseHas('signer_officials', [
            'role' => 'kabeng',
            'personel_id' => $kolonel->id,
        ]);
        $this->assertDatabaseHas('signer_officials', [
            'role' => 'wakabeng',
            'personel_id' => $letkol->id,
        ]);
    }

    public function test_leave_signatories_route_redirects_to_organization_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.leave_signatories.index'));
        $response->assertRedirect(route('admin.organization.index'));
        $response->assertSessionHas('info');
    }

    public function test_validasi_orgas_structure_seeded_and_rendered_correctly()
    {
        $this->seed(\Database\Seeders\OrganizationStructureSeeder::class);

        $response = $this->actingAs($this->admin)->get(route('admin.organization.index'));
        $response->assertStatus(200);
        $response->assertSee('VALIDASI ORGAS BENGPUSKOMLEK');
        $response->assertSee('Unsur Pimpinan');
        $response->assertSee('Unsur Pembantu Pimpinan');
        $response->assertSee('Unsur Pelayanan');
        $response->assertSee('Unsur Pelaksana');
        $response->assertSee('KEPALA');
        $response->assertSee('WAKIL KEPALA');
        $response->assertSee('KABAGUM');
        $response->assertSee('KABAGRENDAL');
        $response->assertSee('PASITUUD');
        $response->assertSee('KABENG SISKOM');
        $response->assertSee('SUBBENG RADIO DIGILOG');
        $response->assertSee('SUBBENG ALKOMSAL DAN MULTIMEDIA');
        $response->assertSee('SUBBENG ALKOMSAT');
        $response->assertSee('KABENG SISLEK');
        $response->assertSee('SUBBENG ALDALLEK');
        $response->assertSee('SUBBENG ALPERNIKA');
        $response->assertSee('SUBBENG MATINDRALEK');
        $response->assertSee('SUBBENG MEKATRONIKA');
        $response->assertSee('KABENG JARINGAN DAN TIK');
        $response->assertSee('SUBBENG JARKABEL');
        $response->assertSee('SUBBENG JARNIRKABEL');
        $response->assertSee('SUBBENG TIK');
        $response->assertSee('KABENG INTEGRASI DAN POWER SYSTEM');
        $response->assertSee('SUBBENG INTEGRASI');
        $response->assertSee('SUBBENG POWER SYSTEM');
        $response->assertSee('KAGUD');
    }
}

