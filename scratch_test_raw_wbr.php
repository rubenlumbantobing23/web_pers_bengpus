<?php
require __DIR__.'/vendor/autoload.php';
$tp = new \PhpOffice\PhpWord\TemplateProcessor('C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx');
$tp->setValue('alamat_anggota', 'A<w:br/>B');
$tp->saveAs('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_raw_wbr.docx');

$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_raw_wbr.docx');
$xml = $zip->getFromName('word/document.xml');
echo (preg_match('/<w:t[^>]*>.*?<w:br\s*\/>.*?<\/w:t>/', $xml) ? 'YES' : 'NO') . "\n";
$dom = new DOMDocument();
libxml_use_internal_errors(true);
if (!$dom->loadXML($xml)) {
    echo "INVALID XML\n";
} else {
    echo "VALID XML\n";
}
