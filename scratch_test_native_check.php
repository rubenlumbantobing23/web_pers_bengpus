<?php
$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_native_newline.docx');
$xml = $zip->getFromName('word/document.xml');
$dom = new DOMDocument();
libxml_use_internal_errors(true);
if (!$dom->loadXML($xml)) {
    echo "INVALID XML\n";
} else {
    echo "VALID XML\n";
}
if (strpos($xml, '<w:br/>') !== false) {
    echo "w:br exists\n";
}
if (strpos($xml, "\n") !== false) {
    echo "literal newline exists\n";
}
