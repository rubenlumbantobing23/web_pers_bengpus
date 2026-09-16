<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Personel;
use App\Models\LeaveEntitlement;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'user@bengpuskomlekad.mil.id'],
            [
                'name' => 'Serka Ahmad Dahlan',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );

        Personel::updateOrCreate(
            ['nrp_nip' => '211204567890'],
            [
                'user_id' => $user->id,
                'nama' => 'Serka Ahmad Dahlan',
                'pangkat_golongan' => 'Serka',
                'jabatan' => 'Bintara Komunikasi',
                'satuan_bagian' => 'Bengkel Komunikasi',
                'no_hp' => '082198765432',
                'email' => 'user@bengpuskomlekad.mil.id',
                'status_aktif' => true,
            ]
        );

        LeaveEntitlement::updateOrCreate(
            [
                'user_id' => $user->id,
                'year' => date('Y'),
            ],
            [
                'total_days' => 12,
            ]
        );
    }
}
