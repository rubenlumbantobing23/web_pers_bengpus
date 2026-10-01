<?php
$xml = file_get_contents('C:\xampp\htdocs\pers_bengpus\storage\app\private\test_multiline_out\word\document.xml');
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml);
$formatted = $dom->saveXML();

if (strpos($xml, '&lt;w:br/&gt;') !== false) {
    echo "FOUND: &lt;w:br/&gt;\n";
}
if (strpos($xml, 'Baris 1') !== false) {
    echo "FOUND: Baris 1\n";
}
if (strpos($xml, '&lt;/w:t&gt;') !== false) {
    echo "FOUND: &lt;/w:t&gt;\n";
}
$pos = strpos($formatted, 'Baris 1');
if ($pos !== false) {
    echo "Context around Baris 1:\n";
    echo substr($formatted, max(0, $pos - 200), 400);
} else {
    echo "Baris 1 not found in formatted either.\n";
    // Check if the original placeholder is still there
    if (strpos($formatted, 'jabatan_pejabat') !== false) {
        echo "jabatan_pejabat placeholder is still there.\n";
    }
}
