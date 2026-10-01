<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LetterType;

class LetterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'code' => 'SPRIN',
                'name' => 'SPRIN / Surat Perintah',
                'sort_order' => 1,
            ],
            [
                'code' => 'SURAT_BIASA',
                'name' => 'Surat Biasa',
                'sort_order' => 2,
            ],
            [
                'code' => 'SURAT_TELEGRAM',
                'name' => 'Surat Telegram',
                'sort_order' => 3,
            ],
            [
                'code' => 'SURAT_PENGANTAR',
                'name' => 'Surat Pengantar',
                'sort_order' => 4,
            ],
            [
                'code' => 'SURAT_KETERANGAN',
                'name' => 'Surat Keterangan',
                'sort_order' => 5,
            ],
        ];

        foreach ($types as $type) {
            $letterType = LetterType::updateOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'sort_order' => $type['sort_order'],
                    'is_active' => true,
                ]
            );

            // Optional safe mapping for existing data
            if ($type['code'] === 'SPRIN') {
                \App\Models\InternalLetter::whereNull('letter_type_id')
                    ->where(function($query) {
                        $query->where('category', 'like', '%Sprit%')
                              ->orWhere('category', 'like', '%Surat Perintah%');
                    })
                    ->update([
                        'letter_type_id' => $letterType->id,
                    ]);
            }
        }
    }
}
