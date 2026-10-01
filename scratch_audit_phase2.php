<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MarriageDocumentType;
use Illuminate\Support\Facades\DB;

$out = [];

// 1. INVENTARISASI SEMUA MARRIAGE DOCUMENT TYPE (DATABASE)
$dbDocs = MarriageDocumentType::orderBy('id')->get();
$out['db_docs'] = $dbDocs->toArray();

// 2. INVENTARISASI SEMUA FILE TEMPLATE DOCX
$dirsToSearch = [
    storage_path('app/templates/'),
    storage_path('app/templates/marriage/')
];
$foundFiles = [];
foreach ($dirsToSearch as $dir) {
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'docx') {
                $absPath = $file->getRealPath();
                if (!isset($foundFiles[$absPath])) {
                    $foundFiles[$absPath] = [
                        'filename' => $file->getFilename(),
                        'absolute_path' => $absPath,
                        'filesize' => $file->getSize(),
                        'sha256' => hash_file('sha256', $absPath)
                    ];
                }
            }
        }
    }
}
$out['files'] = array_values($foundFiles);

// 5. EKSTRAK PLACEHOLDER
$placeholders = [];
foreach ($out['files'] as $fileInfo) {
    $absPath = $fileInfo['absolute_path'];
    $zip = new ZipArchive();
    $foundPlaceholders = [];
    if ($zip->open($absPath) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('/^word\/(document|header\d+|footer\d+)\.xml$/', $name)) {
                $xml = $zip->getFromName($name);
                // match ${...}
                // Word sometimes splits text, so we'll strip tags first
                $text = strip_tags($xml);
                if (preg_match_all('/\$\{[^}]+\}/', $text, $matches)) {
                    foreach ($matches[0] as $match) {
                        $foundPlaceholders[] = $match;
                    }
                }
            }
        }
        $zip->close();
    }
    $placeholders[$absPath] = array_values(array_unique($foundPlaceholders));
}
$out['placeholders'] = $placeholders;

// 9. AUDIT SEEDER
$seederPath = base_path('database/seeders/MarriageDocumentTypeSeeder.php');
if (file_exists($seederPath)) {
    $seederContent = file_get_contents($seederPath);
    // basic extraction of arrays
    if (preg_match_all('/\[\s*\'code\'\s*=>\s*\'([^\']+)\',\s*\'name\'\s*=>\s*\'([^\']+)\',\s*\'template_path\'\s*=>\s*\'([^\']+)\'/i', $seederContent, $matches)) {
        $seederDocs = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $seederDocs[] = [
                'code' => $matches[1][$i],
                'name' => $matches[2][$i],
                'template_path' => $matches[3][$i]
            ];
        }
        $out['seeder_docs'] = $seederDocs;
    } else {
        $out['seeder_docs'] = 'Regex match failed or different format';
    }
} else {
    $out['seeder_docs'] = 'Seeder file not found';
}

file_put_contents(__DIR__.'/scratch_audit_data.json', json_encode($out, JSON_PRETTY_PRINT));
echo "Audit data written to scratch_audit_data.json\n";
