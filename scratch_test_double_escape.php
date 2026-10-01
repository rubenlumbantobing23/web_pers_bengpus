<?php
require __DIR__.'/vendor/autoload.php';
$tp = new \PhpOffice\PhpWord\TemplateProcessor('C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx');
$val = 'A < B & C';
$escaped = htmlspecialchars($val, ENT_XML1, 'UTF-8');
$tp->setValue('alamat_anggota', $escaped);
$tp->saveAs('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_double_escape.docx');

$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_double_escape.docx');
$xml = $zip->getFromName('word/document.xml');
$pos = strpos($xml, ': A');
if ($pos !== false) {
    echo htmlspecialchars(substr($xml, max(0, $pos - 50), 150)) . "\n";
} else {
    echo "NOT FOUND\n";
}
