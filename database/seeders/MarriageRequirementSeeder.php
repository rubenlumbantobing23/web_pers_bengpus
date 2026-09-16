<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarriageRequirement;

class MarriageRequirementSeeder extends Seeder
{
    public function run(): void
    {
        $requirements = [
            ['title' => 'Surat Permohonan Izin Nikah dari Anggota', 'description' => 'Disetujui atasan langsung', 'is_required' => true],
            ['title' => 'Surat Keterangan N1, N2, N4 dari Kelurahan/Desa', 'description' => 'Surat persetujuan dan asal usul calon mempelai', 'is_required' => true],
            ['title' => 'Surat Keterangan Belum Pernah Menikah (SKBM)', 'description' => 'Dari Kelurahan/Desa setempat', 'is_required' => true],
            ['title' => 'SKCK Calon Suami/Istri', 'description' => 'Surat Keterangan Catatan Kepolisian', 'is_required' => true],
            ['title' => 'Surat Keterangan Kesehatan & Imunisasi TT', 'description' => 'Dari Puskesmas / Rumah Sakit Tentara', 'is_required' => true],
            ['title' => 'Pas Foto Bersama Pakaian Dinas Harian (PDH) (Ukuran 4x6, 6 lembar)', 'description' => 'Latar belakang merah', 'is_required' => true],
            ['title' => 'Fotokopi KTP, KK, & Akta Kelahiran Calon Mempelai', 'description' => 'Masing-masing 2 lembar', 'is_required' => true],
        ];

        foreach ($requirements as $req) {
            MarriageRequirement::updateOrCreate(
                ['title' => $req['title']],
                [
                    'description' => $req['description'],
                    'is_required' => $req['is_required'],
                    'is_active' => true,
                ]
            );
        }
    }
}
