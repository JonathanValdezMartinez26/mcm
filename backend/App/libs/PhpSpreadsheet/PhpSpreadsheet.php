<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;

class PHPSpreadsheet
{
    /**
     * Genera una configuración de columna para Excel.
     *
     * @param string $letra La letra de la columna en Excel.
     * @param string $campo El nombre del campo asociado a la columna.
     * @param string $titulo (Opcional) El título de la columna. Si no se proporciona, se usará el nombre del campo.
     * @param array $estilo (Opcional) Un arreglo asociativo con los estilos aplicables a la columna.
     *
     * @return array Un arreglo con la configuración de la columna, incluyendo la letra, el campo, el estilo y el título.
     */
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

    /**
     * Obtiene los estilos predefinidos para Excel.
     *
     * @return array Un arreglo asociativo que contiene los estilos predefinidos:
     *               - 'titulo': Estilo para títulos, con fuente en negrita, alineación centrada y bordes delgados.
     *               - 'centrado': Estilo con alineación centrada.
     *               - 'moneda': Estilo para celdas de moneda, con alineación a la derecha y formato de número de moneda simple.
     *               - 'fecha': Estilo para celdas de fecha, con alineación centrada y formato de fecha DD/MM/YYYY.
     *               - 'fecha_hora': Estilo para celdas de fecha y hora, con alineación centrada y formato de fecha y hora.
     */
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

    /**
     * Genera un archivo Excel con los datos proporcionados.
     *
     * @param string $nombre_archivo El nombre del archivo Excel a generar.
     * @param string $nombre_hoja El nombre de la hoja dentro del archivo Excel.
     * @param string $titulo_reporte El título del reporte que se mostrará en la primera fila.
     * @param array $columnas Un arreglo asociativo que define las columnas del reporte. Cada elemento debe contener:
     *                        - 'letra': La letra de la columna en Excel.
     *                        - 'titulo': El título de la columna.
     *                        - 'campo': El nombre del campo en los datos de las filas.
     *                        - 'estilo': Un arreglo con los estilos a aplicar a la celda.
     * @param array $filas Un arreglo de arreglos asociativos que contiene los datos a mostrar en el reporte. Cada elemento debe ser un arreglo asociativo donde las claves son los nombres de los campos definidos en $columnas.
     *
     * @return void
     */
    public static function GeneraExcel($nombre_archivo, $nombre_hoja, $titulo_reporte, $columnas, $filas)
    {
        $filaInicio = 3;
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
        $noFila = $filaInicio;
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

        // Poner el borde inferior a la última fila
        $sheet->getStyle('A' . ($noFila - 1) . ':' . $columnas[count($columnas) - 1]['letra'] . ($noFila - 1))
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Style\Border::BORDER_THIN);

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
