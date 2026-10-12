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
        $adminPassword = env('ADMIN_SETUP_PASSWORD');
        if (empty($adminPassword)) {
            throw new \Exception("ADMIN_SETUP_PASSWORD tidak disetel di file .env. Harap tentukan password admin yang aman sebelum menjalankan seeder ini untuk mencegah password default.");
        }

        User::updateOrCreate(
            ['email' => 'bengpuskomlek042@gmail.com'],
            [
                'name' => 'Staf Personalia',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]
        );
    }
}
