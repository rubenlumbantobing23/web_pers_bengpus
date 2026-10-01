<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$output = [
    'classes' => [],
    'counts' => []
];

$classes_to_check = [
    'App\Models\MarriageApplication',
    'App\Models\MarriagePartner',
    'App\Models\MarriageDocument',
    'App\Models\MarriageDocumentType',
    'App\Models\MarriageLetter',
    'App\Models\MarriageStatusHistory',
    'App\Services\MarriageLetterGenerator',
    'App\Http\Controllers\User\MarriageApplicationController',
    'App\Http\Controllers\Admin\AdminMarriageApplicationController'
];

foreach ($classes_to_check as $cls) {
    $output['classes'][$cls] = class_exists($cls) ? 'FOUND' : 'NOT FOUND';
}

$tables = [
    'marriage_requests',
    'marriage_request_documents',
    'marriage_applications',
    'marriage_documents',
    'marriage_document_types',
    'marriage_letters',
    'marriage_status_histories'
];

foreach ($tables as $table) {
    try {
        $output['counts'][$table] = DB::table($table)->count();
    } catch (\Exception $e) {
        $output['counts'][$table] = 'ERROR: ' . $e->getMessage();
    }
}

echo json_encode($output, JSON_PRETTY_PRINT);
