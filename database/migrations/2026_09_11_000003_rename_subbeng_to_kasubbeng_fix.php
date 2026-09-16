<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Aktifkan PASIPAM, PASIOPS, PASIPERS, PASILOG yang tidak aktif
        DB::table('organization_units')
            ->whereIn('id', [3, 4, 5, 6])
            ->update(['is_active' => true]);

        // 2. Rename SUBBENG → KASUBBENG for original units (12,13,14,16,17,18,19,21,22,24,30,31)
        $renames = [
            12 => 'KASUBBENG RADIO DIGILOG',
            13 => 'KASUBBENG ALKOMSAL DAN MULTIMEDIA',
            14 => 'KASUBBENG ALKOMSAT',
            16 => 'KASUBBENG ALDALLEK',
            17 => 'KASUBBENG ALPERNIKA',
            18 => 'KASUBBENG MATINDRALEK',
            19 => 'KASUBBENG MEKATRONIKA',
            21 => 'KASUBBENG JARKABEL',
            22 => 'KASUBBENG JARNIRKABEL',
            24 => 'KASUBBENG INTEGRASI',
            30 => 'KASUBBENG TIK',
            31 => 'KASUBBENG POWER SYSTEM',
        ];

        foreach ($renames as $id => $newName) {
            DB::table('organization_units')
                ->where('id', $id)
                ->update(['name' => $newName, 'code' => $newName]);
        }

        // 3. Delete the duplicate KASUBBENG units added by mistake (35-46)
        DB::table('organization_units')
            ->whereIn('id', range(35, 46))
            ->delete();
    }

    public function down(): void
    {
        // Revert renames
        $originals = [
            12 => 'SUBBENG RADIO DIGILOG',
            13 => 'SUBBENG ALKOMSAL DAN MULTIMEDIA',
            14 => 'SUBBENG ALKOMSAT',
            16 => 'SUBBENG ALDALLEK',
            17 => 'SUBBENG ALPERNIKA',
            18 => 'SUBBENG MATINDRALEK',
            19 => 'SUBBENG MEKATRONIKA',
            21 => 'SUBBENG JARKABEL',
            22 => 'SUBBENG JARNIRKABEL',
            24 => 'SUBBENG INTEGRASI',
            30 => 'SUBBENG TIK',
            31 => 'SUBBENG POWER SYSTEM',
        ];
        foreach ($originals as $id => $name) {
            DB::table('organization_units')->where('id', $id)->update(['name' => $name, 'code' => null]);
        }

        // Deactivate PASIPAM etc. back
        DB::table('organization_units')->whereIn('id', [3,4,5,6])->update(['is_active' => false]);
    }
};
