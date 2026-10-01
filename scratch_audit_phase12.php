<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

$output = [
    'templates' => [],
    'unique_placeholders' => []
];

$docTypes = DB::table('marriage_document_types')
    ->select('id', 'code', 'name', 'template_path')
    ->whereNotNull('template_path')
    ->get();

foreach ($docTypes as $doc) {
    $fullPath = storage_path('app/' . $doc->template_path);
    if (!file_exists($fullPath)) {
        $output['templates'][$doc->code] = [
            'status' => 'FILE_NOT_FOUND',
            'path' => $doc->template_path
        ];
        continue;
    }
    
    try {
        $templateProcessor = new TemplateProcessor($fullPath);
        $variables = $templateProcessor->getVariables();
        
        $varCounts = array_count_values($variables);
        
        $output['templates'][$doc->code] = [
            'status' => 'OK',
            'path' => $doc->template_path,
            'variables' => array_keys($varCounts),
            'counts' => $varCounts,
            'total_unique' => count($varCounts),
            'total_occurrences' => count($variables)
        ];
        
        foreach ($varCounts as $var => $count) {
            if (!isset($output['unique_placeholders'][$var])) {
                $output['unique_placeholders'][$var] = [
                    'templates' => [],
                    'total_occurrences' => 0
                ];
            }
            $output['unique_placeholders'][$var]['templates'][] = $doc->code;
            $output['unique_placeholders'][$var]['total_occurrences'] += $count;
        }
        
    } catch (\Exception $e) {
        $output['templates'][$doc->code] = [
            'status' => 'ERROR',
            'message' => $e->getMessage()
        ];
    }
}

file_put_contents('scratch_audit_phase12.json', json_encode($output, JSON_PRETTY_PRINT));
echo "DONE\n";
