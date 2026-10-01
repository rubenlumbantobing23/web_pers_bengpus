<?php
$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_tag.docx');
$xml = $zip->getFromName('word/document.xml');
$pos = strpos($xml, '&lt;/w:t&gt;');
if ($pos !== false) {
    echo "ESCAPED TAG FOUND: " . htmlspecialchars(substr($xml, max(0, $pos - 20), 100)) . "\n";
} else {
    echo "ESCAPED TAG NOT FOUND\n";
}
$pos = strpos($xml, '</w:t>');
if ($pos !== false) {
    echo "RAW TAG FOUND: " . htmlspecialchars(substr($xml, max(0, $pos - 20), 100)) . "\n";
}
