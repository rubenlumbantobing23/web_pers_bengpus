<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$output = [];

try {
    $output['counts']['marriage_requests'] = DB::table('marriage_requests')->count();
} catch (\Exception $e) {
    $output['counts']['marriage_requests'] = 'TABLE NOT FOUND';
}

try {
    $output['counts']['marriage_request_documents'] = DB::table('marriage_request_documents')->count();
} catch (\Exception $e) {
    $output['counts']['marriage_request_documents'] = 'TABLE NOT FOUND';
}

try {
    $output['counts']['marriage_applications'] = DB::table('marriage_applications')->count();
} catch (\Exception $e) {
    $output['counts']['marriage_applications'] = 'TABLE NOT FOUND';
}

try {
    $output['counts']['marriage_documents'] = DB::table('marriage_documents')->count();
} catch (\Exception $e) {
    $output['counts']['marriage_documents'] = 'TABLE NOT FOUND';
}

echo json_encode($output, JSON_PRETTY_PRINT);
