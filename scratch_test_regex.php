<?php
$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_alamat_out.docx');
$xml = $zip->getFromName('word/document.xml');

// Regex: <w:t ...> then any characters EXCEPT <, then <w:br/>, then any characters EXCEPT <, then </w:t>
if (preg_match('/<w:t[^>]*>[^<]*<w:br\s*\/>[^<]*<\/w:t>/', $xml)) {
    echo "YES, w:br inside w:t\n";
} else {
    echo "NO w:br inside w:t\n";
}

$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_raw_wbr.docx');
$xml = $zip->getFromName('word/document.xml');
if (preg_match('/<w:t[^>]*>[^<]*<w:br\s*\/>[^<]*<\/w:t>/', $xml)) {
    echo "RAW: YES, w:br inside w:t\n";
} else {
    echo "RAW: NO w:br inside w:t\n";
}
