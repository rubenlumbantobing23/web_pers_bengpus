<?php
$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_native_newline.docx');
$xml = $zip->getFromName('word/document.xml');
$pos = strpos($xml, ': A');
if ($pos !== false) {
    echo htmlspecialchars(substr($xml, max(0, $pos - 50), 150)) . "\n";
} else {
    echo "NOT FOUND\n";
}
