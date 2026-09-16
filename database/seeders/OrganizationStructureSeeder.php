<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationUnit;
use App\Models\OrganizationOfficialAssignment;
use App\Models\SignerOfficial;

class OrganizationStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. UNSUR PIMPINAN
        $unsurPimpinan = OrganizationUnit::updateOrCreate(['name' => 'UNSUR PIMPINAN'], [
            'parent_id' => null,
            'level' => 'unit',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $kepala = OrganizationUnit::updateOrCreate(['name' => 'KEPALA'], [
            'parent_id' => $unsurPimpinan->id,
            'code' => 'KABENG',
            'level' => 'subunit',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $wakilKepala = OrganizationUnit::updateOrCreate(['name' => 'WAKIL KEPALA'], [
            'parent_id' => $unsurPimpinan->id,
            'code' => 'WAKABENG',
            'level' => 'subunit',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        // Clean up any old assignment directly attached to header
        OrganizationOfficialAssignment::where('organization_unit_id', $unsurPimpinan->id)
            ->update(['organization_unit_id' => $kepala->id]);

        // Sync initial assignments for KEPALA and WAKIL KEPALA from existing signers if available
        $kabengSigner = SignerOfficial::where('role', 'kabeng')->first();
        if ($kabengSigner && !OrganizationOfficialAssignment::where('organization_unit_id', $kepala->id)->where('is_active', true)->exists()) {
            OrganizationOfficialAssignment::create([
                'organization_unit_id' => $kepala->id,
                'personel_id' => $kabengSigner->personel_id,
                'role' => 'kabeng',
                'is_active' => true,
                'valid_from' => now(),
            ]);
        }

        $wakaSigner = SignerOfficial::where('role', 'wakabeng')->first();
        if ($wakaSigner && !OrganizationOfficialAssignment::where('organization_unit_id', $wakilKepala->id)->where('is_active', true)->exists()) {
            OrganizationOfficialAssignment::create([
                'organization_unit_id' => $wakilKepala->id,
                'personel_id' => $wakaSigner->personel_id,
                'role' => 'wakabeng',
                'is_active' => true,
                'valid_from' => now(),
            ]);
        }

        // 2. UNSUR PEMBANTU PIMPINAN
        $pembantuPimpinan = OrganizationUnit::updateOrCreate(['name' => 'UNSUR PEMBANTU PIMPINAN'], [
            'parent_id' => null,
            'level' => 'unit',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $kabagum = OrganizationUnit::updateOrCreate(['name' => 'KABAGUM'], [
            'parent_id' => $pembantuPimpinan->id,
            'code' => 'KABAGUM',
            'level' => 'subunit',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $kabagrendal = OrganizationUnit::updateOrCreate(['name' => 'KABAGRENDAL'], [
            'parent_id' => $pembantuPimpinan->id,
            'code' => 'KABAGRENDAL',
            'level' => 'subunit',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        // 3. UNSUR PELAYANAN
        $unsurPelayanan = OrganizationUnit::updateOrCreate(['name' => 'UNSUR PELAYANAN'], [
            'parent_id' => null,
            'level' => 'unit',
            'sort_order' => 30,
            'is_active' => true,
        ]);

        $pasituud = OrganizationUnit::updateOrCreate(['name' => 'PASITUUD'], [
            'parent_id' => $unsurPelayanan->id,
            'code' => 'PASITUUD',
            'level' => 'subunit',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        // 4. UNSUR PELAKSANA
        $unsurPelaksana = OrganizationUnit::updateOrCreate(['name' => 'UNSUR PELAKSANA'], [
            'parent_id' => null,
            'level' => 'unit',
            'sort_order' => 40,
            'is_active' => true,
        ]);

        // 4a. KABENG SISKOM
        $kabengSiskom = OrganizationUnit::updateOrCreate(['name' => 'KABENG SISKOM'], [
            'parent_id' => $unsurPelaksana->id,
            'code' => 'KABENG SISKOM',
            'level' => 'subunit',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $siskomSub = ['SUBBENG RADIO DIGILOG', 'SUBBENG ALKOMSAL DAN MULTIMEDIA', 'SUBBENG ALKOMSAT'];
        foreach ($siskomSub as $i => $name) {
            OrganizationUnit::updateOrCreate(['name' => $name], [
                'parent_id' => $kabengSiskom->id,
                'level' => 'subunit',
                'sort_order' => ($i + 1) * 10,
                'is_active' => true,
            ]);
        }

        // 4b. KABENG SISLEK
        $kabengSislek = OrganizationUnit::updateOrCreate(['name' => 'KABENG SISLEK'], [
            'parent_id' => $unsurPelaksana->id,
            'code' => 'KABENG SISLEK',
            'level' => 'subunit',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $sislekSub = ['SUBBENG ALDALLEK', 'SUBBENG ALPERNIKA', 'SUBBENG MATINDRALEK', 'SUBBENG MEKATRONIKA'];
        foreach ($sislekSub as $i => $name) {
            OrganizationUnit::updateOrCreate(['name' => $name], [
                'parent_id' => $kabengSislek->id,
                'level' => 'subunit',
                'sort_order' => ($i + 1) * 10,
                'is_active' => true,
            ]);
        }

        // 4c. KABENG JARINGAN DAN TIK
        $kabengJaringan = OrganizationUnit::updateOrCreate(['name' => 'KABENG JARINGAN DAN TIK'], [
            'parent_id' => $unsurPelaksana->id,
            'code' => 'KABENG JARINGAN DAN TIK',
            'level' => 'subunit',
            'sort_order' => 30,
            'is_active' => true,
        ]);

        $jaringanSub = ['SUBBENG JARKABEL', 'SUBBENG JARNIRKABEL', 'SUBBENG TIK'];
        foreach ($jaringanSub as $i => $name) {
            OrganizationUnit::updateOrCreate(['name' => $name], [
                'parent_id' => $kabengJaringan->id,
                'level' => 'subunit',
                'sort_order' => ($i + 1) * 10,
                'is_active' => true,
            ]);
        }

        // 4d. KABENG INTEGRASI DAN POWER SYSTEM
        $kabengIntegrasi = OrganizationUnit::updateOrCreate(['name' => 'KABENG INTEGRASI DAN POWER SYSTEM'], [
            'parent_id' => $unsurPelaksana->id,
            'code' => 'KABENG INTEGRASI DAN POWER SYSTEM',
            'level' => 'subunit',
            'sort_order' => 40,
            'is_active' => true,
        ]);

        $integrasiSub = ['SUBBENG INTEGRASI', 'SUBBENG POWER SYSTEM'];
        foreach ($integrasiSub as $i => $name) {
            OrganizationUnit::updateOrCreate(['name' => $name], [
                'parent_id' => $kabengIntegrasi->id,
                'level' => 'subunit',
                'sort_order' => ($i + 1) * 10,
                'is_active' => true,
            ]);
        }

        // 4e. KAGUD
        OrganizationUnit::updateOrCreate(['name' => 'KAGUD'], [
            'parent_id' => $unsurPelaksana->id,
            'code' => 'KAGUD',
            'level' => 'subunit',
            'sort_order' => 50,
            'is_active' => true,
        ]);

        // Cleanup any obsolete old unit headers or names
        OrganizationUnit::whereIn('name', ['KELOMPOK PIMPINAN', 'BAGUM', 'SIPAM', 'SIOPS', 'SIPERS', 'SILOG', 'GUDANG', 'KOPERASI'])
            ->update(['is_active' => false]);
    }
}
