<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MarriageApplication;
use App\Models\MarriageDocumentType;
use App\Services\MarriageLetterGenerator;

$appModel = MarriageApplication::find(1);
if (!$appModel) {
    echo "No marriage application found.";
    exit;
}

$generator = new MarriageLetterGenerator();
$docType = MarriageDocumentType::where('code', 'SURAT_PERMOHONAN_IZIN_NIKAH')->first();
$templatePath = storage_path('app/' . $docType->template_path);
$outputPath = $generator->generate($appModel, $templatePath, 'TEST_OUTPUT');

echo "Generated: " . $outputPath . "\n";
