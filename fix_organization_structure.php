<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OrganizationUnit;
use Illuminate\Support\Facades\DB;

// Helper to find existing unit or create
function getOrCreateUnit($name, $code, $parentId = null, $order = 0) {
    $unit = OrganizationUnit::where('name', $name)->first();
    if (!$unit) {
        $unit = OrganizationUnit::create([
            'name' => $name,
            'code' => $code,
            'parent_id' => $parentId,
            'sort_order' => $order,
            'is_active' => true,
        ]);
    } else {
        $unit->update([
            'code' => $code,
            'parent_id' => $parentId,
            'sort_order' => $order,
            'is_active' => true,
        ]);
    }
    return $unit;
}

DB::beginTransaction();

try {
    // 1. Rename existing root units if they exist, or create them
    
    // KELOMPOK PIMPINAN (formerly UNSUR PIMPINAN)
    $kelompokPimpinan = OrganizationUnit::where('name', 'UNSUR PIMPINAN')->orWhere('name', 'KELOMPOK PIMPINAN')->first();
    if ($kelompokPimpinan) {
        $kelompokPimpinan->update(['name' => 'KELOMPOK PIMPINAN', 'code' => 'KELOMPOK_PIMPINAN', 'parent_id' => null, 'sort_order' => 10, 'is_active' => true]);
    } else {
        $kelompokPimpinan = getOrCreateUnit('KELOMPOK PIMPINAN', 'KELOMPOK_PIMPINAN', null, 10);
    }

    // KABENG (formerly KEPALA)
    $kabeng = OrganizationUnit::where('name', 'KEPALA')->orWhere('name', 'KABENG')->first();
    if ($kabeng) {
        $kabeng->update(['name' => 'KABENG', 'code' => 'KABENG', 'parent_id' => $kelompokPimpinan->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        $kabeng = getOrCreateUnit('KABENG', 'KABENG', $kelompokPimpinan->id, 10);
    }

    // WAKABENG (formerly WAKIL KEPALA)
    $wakabeng = OrganizationUnit::where('name', 'WAKIL KEPALA')->orWhere('name', 'WAKABENG')->first();
    if ($wakabeng) {
        $wakabeng->update(['name' => 'WAKABENG', 'code' => 'WAKABENG', 'parent_id' => $kelompokPimpinan->id, 'sort_order' => 20, 'is_active' => true]);
    } else {
        $wakabeng = getOrCreateUnit('WAKABENG', 'WAKABENG', $kelompokPimpinan->id, 20);
    }

    // BAGUM (formerly UNSUR PEMBANTU PIMPINAN)
    $bagum = OrganizationUnit::where('name', 'UNSUR PEMBANTU PIMPINAN')->orWhere('name', 'BAGUM')->first();
    if ($bagum) {
        $bagum->update(['name' => 'BAGUM', 'code' => 'BAGUM', 'parent_id' => null, 'sort_order' => 20, 'is_active' => true]);
    } else {
        $bagum = getOrCreateUnit('BAGUM', 'BAGUM', null, 20);
    }

    // BAGUM Children (SIPAM, SIOPS, SIPERS, SILOG, BAGRENDAL)
    $sipam = OrganizationUnit::where('name', 'PASIPAM')->orWhere('name', 'SIPAM')->first();
    if ($sipam) {
        $sipam->update(['name' => 'SIPAM', 'code' => 'SIPAM', 'parent_id' => $bagum->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        $sipam = getOrCreateUnit('SIPAM', 'SIPAM', $bagum->id, 10);
    }

    $siops = OrganizationUnit::where('name', 'PASIOPS')->orWhere('name', 'SIOPS')->first();
    if ($siops) {
        $siops->update(['name' => 'SIOPS', 'code' => 'SIOPS', 'parent_id' => $bagum->id, 'sort_order' => 20, 'is_active' => true]);
    } else {
        $siops = getOrCreateUnit('SIOPS', 'SIOPS', $bagum->id, 20);
    }

    $sipers = OrganizationUnit::where('name', 'PASIPERS')->orWhere('name', 'SIPERS')->first();
    if ($sipers) {
        $sipers->update(['name' => 'SIPERS', 'code' => 'SIPERS', 'parent_id' => $bagum->id, 'sort_order' => 30, 'is_active' => true]);
    } else {
        $sipers = getOrCreateUnit('SIPERS', 'SIPERS', $bagum->id, 30);
    }

    $silog = OrganizationUnit::where('name', 'PASILOG')->orWhere('name', 'SILOG')->first();
    if ($silog) {
        $silog->update(['name' => 'SILOG', 'code' => 'SILOG', 'parent_id' => $bagum->id, 'sort_order' => 40, 'is_active' => true]);
    } else {
        $silog = getOrCreateUnit('SILOG', 'SILOG', $bagum->id, 40);
    }

    $bagrendal = OrganizationUnit::where('name', 'PASIRENDAL')->orWhere('name', 'BAGRENDAL')->first();
    if ($bagrendal) {
        $bagrendal->update(['name' => 'BAGRENDAL', 'code' => 'BAGRENDAL', 'parent_id' => $bagum->id, 'sort_order' => 50, 'is_active' => true]);
    } else {
        $bagrendal = getOrCreateUnit('BAGRENDAL', 'BAGRENDAL', $bagum->id, 50);
    }

    // UNSUR PELAYANAN
    $unsurPelayanan = OrganizationUnit::where('name', 'UNSUR PELAYANAN')->first();
    if ($unsurPelayanan) {
        $unsurPelayanan->update(['code' => 'UNSUR_PELAYANAN', 'parent_id' => null, 'sort_order' => 30, 'is_active' => true]);
    } else {
        $unsurPelayanan = getOrCreateUnit('UNSUR PELAYANAN', 'UNSUR_PELAYANAN', null, 30);
    }

    // SITUUD (formerly PASITUUD)
    $situud = OrganizationUnit::where('name', 'PASITUUD')->orWhere('name', 'SITUUD')->first();
    if ($situud) {
        $situud->update(['name' => 'SITUUD', 'code' => 'SITUUD', 'parent_id' => $unsurPelayanan->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        $situud = getOrCreateUnit('SITUUD', 'SITUUD', $unsurPelayanan->id, 10);
    }

    // UNSUR PELAKSANA
    $unsurPelaksana = OrganizationUnit::where('name', 'UNSUR PELAKSANA')->first();
    if ($unsurPelaksana) {
        $unsurPelaksana->update(['code' => 'UNSUR_PELAKSANA', 'parent_id' => null, 'sort_order' => 40, 'is_active' => true]);
    } else {
        $unsurPelaksana = getOrCreateUnit('UNSUR PELAKSANA', 'UNSUR_PELAKSANA', null, 40);
    }

    // BENGSISKOM
    $bengSiskom = OrganizationUnit::where('name', 'KABENG SISKOM')->orWhere('name', 'BENGSISKOM')->first();
    if ($bengSiskom) {
        $bengSiskom->update(['name' => 'BENGSISKOM', 'code' => 'BENGSISKOM', 'parent_id' => $unsurPelaksana->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        $bengSiskom = getOrCreateUnit('BENGSISKOM', 'BENGSISKOM', $unsurPelaksana->id, 10);
    }
    
    // BENGSISKOM Children
    $subbengRadioDigilog = OrganizationUnit::where('name', 'SUBBENG RADIO DIGILOG')->orWhere('name', 'KASUBBENG RADIO DIGILOG')->first();
    if ($subbengRadioDigilog) {
        $subbengRadioDigilog->update(['name' => 'SUBBENG RADIO DIGILOG', 'code' => 'SUBBENG_RADIO_DIGILOG', 'parent_id' => $bengSiskom->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        $subbengRadioDigilog = getOrCreateUnit('SUBBENG RADIO DIGILOG', 'SUBBENG_RADIO_DIGILOG', $bengSiskom->id, 10);
    }

    $subbengAlkom = OrganizationUnit::where('name', 'SUBBENG ALKOMSAL DAN MULTIMEDIA')->orWhere('name', 'KASUBBENG ALKOMSAL DAN MULTIMEDIA')->first();
    if ($subbengAlkom) {
        $subbengAlkom->update(['name' => 'SUBBENG ALKOMSAL DAN MULTIMEDIA', 'code' => 'SUBBENG_ALKOMSAL_MULTIMEDIA', 'parent_id' => $bengSiskom->id, 'sort_order' => 20, 'is_active' => true]);
    } else {
        $subbengAlkom = getOrCreateUnit('SUBBENG ALKOMSAL DAN MULTIMEDIA', 'SUBBENG_ALKOMSAL_MULTIMEDIA', $bengSiskom->id, 20);
    }

    $subbengAlkomsat = OrganizationUnit::where('name', 'SUBBENG ALKOMSAT')->orWhere('name', 'KASUBBENG ALKOMSAT')->first();
    if ($subbengAlkomsat) {
        $subbengAlkomsat->update(['name' => 'SUBBENG ALKOMSAT', 'code' => 'SUBBENG_ALKOMSAT', 'parent_id' => $bengSiskom->id, 'sort_order' => 30, 'is_active' => true]);
    } else {
        $subbengAlkomsat = getOrCreateUnit('SUBBENG ALKOMSAT', 'SUBBENG_ALKOMSAT', $bengSiskom->id, 30);
    }


    // BENGSISLEK
    $bengSislek = getOrCreateUnit('BENGSISLEK', 'BENGSISLEK', $unsurPelaksana->id, 20);

    // BENGSISLEK Children
    $subbengAldallek = OrganizationUnit::where('name', 'SUBBENG ALDALLEK')->orWhere('name', 'KASUBBENG ALDALLEK')->first();
    if ($subbengAldallek) {
        $subbengAldallek->update(['name' => 'SUBBENG ALDALLEK', 'code' => 'SUBBENG_ALDALLEK', 'parent_id' => $bengSislek->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG ALDALLEK', 'SUBBENG_ALDALLEK', $bengSislek->id, 10);
    }

    $subbengAlpernika = OrganizationUnit::where('name', 'SUBBENG ALPERNIKA')->orWhere('name', 'KASUBBENG ALPERNIKA')->first();
    if ($subbengAlpernika) {
        $subbengAlpernika->update(['name' => 'SUBBENG ALPERNIKA', 'code' => 'SUBBENG_ALPERNIKA', 'parent_id' => $bengSislek->id, 'sort_order' => 20, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG ALPERNIKA', 'SUBBENG_ALPERNIKA', $bengSislek->id, 20);
    }

    $subbengMatrindralek = OrganizationUnit::where('name', 'SUBBENG MATINDRALEK')->orWhere('name', 'SUBBENG MATRINDRALEK')->orWhere('name', 'KASUBBENG MATINDRALEK')->first();
    if ($subbengMatrindralek) {
        $subbengMatrindralek->update(['name' => 'SUBBENG MATRINDRALEK', 'code' => 'SUBBENG_MATRINDRALEK', 'parent_id' => $bengSislek->id, 'sort_order' => 30, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG MATRINDRALEK', 'SUBBENG_MATRINDRALEK', $bengSislek->id, 30);
    }

    $subbengMekatronika = OrganizationUnit::where('name', 'SUBBENG MEKATRONIKA')->orWhere('name', 'KASUBBENG MEKATRONIKA')->first();
    if ($subbengMekatronika) {
        $subbengMekatronika->update(['name' => 'SUBBENG MEKATRONIKA', 'code' => 'SUBBENG_MEKATRONIKA', 'parent_id' => $bengSislek->id, 'sort_order' => 40, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG MEKATRONIKA', 'SUBBENG_MEKATRONIKA', $bengSislek->id, 40);
    }


    // BENGJARINGAN DAN TIK
    $bengJaringan = getOrCreateUnit('BENGJARINGAN DAN TIK', 'BENGJARINGAN_DAN_TIK', $unsurPelaksana->id, 30);
    
    $subbengKabel = OrganizationUnit::where('name', 'SUBBENG JARKABEL')->orWhere('name', 'SUBBENG JARINGAN KABEL')->orWhere('name', 'KASUBBENG JARKABEL')->first();
    if ($subbengKabel) {
        $subbengKabel->update(['name' => 'SUBBENG JARINGAN KABEL', 'code' => 'SUBBENG_JARINGAN_KABEL', 'parent_id' => $bengJaringan->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG JARINGAN KABEL', 'SUBBENG_JARINGAN_KABEL', $bengJaringan->id, 10);
    }

    $subbengNirkabel = OrganizationUnit::where('name', 'SUBBENG JARNIRKABEL')->orWhere('name', 'SUBBENG JARINGAN NIRKABEL')->orWhere('name', 'KASUBBENG JARNIRKABEL')->first();
    if ($subbengNirkabel) {
        $subbengNirkabel->update(['name' => 'SUBBENG JARINGAN NIRKABEL', 'code' => 'SUBBENG_JARINGAN_NIRKABEL', 'parent_id' => $bengJaringan->id, 'sort_order' => 20, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG JARINGAN NIRKABEL', 'SUBBENG_JARINGAN_NIRKABEL', $bengJaringan->id, 20);
    }

    $subbengTik = OrganizationUnit::where('name', 'SUBBENG JARINGAN TIK')->orWhere('name', 'KASUBBENG TIK')->first();
    if ($subbengTik) {
        $subbengTik->update(['name' => 'SUBBENG JARINGAN TIK', 'code' => 'SUBBENG_JARINGAN_TIK', 'parent_id' => $bengJaringan->id, 'sort_order' => 30, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG JARINGAN TIK', 'SUBBENG_JARINGAN_TIK', $bengJaringan->id, 30);
    }

    
    // BENGINTEGRASI DAN POWER SYSTEM
    $bengIntegrasi = OrganizationUnit::where('name', 'KABENG INTEGRASI DAN POWER SYSTEM')->orWhere('name', 'BENGINTEGRASI DAN POWER SYSTEM')->first();
    if ($bengIntegrasi) {
        $bengIntegrasi->update(['name' => 'BENGINTEGRASI DAN POWER SYSTEM', 'code' => 'BENGINTEGRASI_POWER_SYSTEM', 'parent_id' => $unsurPelaksana->id, 'sort_order' => 40, 'is_active' => true]);
    } else {
        $bengIntegrasi = getOrCreateUnit('BENGINTEGRASI DAN POWER SYSTEM', 'BENGINTEGRASI_POWER_SYSTEM', $unsurPelaksana->id, 40);
    }

    $subbengIntegrasi = OrganizationUnit::where('name', 'KASUBBENG INTEGRASI')->orWhere('name', 'SUBBENG INTEGRASI')->first();
    if ($subbengIntegrasi) {
        $subbengIntegrasi->update(['name' => 'SUBBENG INTEGRASI', 'code' => 'SUBBENG_INTEGRASI', 'parent_id' => $bengIntegrasi->id, 'sort_order' => 10, 'is_active' => true]);
    } else {
        getOrCreateUnit('SUBBENG INTEGRASI', 'SUBBENG_INTEGRASI', $bengIntegrasi->id, 10);
    }

    $powerSystem = OrganizationUnit::where('name', 'SUBBENG POWER SYSTEM')->orWhere('name', 'POWER SYSTEM')->orWhere('name', 'KASUBBENG POWER SYSTEM')->first();
    if ($powerSystem) {
        $powerSystem->update(['name' => 'POWER SYSTEM', 'code' => 'POWER_SYSTEM', 'parent_id' => $bengIntegrasi->id, 'sort_order' => 20, 'is_active' => true]);
    } else {
        getOrCreateUnit('POWER SYSTEM', 'POWER_SYSTEM', $bengIntegrasi->id, 20);
    }

    // GUDANG
    $gudang = OrganizationUnit::where('name', 'KAGUD')->orWhere('name', 'GUDANG')->first();
    if ($gudang) {
        $gudang->update(['name' => 'GUDANG', 'code' => 'GUDANG', 'parent_id' => null, 'sort_order' => 50, 'is_active' => true]);
    } else {
        getOrCreateUnit('GUDANG', 'GUDANG', null, 50);
    }

    // List of ALL expected new unit names
    $expectedNames = [
        'KELOMPOK PIMPINAN', 'KABENG', 'WAKABENG',
        'BAGUM', 'SIPAM', 'SIOPS', 'SIPERS', 'SILOG', 'BAGRENDAL',
        'UNSUR PELAYANAN', 'SITUUD',
        'UNSUR PELAKSANA', 
        'BENGSISKOM', 'SUBBENG RADIO DIGILOG', 'SUBBENG ALKOMSAL DAN MULTIMEDIA', 'SUBBENG ALKOMSAT',
        'BENGSISLEK', 'SUBBENG ALDALLEK', 'SUBBENG ALPERNIKA', 'SUBBENG MATRINDRALEK', 'SUBBENG MEKATRONIKA',
        'BENGJARINGAN DAN TIK', 'SUBBENG JARINGAN KABEL', 'SUBBENG JARINGAN NIRKABEL', 'SUBBENG JARINGAN TIK',
        'BENGINTEGRASI DAN POWER SYSTEM', 'SUBBENG INTEGRASI', 'POWER SYSTEM',
        'GUDANG'
    ];

    // Soft delete/deactivate any duplicate old units (that weren't mapped above)
    OrganizationUnit::whereNotIn('name', $expectedNames)->update(['is_active' => false]);
    
    DB::commit();
    echo "Organization structure successfully mapped and updated!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error updating structure: " . $e->getMessage() . "\n";
}
