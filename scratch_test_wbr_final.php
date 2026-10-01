<?php
$zip = new ZipArchive();
$zip->open('C:/xampp/htdocs/pers_bengpus/storage/app/private/marriage_letters/1/1790730685_SURAT_PERMOHONAN_IZIN_NIKAH_FINAL.docx');
$xml = $zip->getFromName('word/document.xml');
if (strpos($xml, '<w:br/>') !== false) {
    echo "MULTILINE HAS <w:br/>: YA\n";
} else {
    echo "MULTILINE HAS <w:br/>: TIDAK\n";
}
