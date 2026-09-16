<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 1. Rename SIPAM→PASIPAM, SIOPS→PASIOPS, SIPERS→PASIPERS, SILOG→PASILOG (already under KABAGUM)
     * 2. Move KABAGRENDAL (id=7) under KABAGUM (id=2) and rename to PASIRENDAL
     * 3. Add KASUBBENG position units for each subbeng (jabatan-type, like KEPALA)
     */
    public function up(): void
    {
        // 1. Rename Sek* units to Pasi* under KABAGUM (IDs 3,4,5,6)
        DB::table('organization_units')->where('id', 3)->update(['name' => 'PASIPAM', 'code' => 'PASIPAM']);
        DB::table('organization_units')->where('id', 4)->update(['name' => 'PASIOPS', 'code' => 'PASIOPS']);
        DB::table('organization_units')->where('id', 5)->update(['name' => 'PASIPERS', 'code' => 'PASIPERS']);
        DB::table('organization_units')->where('id', 6)->update(['name' => 'PASILOG', 'code' => 'PASILOG']);

        // 2. Rename KABAGRENDAL → PASIRENDAL and move under KABAGUM (parent_id=2)
        DB::table('organization_units')->where('id', 7)->update([
            'name'      => 'PASIRENDAL',
            'code'      => 'PASIRENDAL',
            'parent_id' => 2,
            'sort_order' => 50,
        ]);

        // 3. Add KASUBBENG jabatan-position units (parent = their respective subbeng)
        $kasubbengUnits = [
            // parent_id  => name
            12 => ['KASUBBENG RADIO DIGILOG',         'KASUBBENG RADIODIGILOG', 5],
            13 => ['KASUBBENG ALKOMSAL DAN MULTIMEDIA','KASUBBENG ALKOMSAL',     5],
            14 => ['KASUBBENG ALKOMSAT',               'KASUBBENG ALKOMSAT',     5],
            16 => ['KASUBBENG ALDALLEK',               'KASUBBENG ALDALLEK',     5],
            17 => ['KASUBBENG ALPERNIKA',              'KASUBBENG ALPERNIKA',    5],
            18 => ['KASUBBENG MATINDRALEK',            'KASUBBENG MATINDRALEK',  5],
            19 => ['KASUBBENG MEKATRONIKA',            'KASUBBENG MEKATRONIKA',  5],
            21 => ['KASUBBENG JARKABEL',               'KASUBBENG JARKABEL',     5],
            22 => ['KASUBBENG JARNIRKABEL',            'KASUBBENG JARNIRKABEL',  5],
            24 => ['KASUBBENG INTEGRASI',              'KASUBBENG INTEGRASI',    5],
            30 => ['KASUBBENG TIK',                    'KASUBBENG TIK',          5],
            31 => ['KASUBBENG POWER SYSTEM',           'KASUBBENG POWER SYSTEM', 5],
        ];

        $now = now();
        foreach ($kasubbengUnits as $parentId => [$name, $code, $sort]) {
            // Only insert if not already present
            $exists = DB::table('organization_units')
                ->where('name', $name)
                ->exists();
            if (!$exists) {
                DB::table('organization_units')->insert([
                    'name'       => $name,
                    'code'       => $code,
                    'parent_id'  => $parentId,
                    'sort_order' => $sort,
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Revert renames
        DB::table('organization_units')->where('id', 3)->update(['name' => 'SIPAM',  'code' => null]);
        DB::table('organization_units')->where('id', 4)->update(['name' => 'SIOPS',  'code' => null]);
        DB::table('organization_units')->where('id', 5)->update(['name' => 'SIPERS', 'code' => null]);
        DB::table('organization_units')->where('id', 6)->update(['name' => 'SILOG',  'code' => null]);
        DB::table('organization_units')->where('id', 7)->update([
            'name'      => 'KABAGRENDAL',
            'code'      => 'KABAGRENDAL',
            'parent_id' => 29,
            'sort_order' => 20,
        ]);

        // Remove kasubbeng units
        $names = [
            'KASUBBENG RADIO DIGILOG', 'KASUBBENG ALKOMSAL DAN MULTIMEDIA',
            'KASUBBENG ALKOMSAT', 'KASUBBENG ALDALLEK', 'KASUBBENG ALPERNIKA',
            'KASUBBENG MATINDRALEK', 'KASUBBENG MEKATRONIKA', 'KASUBBENG JARKABEL',
            'KASUBBENG JARNIRKABEL', 'KASUBBENG INTEGRASI', 'KASUBBENG TIK',
            'KASUBBENG POWER SYSTEM',
        ];
        DB::table('organization_units')->whereIn('name', $names)->delete();
    }
};
