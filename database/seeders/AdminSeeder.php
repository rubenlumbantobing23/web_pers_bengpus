<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Personel;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@bengpuskomlekad.mil.id'],
            [
                'name' => 'Staf Personalia Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        Personel::updateOrCreate(
            ['nrp_nip' => '1198001002003'],
            [
                'user_id' => $adminUser->id,
                'nama' => 'Mayor Chb Bambang Suryo',
                'pangkat_golongan' => 'Mayor Chb',
                'jabatan' => 'Kasi Personalia',
                'satuan_bagian' => 'Staf Pers Bengpuskomlekad',
                'no_hp' => '081234567890',
                'email' => 'admin@bengpuskomlekad.mil.id',
                'status_aktif' => true,
            ]
        );
    }
}
