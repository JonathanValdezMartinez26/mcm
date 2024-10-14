<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;

class PHPSpreadsheet
{
    public static function ColumnaExcel($letra, $campo, $titulo = '', $estilo = [])
    {
        $titulo = $titulo == '' ? $campo : $titulo;

        return [
            'letra' => $letra,
            'campo' => $campo,
            'estilo' => $estilo,
            'titulo' => $titulo
        ];
    }

    public static function GetEstilosExcel()
    {
        return [
            'titulo' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Style\Border::BORDER_THIN]
                ]
            ],
            'centrado' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER]
            ],
            'moneda' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_RIGHT],
                'numberFormat' => ['formatCode' => Style\NumberFormat::FORMAT_CURRENCY_SIMPLE]
            ],
            'fecha' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER],
                'numberFormat' => ['formatCode' => Style\NumberFormat::FORMAT_DATE_DDMMYYYY]
            ],
            'fecha_hora' => [
                'alignment' => ['horizontal' =>  Style\Alignment::HORIZONTAL_CENTER],
                'numberFormat' => ['formatCode' => Style\NumberFormat::FORMAT_DATE_DATETIME]
            ],
        ];
    }

    public static function GeneraExcel($nombre_archivo, $nombre_hoja, $titulo_reporte, $columnas, $filas)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($nombre_hoja);
    
        // Título del reporte
        $sheet->setCellValue('A1', $titulo_reporte);
        $sheet->mergeCells('A1:' . $columnas[count($columnas) - 1]['letra'] . '1');
        $sheet->getStyle('A1')->applyFromArray(self::GetEstilosExcel()['titulo']);
    
        // Encabezados de columna
        foreach ($columnas as $key => $columna) {
            $sheet->setCellValue($columna['letra'] . '2', $columna['titulo']);
            $sheet->getStyle($columna['letra'] . '2')->applyFromArray(self::GetEstilosExcel()['titulo']);
            $sheet->getColumnDimension($columna['letra'])->setAutoSize(true);
        }
    
        // Filas de datos
        $noFila = 3;
        foreach ($filas as $key => $fila) {
            if ($noFila % 2 == 0) {
                $sheet->getStyle('A' . $noFila . ':' . $columnas[count($columnas) - 1]['letra'] . $noFila)
                      ->getFill()
                      ->setFillType(Style\Fill::FILL_SOLID)
                      ->getStartColor()
                      ->setRGB('F0F0F0');
            }
    
            foreach ($columnas as $key => $columna) {
                $estiloCelda = $columna['estilo'];
                $estiloCelda['borders']['left']['borderStyle'] = Style\Border::BORDER_THIN;
                $estiloCelda['borders']['right']['borderStyle'] = Style\Border::BORDER_THIN;
    
                $sheet->setCellValue($columna['letra'] . $noFila, html_entity_decode($fila[$columna['campo']], ENT_QUOTES, "UTF-8"));
                $sheet->getStyle($columna['letra'] . $noFila)->applyFromArray($estiloCelda);
            }
    
            $noFila += 1;
        }
    
        // Seleccionar celda A1, congelar fila 3 y aplicar filtro a las columnas
        $sheet->setSelectedCell('A1');
        $sheet->freezePane('A3');
        $sheet->setAutoFilter('A2:' . $columnas[count($columnas) - 1]['letra'] . '2');
    
        // Configuración de encabezados HTTP
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombre_archivo . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Pragma: public');
    
        // Guardar el archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}