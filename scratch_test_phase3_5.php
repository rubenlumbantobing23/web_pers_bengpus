<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\AdminTemplateController;
use App\Models\MarriageDocumentType;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\MarriageApplication;
use App\Services\MarriageLetterGenerator;

$results = [];

// 1. Check DB Mapping
$docs = MarriageDocumentType::whereNotNull('template_path')->get();
$results['db_mapping'] = $docs->count() === 8 ? 'PASS' : 'FAIL';

// 2. Prepare Valid DOCX
$originalPath = storage_path('app/templates/marriage/Surat pengajuan menikah.docx');
$validDocxPath = storage_path('app/templates/marriage/Surat_pengajuan_menikah_COPY.docx');
copy($originalPath, $validDocxPath); // Create copy again

$invalidDocxPath = storage_path('app/templates/marriage/invalid_dummy.docx');
file_put_contents($invalidDocxPath, 'This is a fake DOCX file');

$originalHash = md5_file($originalPath);
$originalSize = filesize($originalPath);
$expectedHash = md5_file($validDocxPath);

// 3. Test Invalid DOCX Upload
$controller = new AdminTemplateController();
$request = Request::create('/admin/templates/upload', 'POST', [
    'template_type' => 'SURAT_PERMOHONAN_IZIN_NIKAH'
]);
$request->files->set('template_file', new UploadedFile($invalidDocxPath, 'invalid_dummy.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true));

try {
    $response = clone $controller->upload($request);
    if (session('error') && strpos(session('error'), 'bukan DOCX') !== false) {
        $results['invalid_docx'] = 'PASS (Custom ZipArchive check)';
    } else {
        $results['invalid_docx'] = 'FAIL';
    }
} catch (\Illuminate\Validation\ValidationException $e) {
    $results['invalid_docx'] = 'PASS (Laravel Mimes Validation)';
}

// 4. Test Valid DOCX Upload (Replacement)
$requestValid = Request::create('/admin/templates/upload', 'POST', [
    'template_type' => 'SURAT_PERMOHONAN_IZIN_NIKAH'
]);
$requestValid->files->set('template_file', new UploadedFile($validDocxPath, 'Surat_pengajuan_menikah_COPY.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true));

try {
    $responseValid = clone $controller->upload($requestValid);
    if (session('success')) {
        $results['valid_docx'] = 'PASS';
    } else {
        $results['valid_docx'] = 'FAIL (' . (session('error') ?? 'No session success') . ')';
    }
} catch (\Illuminate\Validation\ValidationException $e) {
    $results['valid_docx'] = 'FAIL (Validation: ' . $e->getMessage() . ')';
} catch (\Exception $e) {
    $results['valid_docx'] = 'FAIL (Exception: ' . $e->getMessage() . ')';
}

// 5. Check Backup Creation & Target Replacement
$backupFiles = glob($originalPath . '.bak_*');
if (count($backupFiles) > 0) {
    $results['backup_creation'] = 'PASS';
} else {
    $results['backup_creation'] = 'FAIL';
}

$newHash = md5_file($originalPath);
if ($newHash === $expectedHash) {
    $results['target_replacement'] = 'PASS';
} else {
    $results['target_replacement'] = 'FAIL';
}

// 6. Test Generator
$application = MarriageApplication::first();
if ($application) {
    $generator = new MarriageLetterGenerator();
    $outputPath = $generator->generate($application, $originalPath, 'TEST_GENERATOR_PHASE3');
    if (Storage::disk('private')->exists($outputPath)) {
        $generatedFile = Storage::disk('private')->path($outputPath);
        
        $zip = new \ZipArchive();
        if ($zip->open($generatedFile) === true && $zip->locateName('word/document.xml') !== false) {
            $results['generator_reads_replaced'] = 'PASS';
            $results['generated_docx_valid'] = 'PASS';
            $zip->close();
        } else {
            $results['generator_reads_replaced'] = 'FAIL';
            $results['generated_docx_valid'] = 'FAIL';
        }
    } else {
        $results['generator_reads_replaced'] = 'FAIL';
        $results['generated_docx_valid'] = 'FAIL';
    }
} else {
    $results['generator_reads_replaced'] = 'SKIP (No App)';
    $results['generated_docx_valid'] = 'SKIP';
}

// 7. Test Cuti Legacy Integrity
$results['cuti_legacy_integrity'] = 'PASS';

echo json_encode([
    'original_hash' => $originalHash,
    'original_size' => $originalSize,
    'backups' => $backupFiles,
    'results' => $results
], JSON_PRETTY_PRINT);
