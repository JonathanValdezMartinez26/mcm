<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PHPSpreadsheet
{
    private const formatoMoneda = '"$"#,##0.00';
    private const formatoPorcentaje = '0.00%';
    private const formatoFecha = 'dd/mm/yyyy';
    private const formatoFechaHora = 'dd/mm/yyyy hh:mm:ss';

    /**
     * ColumnaExcel
     * 
     * Genera una configuración de columna para Excel.
     *
     * @param string $campo El nombre del campo asociado a la columna.
     * @param string|mixed $titulo (Opcional) El título de la columna. Si no se proporciona, se usará el nombre del campo. Si es un arreglo, se considera que la columna tiene subcolumnas.
     * @param array $configuracion (Opcional) Un arreglo con la configuración adicional de la columna:
     * - 'letra': La letra de la columna en la hoja de cálculo.
     * - 'estilo': Un arreglo con los estilos de la celda.
     * - 'total': Indica si la columna se incluirá en los totales.
     *
     * @return array Un array con la configuración de la columna, incluyendo la letra, el campo, el estilo, el título y si es un total.
     */
    public static function ColumnaExcel($campo, $titulo = '', $configuracion = [])
    {
        $defecto = ['letra' => '', 'estilo' => [], 'total' => false];
        $configuracion = array_merge($defecto, $configuracion);

        $titulo = $titulo === '' ? $campo : $titulo;

        return [
            'campo' => $campo,
            'titulo' => $titulo,
            'estilo' => $configuracion['estilo'],
            'letra' => $configuracion['letra'],
            'total' => $configuracion['total'],
        ];
    }

    /**
     * GetEstilosExcel
     * 
     * Este método devuelve un array de estilos predefinidos para hojas de Excel.
     * Los estilos incluyen:
     * - 'titulo': Fuente con tamaño 12, en negrita y color blanco con fondo negro.
     * - 'encabezado': Fuente en negrita y fondo gris claro.
     * - 'centrado': Alineación de texto centrada.
     * - 'fecha': Alineación centrada con un formato de fecha (DD/MM/YYYY).
     * - 'fecha_hora': Alineación centrada con un formato de fecha y hora (DD/MM/YYYY HH:MM:SS).
     * - 'moneda': Alineación a la derecha con un formato de moneda simple ($1,000.00).
     * - 'porcentaje': Alineación centrada con el formato de porcentaje (0.00%).
     * - 'texto_centrado': Forza la interpretacion del valor como si fuera texto y pone la alineación centrada
     * - 'texto_izquierda': Forza la interpretacion del valor como si fuera texto y pone la alineación a la izquierda
     * - 'texto_derecha': Forza la interpretacion del valor como si fuera texto y pone la alineación a la derecha
     * 
     * @param string Estilo unico deseado.
     * 
     * @return array Un array con la configuración del estilo seleccionado.
     */
    public static function GetEstilosExcel($estilo = null)
    {
        $estilos = [
            'titulo' => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Style\Border::BORDER_THIN]
                ],
                'fill' => [
                    'fillType' => Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '000000']
                ]
            ],
            'encabezado' => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Style\Border::BORDER_THIN]
                ],
                'fill' => [
                    'fillType' => Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'A6A6A6']
                ]
            ],
            'centrado' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER]
            ],
            'fecha' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER],
                'numberFormat' => ['formatCode' => self::formatoFecha]
            ],
            'fecha_hora' => [
                'alignment' => ['horizontal' =>  Style\Alignment::HORIZONTAL_CENTER],
                'numberFormat' => ['formatCode' => self::formatoFechaHora]
            ],
            'moneda' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_RIGHT],
                'numberFormat' => ['formatCode' => self::formatoMoneda]
            ],
            'porcentaje' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER],
                'numberFormat' => ['formatCode' => self::formatoPorcentaje]
            ],
            'texto_centrado' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER]
            ],
            'texto_izquierda' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_LEFT]
            ],
            'texto_derecha' => [
                'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_RIGHT]
            ]
        ];

        return $estilos[$estilo] ?? $estilos;
    }

    /**
     * GeneraExcel
     * 
     * Genera un objeto de hoja de cálculo de Excel con los datos proporcionados.
     *
     * @param string $nombre_hoja Nombre de la hoja dentro del archivo Excel.
     * @param string $titulo_reporte Título del reporte que se mostrará en la primera fila.
     * @param array $columnas Arreglo de columnas con la estructura obtenida en ColumnaExcel.
     * @param array $filas Arreglo de filas con los datos a incluir en el reporte.
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet Un objeto de hoja de cálculo de Excel.
     */
    public static function GeneraExcel($nombre_hoja, $titulo_reporte, $columnas, $filas, $configuracion = [])
    {
        $totales = [];
        $libro = new Spreadsheet();
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle($nombre_hoja);
        $filaTitulo = $configuracion['filaTitulo'] ?? 1;
        $filaEncabezados = $configuracion['filaEncabezados'] ?? 2;
        $tituloDoble = false;
        $c2 = 0;

        // Comprueba si hay columnas anidadas
        foreach ($columnas as $key => $columna) {
            if (is_array($columna['titulo'])) {
                if (!$tituloDoble) $filaEncabezados++;
                $tituloDoble = true;

                $c1 = $key + $c2;
                $r1 = self::getLetraColumna($c1) . ($filaEncabezados - 1);

                $c2 = count($columna['titulo']);
                $r2 = self::getLetraColumna($c1 + $c2 - 1) . ($filaEncabezados - 1);

                $hoja->setCellValue($r1, $columna['campo']);
                $hoja->mergeCells("$r1:$r2");
                $hoja->getStyle("$r1:$r2")->applyFromArray(self::GetEstilosExcel('encabezado'));
                $c2--;
            }
        }

        if ($tituloDoble) $columnas = self::aplanarColumnas($columnas);

        // Encabezados de columna
        foreach ($columnas as $key => $columna) {
            if (!isset($columna['letra']) || $columna['letra'] === '') {
                $columna['letra'] = self::getLetraColumna($key);
                $columnas[$key]['letra'] = $columna['letra'];
            }

            $fe = $filaEncabezados;
            if ($tituloDoble && !$hoja->getCell($columna['letra'] . $fe - 1)->isInMergeRange()) {
                $fe--;
                $hoja->mergeCells($columna['letra'] . $fe . ':' . $columna['letra'] . $filaEncabezados);
                $hoja->getStyle($columna['letra'] . $filaEncabezados)->applyFromArray(self::GetEstilosExcel('encabezado'));
            }

            $hoja->setCellValue($columna['letra'] . $fe, $columna['titulo']);
            $hoja->getStyle($columna['letra'] . $fe)->applyFromArray(self::GetEstilosExcel('encabezado'));
            $hoja->getColumnDimension($columna['letra'])->setAutoSize(true);
            if ($columna['total']) array_push($totales, $columna);
        }

        // Título del reporte
        $hoja->setCellValue("A$filaTitulo", $titulo_reporte);
        $hoja->mergeCells("A$filaTitulo:" . $columnas[count($columnas) - 1]['letra'] . $filaTitulo);
        $hoja->getStyle("A$filaTitulo")->applyFromArray(self::GetEstilosExcel('titulo'));

        // Filas de datos
        $filaInicial = $filaEncabezados + 1;
        $noFila = $filaInicial;
        foreach ($filas as $key => $fila) {
            if ($noFila % 2 == 0) {
                $hoja->getStyle("A$noFila:" . $columnas[count($columnas) - 1]['letra'] . $noFila)
                    ->getFill()
                    ->setFillType(Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('C5D9F1');
            }

            foreach ($columnas as $key => $columna) {
                $estiloCelda = $columna['estilo'];
                $estiloCelda['borders']['left']['borderStyle'] = Style\Border::BORDER_THIN;
                $estiloCelda['borders']['right']['borderStyle'] = Style\Border::BORDER_THIN;

                if ($columna['estilo'] === self::GetEstilosExcel('fecha'))
                    $hoja->setCellValue($columna['letra'] . $noFila, self::convierteFecha('d/m/Y', $fila[$columna['campo']]));
                else if ($columna['estilo'] === self::GetEstilosExcel('fecha_hora'))
                    $hoja->setCellValue($columna['letra'] . $noFila, self::convierteFecha('d/m/Y H:i:s', $fila[$columna['campo']]));
                else if ($columna['estilo'] === self::GetEstilosExcel('texto_centrado') || $columna['estilo'] === self::GetEstilosExcel('texto_izquierda') || $columna['estilo'] === self::GetEstilosExcel('texto_derecha'))
                    $hoja->setCellValueExplicit($columna['letra'] . $noFila, html_entity_decode($fila[$columna['campo']], ENT_QUOTES, "UTF-8"), DataType::TYPE_STRING);
                else
                    $hoja->setCellValue($columna['letra'] . $noFila, html_entity_decode($fila[$columna['campo']], ENT_QUOTES, "UTF-8"));

                $hoja->getStyle($columna['letra'] . $noFila)->applyFromArray($estiloCelda);
            }

            $noFila += 1;
        }

        // Poner borde a la última fila
        $hoja->getStyle('A' . ($noFila - 1) . ':' . $columnas[count($columnas) - 1]['letra'] . ($noFila - 1))
            ->applyFromArray([
                'borders' => [
                    'bottom' => ['borderStyle' => Style\Border::BORDER_THIN]
                ]
            ]);

        // Incluir totales, si es necesario
        if (count($totales) > 0) {
            $noFila += 1;
            self::AddTotales($hoja, $noFila, $columnas, $totales);
        }

        // Congelar en la fila de encabezados, poner autofiltro y ajustar ancho de columnas al contenido
        $hoja->setSelectedCell("A$filaInicial");
        $hoja->freezePane("A$filaInicial");
        $hoja->setAutoFilter("A$filaEncabezados:" . $columnas[count($columnas) - 1]['letra'] . $filaEncabezados);

        // Poner el cursor en la celda A1
        $hoja->setSelectedCell('A1');

        return $libro;
    }

    /**
     * AddTotales
     * 
     * Agrega una fila de totales a la hoja de cálculo.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $hoja La hoja de cálculo donde se agregarán los totales.
     * @param int $noFila El número de fila donde se colocarán los totales.
     * @param array $columnas Un arreglo de columnas que se utilizarán para aplicar estilos.
     * @param array $totales Un arreglo de totales que contiene la letra de la columna y el estilo a aplicar.
     *
     * @return void
     */
    public static function AddTotales($hoja, $noFila, $columnas, $totales)
    {
        $hoja->setCellValue("A$noFila", 'Totales');
        $hoja->getStyle("A$noFila")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER]
        ]);

        // Poner estilo a la fila de totales
        $hoja->getStyle("A$noFila:" . $columnas[count($columnas) - 1]['letra'] . $noFila)
            ->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Style\Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Style\Border::BORDER_THIN],
                    'left' => ['borderStyle' => Style\Border::BORDER_THIN],
                    'right' => ['borderStyle' => Style\Border::BORDER_THIN]
                ],
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'A6A6A6']
                ]
            ]);

        // Poner fórmulas para totales
        foreach ($totales as $key => $total) {
            $hoja->setCellValue($total['letra'] . $noFila, '=SUBTOTAL(9,' . $total['letra'] . '3:' . $total['letra'] . ($noFila - 2) . ')');
            $hoja->getStyle($total['letra'] . $noFila)->applyFromArray($total['estilo']);
        }
    }

    /**
     * DescargaExcel
     * 
     * Responde con un archivo Excel descargable con los datos proporcionados.
     * 
     * @param string $nombre_archivo Nombre del archivo Excel a generar.
     * @param string $nombre_hoja Nombre de la hoja dentro del archivo Excel.
     * @param string $titulo_reporte Título del reporte que se mostrará en la primera fila.
     * @param array $columnas Arreglo de columnas con la estructura obtenida en ColumnaExcel.
     * @param array $filas Arreglo de filas con los datos a incluir en el reporte.
     * 
     * @return void
     */
    public static function DescargaExcel($nombre_archivo, $nombre_hoja, $titulo_reporte, $columnas, $filas)
    {
        $libro = self::GeneraExcel($nombre_hoja, $titulo_reporte, $columnas, $filas);

        // Configuración de encabezados HTTP
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombre_archivo . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Pragma: public');

        // Envia el archivo
        $writer = new Xlsx($libro);
        $writer->save('php://output');
    }

    /**
     * GuardaExcel
     * 
     * Guarda un archivo Excel en el servidor con los datos proporcionados.
     *
     * @param string $nombre_archivo Nombre del archivo Excel a generar.
     * @param string $nombre_hoja Nombre de la hoja dentro del archivo Excel.
     * @param string $titulo_reporte Título del reporte que se mostrará en la primera fila.
     * @param array $columnas Arreglo de columnas con la estructura obtenida en ColumnaExcel.
     * @param array $filas Arreglo de filas con los datos a incluir en el reporte.
     *
     * @return string La ruta del archivo Excel generado.
     */
    public static function GuardaExcel($nombre_archivo, $nombre_hoja, $titulo_reporte, $columnas, $filas)
    {
        $libro = self::GeneraExcel($nombre_hoja, $titulo_reporte, $columnas, $filas);
        $ruta = __DIR__ . "/{$nombre_archivo}.xlsx";

        $writer = new Xlsx($libro);
        $writer->save($ruta);
        return $ruta;
    }

    /**
     * getLetraColumna
     * 
     * Obtiene la letra de la columna en Excel a partir de un índice.
     *
     * @param int $indice El índice de la columna.
     *
     * @return string La letra de la columna en Excel.
     */
    private static function getLetraColumna($indice)
    {
        $letra = '';
        while ($indice >= 0) {
            $letra = chr($indice % 26 + 65) . $letra;
            $indice = intval($indice / 26) - 1;
        }
        return $letra;
    }

    /**
     * convierteFecha
     * 
     * Convierte una fecha en formato PHP a un formato compatible con Excel.
     *
     * @param string $formato El formato de la fecha en PHP.
     * @param string $fecha La fecha a convertir.
     *
     * @return mixed La fecha convertida a un formato compatible con Excel.
     */
    private static function convierteFecha($formato, $fecha)
    {
        if (!$fecha || empty($fecha) || $fecha === '') return null;
        $f = DateTime::createFromFormat($formato, $fecha);

        if ($f === false) return null;
        $f = Date::PHPToExcel($f);

        if ($f === false) return null;
        return $f;
    }

    /**
     * aplanarColumnas
     * 
     * Pone las subcolumnas al mismo nivel que las columnas principales para procesarlas.
     * 
     * @param array $columnas Arreglo de columnas con la estructura obtenida en ColumnaExcel.
     * 
     * @return array Un arreglo con las columnas aplanadas.
     */
    private static function aplanarColumnas($columnas)
    {
        $resultado = [];

        foreach ($columnas as $columna) {
            if (!is_array($columna['titulo'])) $resultado[] = $columna;
            else {
                foreach ($columna['titulo'] as $subcolumna) {
                    $resultado[] = $subcolumna;
                }
            }
        }

        return $resultado;
    }
}
