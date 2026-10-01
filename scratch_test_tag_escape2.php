<?php
require __DIR__.'/vendor/autoload.php';
$tp = new \PhpOffice\PhpWord\TemplateProcessor('C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx');
$tp->setValue('alamat_anggota', 'TEST</w:t>TEST');
$tp->saveAs('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_tag_escape2.docx');
$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_tag_escape2.docx');
$xml = $zip->getFromName('word/document.xml');
$pos = strpos($xml, 'TEST');
if ($pos !== false) {
    echo htmlspecialchars(substr($xml, max(0, $pos - 20), 100)) . "\n";
} else {
    echo "NOT FOUND\n";
}
