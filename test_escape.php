<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tp = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/templates/marriage/Surat pengajuan menikah.docx'));
$val = htmlspecialchars("A & B \n C > D", ENT_XML1, 'UTF-8');
$val = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $val);
$tp->setValue('nama_anggota', $val);
$tp->saveAs(storage_path('app/private/test_escape_br.docx'));

$zip = new ZipArchive();
$zip->open(storage_path('app/private/test_escape_br.docx'));
$xml = $zip->getFromName('word/document.xml');
$doc = new DOMDocument();
if (!@$doc->loadXML($xml)) {
    echo "XML is invalid!\n";
} else {
    echo "XML IS VALID!\n";
}
