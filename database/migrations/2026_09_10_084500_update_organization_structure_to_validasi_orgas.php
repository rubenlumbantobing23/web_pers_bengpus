<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\OrganizationUnit;
use App\Models\OrganizationOfficialAssignment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. UNSUR PIMPINAN
        $pimpinan = OrganizationUnit::where('name', 'KELOMPOK PIMPINAN')
            ->orWhere('name', 'UNSUR PIMPINAN')
            ->first();

        if ($pimpinan) {
            $pimpinan->update([
                'name' => 'UNSUR PIMPINAN',
                'parent_id' => null,
                'level' => 'unit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            $pimpinan = OrganizationUnit::create([
                'name' => 'UNSUR PIMPINAN',
                'parent_id' => null,
                'level' => 'unit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        // KEPALA
        $kepala = OrganizationUnit::where('parent_id', $pimpinan->id)
            ->where(function ($q) {
                $q->where('name', 'KABENG')->orWhere('name', 'KEPALA');
            })->first();

        if ($kepala) {
            $kepala->update([
                'name' => 'KEPALA',
                'code' => 'KABENG',
                'parent_id' => $pimpinan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            $kepala = OrganizationUnit::create([
                'name' => 'KEPALA',
                'code' => 'KABENG',
                'parent_id' => $pimpinan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        // WAKIL KEPALA
        $wakil = OrganizationUnit::where('parent_id', $pimpinan->id)
            ->where(function ($q) {
                $q->where('name', 'WAKABENG')->orWhere('name', 'WAKIL KEPALA');
            })->first();

        if ($wakil) {
            $wakil->update([
                'name' => 'WAKIL KEPALA',
                'code' => 'WAKABENG',
                'parent_id' => $pimpinan->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        } else {
            $wakil = OrganizationUnit::create([
                'name' => 'WAKIL KEPALA',
                'code' => 'WAKABENG',
                'parent_id' => $pimpinan->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        }

        // 2. UNSUR PEMBANTU PIMPINAN
        $pembantuPimpinan = OrganizationUnit::where('name', 'UNSUR PEMBANTU PIMPINAN')->first();
        if (!$pembantuPimpinan) {
            $pembantuPimpinan = OrganizationUnit::create([
                'name' => 'UNSUR PEMBANTU PIMPINAN',
                'parent_id' => null,
                'level' => 'unit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        } else {
            $pembantuPimpinan->update([
                'parent_id' => null,
                'level' => 'unit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        }

        // KABAGUM (formerly BAGUM)
        $kabagum = OrganizationUnit::where('name', 'BAGUM')->orWhere('name', 'KABAGUM')->first();
        if ($kabagum) {
            $kabagum->update([
                'name' => 'KABAGUM',
                'code' => 'KABAGUM',
                'parent_id' => $pembantuPimpinan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            $kabagum = OrganizationUnit::create([
                'name' => 'KABAGUM',
                'code' => 'KABAGUM',
                'parent_id' => $pembantuPimpinan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        // KABAGRENDAL (formerly BAGRENDAL under BAGUM)
        $kabagrendal = OrganizationUnit::where('name', 'BAGRENDAL')->orWhere('name', 'KABAGRENDAL')->first();
        if ($kabagrendal) {
            $kabagrendal->update([
                'name' => 'KABAGRENDAL',
                'code' => 'KABAGRENDAL',
                'parent_id' => $pembantuPimpinan->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
            // Update assignment role to kabagrendal if currently pasirendal
            OrganizationOfficialAssignment::where('organization_unit_id', $kabagrendal->id)
                ->where('role', 'pasirendal')
                ->update(['role' => 'kabagrendal']);
        } else {
            $kabagrendal = OrganizationUnit::create([
                'name' => 'KABAGRENDAL',
                'code' => 'KABAGRENDAL',
                'parent_id' => $pembantuPimpinan->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        }

        // Nonaktifkan seksi staf bagum lama (SIPAM, SIOPS, SIPERS, SILOG) agar sesuai Orgas Validasi
        OrganizationUnit::whereIn('name', ['SIPAM', 'SIOPS', 'SIPERS', 'SILOG'])
            ->update(['is_active' => false]);

        // 3. UNSUR PELAYANAN
        $unsurPelayanan = OrganizationUnit::updateOrCreate(['name' => 'UNSUR PELAYANAN'], [
            'parent_id' => null,
            'level' => 'unit',
            'sort_order' => 30,
            'is_active' => true,
        ]);

        // PASITUUD (formerly SITUUD)
        $situud = OrganizationUnit::where('name', 'SITUUD')->orWhere('name', 'PASITUUD')->first();
        if ($situud) {
            $situud->update([
                'name' => 'PASITUUD',
                'code' => 'PASITUUD',
                'parent_id' => $unsurPelayanan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            OrganizationUnit::create([
                'name' => 'PASITUUD',
                'code' => 'PASITUUD',
                'parent_id' => $unsurPelayanan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        // 4. UNSUR PELAKSANA
        $unsurPelaksana = OrganizationUnit::updateOrCreate(['name' => 'UNSUR PELAKSANA'], [
            'parent_id' => null,
            'level' => 'unit',
            'sort_order' => 40,
            'is_active' => true,
        ]);

        // 4a. KABENG SISKOM
        $siskom = OrganizationUnit::where('name', 'BENGSISKOM')->orWhere('name', 'KABENG SISKOM')->first();
        if ($siskom) {
            $siskom->update([
                'name' => 'KABENG SISKOM',
                'code' => 'KABENG SISKOM',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            $siskom = OrganizationUnit::create([
                'name' => 'KABENG SISKOM',
                'code' => 'KABENG SISKOM',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        // Subbeng Siskom
        $radioDigilog = OrganizationUnit::where('name', 'SUBBENGRAD DIGILOG')
            ->orWhere('name', 'SUBBENG RADIO DIGILOG')
            ->first();
        if ($radioDigilog) {
            $radioDigilog->update([
                'name' => 'SUBBENG RADIO DIGILOG',
                'parent_id' => $siskom->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            OrganizationUnit::create([
                'name' => 'SUBBENG RADIO DIGILOG',
                'parent_id' => $siskom->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        OrganizationUnit::updateOrCreate(['name' => 'SUBBENG ALKOMSAL DAN MULTIMEDIA'], [
            'parent_id' => $siskom->id,
            'level' => 'subunit',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        OrganizationUnit::updateOrCreate(['name' => 'SUBBENG ALKOMSAT'], [
            'parent_id' => $siskom->id,
            'level' => 'subunit',
            'sort_order' => 30,
            'is_active' => true,
        ]);

        // 4b. KABENG SISLEK
        $sislek = OrganizationUnit::where('name', 'BENGSISLEK')->orWhere('name', 'KABENG SISLEK')->first();
        if ($sislek) {
            $sislek->update([
                'name' => 'KABENG SISLEK',
                'code' => 'KABENG SISLEK',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        } else {
            $sislek = OrganizationUnit::create([
                'name' => 'KABENG SISLEK',
                'code' => 'KABENG SISLEK',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        }

        OrganizationUnit::updateOrCreate(['name' => 'SUBBENG ALDALLEK'], [
            'parent_id' => $sislek->id,
            'level' => 'subunit',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        OrganizationUnit::updateOrCreate(['name' => 'SUBBENG ALPERNIKA'], [
            'parent_id' => $sislek->id,
            'level' => 'subunit',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        // Matindralek (fix typo matrindralek)
        $matindralek = OrganizationUnit::where('name', 'SUBBENG MATRINDRALEK')
            ->orWhere('name', 'SUBBENG MATINDRALEK')
            ->first();
        if ($matindralek) {
            $matindralek->update([
                'name' => 'SUBBENG MATINDRALEK',
                'parent_id' => $sislek->id,
                'level' => 'subunit',
                'sort_order' => 30,
                'is_active' => true,
            ]);
        } else {
            OrganizationUnit::create([
                'name' => 'SUBBENG MATINDRALEK',
                'parent_id' => $sislek->id,
                'level' => 'subunit',
                'sort_order' => 30,
                'is_active' => true,
            ]);
        }

        OrganizationUnit::updateOrCreate(['name' => 'SUBBENG MEKATRONIKA'], [
            'parent_id' => $sislek->id,
            'level' => 'subunit',
            'sort_order' => 40,
            'is_active' => true,
        ]);

        // 4c. KABENG JARINGAN DAN TIK
        $jaringan = OrganizationUnit::where('name', 'BENGJARINGAN DAN TIK')->orWhere('name', 'KABENG JARINGAN DAN TIK')->first();
        if ($jaringan) {
            $jaringan->update([
                'name' => 'KABENG JARINGAN DAN TIK',
                'code' => 'KABENG JARINGAN DAN TIK',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 30,
                'is_active' => true,
            ]);
        } else {
            $jaringan = OrganizationUnit::create([
                'name' => 'KABENG JARINGAN DAN TIK',
                'code' => 'KABENG JARINGAN DAN TIK',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 30,
                'is_active' => true,
            ]);
        }

        // Subbeng Jaringan dan TIK (JARKABEL, JARNIRKABEL, TIK)
        $jarkabel = OrganizationUnit::where('name', 'SUBBENG JARINGAN KABEL')
            ->orWhere('name', 'SUBBENG JARKABEL')
            ->first();
        if ($jarkabel) {
            $jarkabel->update([
                'name' => 'SUBBENG JARKABEL',
                'parent_id' => $jaringan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            OrganizationUnit::create([
                'name' => 'SUBBENG JARKABEL',
                'parent_id' => $jaringan->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        $jarnirkabel = OrganizationUnit::where('name', 'SUBBENG JARINGAN NIRKABEL DAN TIK')
            ->orWhere('name', 'SUBBENG JARNIRKABEL')
            ->first();
        if ($jarnirkabel) {
            $jarnirkabel->update([
                'name' => 'SUBBENG JARNIRKABEL',
                'parent_id' => $jaringan->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        } else {
            OrganizationUnit::create([
                'name' => 'SUBBENG JARNIRKABEL',
                'parent_id' => $jaringan->id,
                'level' => 'subunit',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        }

        OrganizationUnit::updateOrCreate(['name' => 'SUBBENG TIK'], [
            'parent_id' => $jaringan->id,
            'level' => 'subunit',
            'sort_order' => 30,
            'is_active' => true,
        ]);

        // 4d. KABENG INTEGRASI DAN POWER SYSTEM
        $integrasi = OrganizationUnit::where('name', 'BENGINTEGRASI DAN POWER SYSTEM')->orWhere('name', 'KABENG INTEGRASI DAN POWER SYSTEM')->first();
        if ($integrasi) {
            $integrasi->update([
                'name' => 'KABENG INTEGRASI DAN POWER SYSTEM',
                'code' => 'KABENG INTEGRASI DAN POWER SYSTEM',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 40,
                'is_active' => true,
            ]);
        } else {
            $integrasi = OrganizationUnit::create([
                'name' => 'KABENG INTEGRASI DAN POWER SYSTEM',
                'code' => 'KABENG INTEGRASI DAN POWER SYSTEM',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 40,
                'is_active' => true,
            ]);
        }

        // Subbeng Integrasi & Power System
        $subIntegrasi = OrganizationUnit::where('name', 'SUBBENG INTEGRASI DAN POWER SYSTEM')
            ->orWhere('name', 'SUBBENG INTEGRASI')
            ->first();
        if ($subIntegrasi) {
            $subIntegrasi->update([
                'name' => 'SUBBENG INTEGRASI',
                'parent_id' => $integrasi->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        } else {
            OrganizationUnit::create([
                'name' => 'SUBBENG INTEGRASI',
                'parent_id' => $integrasi->id,
                'level' => 'subunit',
                'sort_order' => 10,
                'is_active' => true,
            ]);
        }

        OrganizationUnit::updateOrCreate(['name' => 'SUBBENG POWER SYSTEM'], [
            'parent_id' => $integrasi->id,
            'level' => 'subunit',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        // 4e. KAGUD (under Unsur Pelaksana)
        $kagud = OrganizationUnit::where('name', 'GUDANG')->orWhere('name', 'KAGUD')->first();
        if ($kagud) {
            $kagud->update([
                'name' => 'KAGUD',
                'code' => 'KAGUD',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 50,
                'is_active' => true,
            ]);
        } else {
            OrganizationUnit::create([
                'name' => 'KAGUD',
                'code' => 'KAGUD',
                'parent_id' => $unsurPelaksana->id,
                'level' => 'subunit',
                'sort_order' => 50,
                'is_active' => true,
            ]);
        }

        // Nonaktifkan Koperasi dari bagan validasi
        OrganizationUnit::where('name', 'KOPERASI')->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe reverse
    }
};
