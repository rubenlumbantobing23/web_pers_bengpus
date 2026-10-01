<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\Storage::disk('public')->put('internal_letters/dummy_biasa.txt', 'Isi Surat Biasa');
$letterBiasa = \App\Models\InternalLetter::create([
    'letter_number' => 'B-01/I/2026',
    'letter_date' => now(),
    'category' => 'Surat Biasa',
    'letter_type_id' => 2,
    'direction' => 'MASUK',
    'classification' => 'BIASA',
    'subject' => 'Test Biasa',
    'file_path' => 'internal_letters/dummy_biasa.txt',
    'file_size' => 15,
    'uploader_id' => 1
]);

Illuminate\Support\Facades\Storage::disk('local')->put('internal_letters_private/dummy_rahasia.txt', 'Isi Surat Rahasia');
$letterRahasia = \App\Models\InternalLetter::create([
    'letter_number' => 'R-01/I/2026',
    'letter_date' => now(),
    'category' => 'Surat Perintah (Sprit)',
    'letter_type_id' => 1,
    'direction' => 'MASUK',
    'classification' => 'RAHASIA',
    'subject' => 'Test Rahasia',
    'file_path' => 'internal_letters_private/dummy_rahasia.txt',
    'file_size' => 17,
    'uploader_id' => 1
]);

echo 'Biasa ID: ' . $letterBiasa->id . ' | Rahasia ID: ' . $letterRahasia->id . PHP_EOL;
