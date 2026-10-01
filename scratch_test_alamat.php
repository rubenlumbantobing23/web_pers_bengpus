<?php

require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

$templatePath = 'C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx';
$outputPath = 'C:/xampp/htdocs/pers_bengpus/storage/app/private/test_alamat_out.docx';

$templateProcessor = new class($templatePath) extends TemplateProcessor {
    public function __construct($documentTemplate)
    {
        parent::__construct($documentTemplate);
        $this->tempDocumentMainPart = str_replace('<w:t>', '<w:t xml:space="preserve">', $this->tempDocumentMainPart);
    }
};

$escapeValue = static function ($value) {
    if ($value === null) return '';
    $value = htmlspecialchars((string) $value, ENT_XML1, 'UTF-8');
    return str_replace(["\r\n", "\r", "\n"], '</w:t><w:br/><w:t xml:space="preserve">', $value);
};

$templateProcessor->setValue('alamat_anggota', $escapeValue("Jalan A\nJalan B"));
$templateProcessor->saveAs($outputPath);

$zip = new ZipArchive();
$zip->open($outputPath);
$xml = $zip->getFromName('word/document.xml');
$zip->close();

if (strpos($xml, '&lt;w:br/&gt;') !== false) {
    echo "WARNING: Escaped <w:br/> found!\n";
}
if (preg_match('/<w:t[^>]*>.*?<w:br\s*\/>.*?<\/w:t>/', $xml)) {
    echo "WARNING: <w:br/> found INSIDE <w:t>\n";
}

$dom = new DOMDocument();
$dom->loadXML($xml);
$formatted = $dom->saveXML();
$pos = strpos($formatted, 'Jalan A');
if ($pos !== false) {
    echo "Context around Jalan A:\n";
    echo substr($formatted, max(0, $pos - 100), 200);
}
