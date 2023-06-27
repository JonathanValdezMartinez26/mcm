<?php
// Declaramos la libreria
require __DIR__ . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spread = new Spreadsheet();
$spread
    ->getProperties()
    ->setCreator("BaulPHP")
    ->setLastModifiedBy('Nestor Tapia')
    ->setTitle('Excel creado con PhpSpreadSheet')
    ->setSubject('Excel Demostración')
    ->setDescription('Excel generado como prueba')
    ->setKeywords('PHPSpreadsheet')
    ->setCategory('Categoría de prueba');

$writer = new Xlsx($spread);

# Creamos el archivo y lo guardamos en el disco
$writer->save('./doc_exportados/reporte_2022_01_01.xlsx');
?>