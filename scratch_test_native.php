<?php
require __DIR__.'/vendor/autoload.php';
$tp = new \PhpOffice\PhpWord\TemplateProcessor('C:/xampp/htdocs/pers_bengpus/storage/app/templates/marriage/Surat Permohonan ijin Nikah.docx');
$tp->setValue('alamat_anggota', "A\nB");
$tp->saveAs('C:/xampp/htdocs/pers_bengpus/storage/app/private/test_native_newline.docx');
