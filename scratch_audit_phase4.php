<?php

// script to dump and analyze marriage module
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = [];

function searchFiles($path, $query) {
    $cmd = "findstr /S /I /M \"$query\" " . escapeshellarg(base_path($path . '\\*.php'));
    exec($cmd, $out);
    return array_map('basename', $out);
}

// 1. Models
$models = ['MarriageApplication', 'MarriagePartner', 'MarriageDocument', 'MarriageDocumentType', 'MarriageLetter', 'MarriageStatusHistory', 'Personel', 'User', 'MarriageRequest'];
foreach ($models as $m) {
    if (class_exists("App\\Models\\$m")) {
        $model = new ("App\\Models\\$m");
        $output['models'][$m] = [
            'table' => $model->getTable(),
            'primary_key' => $model->getKeyName(),
            'fillable' => $model->getFillable(),
            'casts' => $model->getCasts()
        ];
    } else {
        $output['models'][$m] = 'NOT FOUND';
    }
}

file_put_contents('scratch_audit_phase4_data.json', json_encode($output, JSON_PRETTY_PRINT));
echo "Audit script created data";
