<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;
$templatePath = 'C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx';
$templateProcessor = new TemplateProcessor($templatePath);
$vars = $templateProcessor->getVariables();
print_r($vars);
