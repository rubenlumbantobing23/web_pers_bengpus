<?php
$xml = file_get_contents('C:\xampp\htdocs\pers_bengpus\storage\app\private\test_multiline_out\word\document.xml');
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml);
$formatted = $dom->saveXML();

$pos = strpos($formatted, 'Budi');
if ($pos !== false) {
    echo "Context around Budi:\n";
    echo substr($formatted, max(0, $pos - 100), 200);
} else {
    echo "Budi not found.\n";
}
