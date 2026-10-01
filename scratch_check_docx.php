<?php

$outputPath = 'C:/xampp/htdocs/pers_bengpus/storage/app/private/test_minimal_out.docx';

$zip = new ZipArchive();
if ($zip->open($outputPath) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    // Check if well formed
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    if ($dom) {
        echo "Valid XML\n";
    } else {
        echo "Invalid XML\n";
    }

    if (strpos($xml, '&lt;w:br/&gt;') !== false) {
        echo "WARNING: Escaped <w:br/> found!\n";
    }
    if (strpos($xml, '<w:br/>') !== false) {
        echo "Found raw <w:br/> tag.\n";
    }
    
} else {
    echo "Cannot open docx as zip.\n";
}
