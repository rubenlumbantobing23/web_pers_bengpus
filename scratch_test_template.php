<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$doc = App\Models\MarriageDocumentType::where('code', 'SURAT_PERMOHONAN_IZIN_NIKAH')->first();
if ($doc) {
    echo json_encode(['path' => $doc->template_path]) . "\n";
    $absPath = storage_path('app/' . $doc->template_path);
    echo "Absolute Path: " . $absPath . "\n";
    if (file_exists($absPath)) {
        echo "Size: " . filesize($absPath) . " bytes\n";
        echo "SHA256: " . hash_file('sha256', $absPath) . "\n";
    } else {
        echo "File not found at absolute path.\n";
    }
} else {
    echo "Not found\n";
}
