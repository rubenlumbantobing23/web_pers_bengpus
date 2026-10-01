<?php

require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$templatePath = 'C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx';
$outputPath = 'C:/xampp/htdocs/pers_bengpus/storage/app/private/test_minimal_out.docx';

$templateProcessor = new TemplateProcessor($templatePath);
$templateProcessor->setValue('nama_anggota', 'Budi Santoso');
$templateProcessor->saveAs($outputPath);

echo "Minimal generation complete.\n";
echo "Output size: " . filesize($outputPath) . "\n";
