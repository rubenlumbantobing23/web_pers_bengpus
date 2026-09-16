<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Personel;
use Carbon\Carbon;

class FixShiftedPersonelData extends Command
{
    protected $signature = 'fix:personel-data';
    protected $description = 'Fix shifted personel data from legacy import';

    public function handle()
    {
        $personels = Personel::all();
        $count = 0;
        foreach($personels as $p) {
            if(strpos($p->agama_suku, "\n") !== false) {
                $parts = explode("\n", $p->agama_suku, 2);
                $p->agama_suku = $parts[0];
                try { 
                    $p->tgl_lahir = \Carbon\Carbon::parse(trim($parts[1]))->format('Y-m-d'); 
                } catch(\Exception $e) {
                    $p->tgl_lahir = null;
                }
                $p->ket = $p->pendidikan_lanjutan;
                $p->pendidikan_lanjutan = $p->thn_lulus_dikmit;
                $p->thn_lulus_dikmit = $p->dikmit_tni;
                $p->dikmit_tni = $p->thn_lulus_dikum;
                $p->thn_lulus_dikum = $p->dikum_ti;
                $p->dikum_ti = $p->jenis_kelamin;
                $p->jenis_kelamin = $p->mkg;
                $p->mkg = $p->tempat_lahir;
                $p->tempat_lahir = null;
                $p->save();
                $count++;
            }
        }
        $this->info("Fixed {$count} records.");
    }
}
