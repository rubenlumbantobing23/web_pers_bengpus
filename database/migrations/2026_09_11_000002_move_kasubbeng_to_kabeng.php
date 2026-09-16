<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Move KASUBBENG units from being children of SUBBENG
     * to being direct children of their respective KABENG.
     *
     * This makes the org chart show:
     *   KABENG SISKOM → KASUBBENG RADIO DIGILOG, KASUBBENG ALKOMSAL, ...
     * instead of:
     *   KABENG SISKOM → SUBBENG RADIO DIGILOG → KASUBBENG RADIO DIGILOG
     */
    public function up(): void
    {
        // Map: KASUBBENG name => new parent_id (the KABENG unit)
        //   KABENG SISKOM      = 11  (subbeng: 12,13,14)
        //   KABENG SISLEK      = 15  (subbeng: 16,17,18,19)
        //   KABENG JARINGAN    = 20  (subbeng: 21,22,30)
        //   KABENG INTEGRASI   = 23  (subbeng: 24,31)

        $moves = [
            // name                                      => new parent (KABENG id)
            'KASUBBENG RADIO DIGILOG'              => 11,
            'KASUBBENG ALKOMSAL DAN MULTIMEDIA'    => 11,
            'KASUBBENG ALKOMSAT'                   => 11,
            'KASUBBENG ALDALLEK'                   => 15,
            'KASUBBENG ALPERNIKA'                  => 15,
            'KASUBBENG MATINDRALEK'                => 15,
            'KASUBBENG MEKATRONIKA'                => 15,
            'KASUBBENG JARKABEL'                   => 20,
            'KASUBBENG JARNIRKABEL'                => 20,
            'KASUBBENG TIK'                        => 20,
            'KASUBBENG INTEGRASI'                  => 23,
            'KASUBBENG POWER SYSTEM'               => 23,
        ];

        // Set sort_order for each kasubbeng so they appear in order
        $sortOrders = [
            'KASUBBENG RADIO DIGILOG'           => 10,
            'KASUBBENG ALKOMSAL DAN MULTIMEDIA'  => 20,
            'KASUBBENG ALKOMSAT'                 => 30,
            'KASUBBENG ALDALLEK'                 => 10,
            'KASUBBENG ALPERNIKA'                => 20,
            'KASUBBENG MATINDRALEK'              => 30,
            'KASUBBENG MEKATRONIKA'              => 40,
            'KASUBBENG JARKABEL'                 => 10,
            'KASUBBENG JARNIRKABEL'              => 20,
            'KASUBBENG TIK'                      => 30,
            'KASUBBENG INTEGRASI'                => 10,
            'KASUBBENG POWER SYSTEM'             => 20,
        ];

        foreach ($moves as $name => $newParent) {
            DB::table('organization_units')
                ->where('name', $name)
                ->update([
                    'parent_id'  => $newParent,
                    'sort_order' => $sortOrders[$name] ?? 10,
                ]);
        }
    }

    public function down(): void
    {
        // Revert: put KASUBBENG back under their original SUBBENG
        $revert = [
            'KASUBBENG RADIO DIGILOG'           => 12,
            'KASUBBENG ALKOMSAL DAN MULTIMEDIA'  => 13,
            'KASUBBENG ALKOMSAT'                 => 14,
            'KASUBBENG ALDALLEK'                 => 16,
            'KASUBBENG ALPERNIKA'                => 17,
            'KASUBBENG MATINDRALEK'              => 18,
            'KASUBBENG MEKATRONIKA'              => 19,
            'KASUBBENG JARKABEL'                 => 21,
            'KASUBBENG JARNIRKABEL'              => 22,
            'KASUBBENG TIK'                      => 30,
            'KASUBBENG INTEGRASI'                => 24,
            'KASUBBENG POWER SYSTEM'             => 31,
        ];

        foreach ($revert as $name => $oldParent) {
            DB::table('organization_units')
                ->where('name', $name)
                ->update(['parent_id' => $oldParent, 'sort_order' => 5]);
        }
    }
};
