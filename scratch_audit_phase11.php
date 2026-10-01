<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$output = [
    'templates' => [
        'with_path' => [],
        'without_path' => []
    ],
    'foreign_keys' => []
];

// 5. Template Mapping
$docTypes = DB::table('marriage_document_types')->select('id', 'code', 'name', 'template_path')->get();
foreach ($docTypes as $doc) {
    if (!empty($doc->template_path)) {
        $output['templates']['with_path'][] = $doc;
    } else {
        $output['templates']['without_path'][] = $doc;
    }
}

// 6. DB Integrity (simplified logic just to get foreign keys for marriage tables)
$tables = [
    'marriage_applications',
    'marriage_partners',
    'marriage_documents',
    'marriage_document_types',
    'marriage_letters',
    'marriage_status_histories'
];

foreach ($tables as $table) {
    try {
        $fks = DB::select("
            SELECT 
                CONSTRAINT_NAME, 
                COLUMN_NAME, 
                REFERENCED_TABLE_NAME, 
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = 'bengpus_pers'
              AND TABLE_NAME = ?
        ", [$table]);
        $output['foreign_keys'][$table] = $fks;
    } catch (\Exception $e) {}
}

file_put_contents('scratch_audit_phase11.json', json_encode($output, JSON_PRETTY_PRINT));
echo "DONE\n";
