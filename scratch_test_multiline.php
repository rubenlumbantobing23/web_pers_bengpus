<?php

require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

$templatePath = 'C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx';
$outputPath = 'C:/xampp/htdocs/pers_bengpus/storage/app/private/test_multiline_out.docx';

$templateProcessor = new class($templatePath) extends TemplateProcessor {
    public function __construct($documentTemplate)
    {
        parent::__construct($documentTemplate);
        $this->tempDocumentMainPart = str_replace('<w:t>', '<w:t xml:space="preserve">', $this->tempDocumentMainPart);
        foreach ($this->tempDocumentHeaders as $index => $xml) {
            $this->tempDocumentHeaders[$index] = str_replace('<w:t>', '<w:t xml:space="preserve">', $xml);
        }
        foreach ($this->tempDocumentFooters as $index => $xml) {
            $this->tempDocumentFooters[$index] = str_replace('<w:t>', '<w:t xml:space="preserve">', $xml);
        }
    }
};

$escapeValue = static function ($value) {
    if ($value === null) return '';
    $value = htmlspecialchars((string) $value, ENT_XML1, 'UTF-8');
    return str_replace(["\r\n", "\r", "\n"], '</w:t><w:br/><w:t xml:space="preserve">', $value);
};

$templateProcessor->setValue('nama_anggota', $escapeValue('Budi Santoso'));
$templateProcessor->setValue('jabatan_pejabat', $escapeValue("Baris 1\nBaris 2"));
$templateProcessor->saveAs($outputPath);

echo "Multiline generation complete.\n";

$zip = new ZipArchive();
if ($zip->open($outputPath) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    // Check if well formed
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    if (empty(libxml_get_errors())) {
        echo "Valid XML\n";
    } else {
        echo "Invalid XML\n";
        foreach(libxml_get_errors() as $err) {
            echo $err->message . "\n";
        }
    }
    libxml_clear_errors();

    if (strpos($xml, '&lt;w:br/&gt;') !== false) {
        echo "WARNING: Escaped <w:br/> found!\n";
    }
    
    // Check if <w:br/> is directly inside <w:t> which is invalid
    if (preg_match('/<w:t[^>]*>.*?<w:br\s*\/>.*?<\/w:t>/', $xml)) {
        echo "WARNING: <w:br/> found INSIDE <w:t>\n";
    }

}
