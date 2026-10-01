<?php
$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_tag.docx');
$xml = $zip->getFromName('word/document.xml');
$pos = strpos($xml, 'w:t&gt;');
if ($pos !== false) {
    echo "ESCAPED TAG FOUND\n";
}
$pos = strpos($xml, 'alamat');
if ($pos !== false) {
    echo "ALAMAT: " . htmlspecialchars(substr($xml, max(0, $pos - 40), 100)) . "\n";
} else {
    echo "ALAMAT NOT FOUND\n";
}
