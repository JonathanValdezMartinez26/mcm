<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Reportes as ReportesDao;
use DateTime;

class Reportes extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function UsuariosMCM()
    {
        $extraHeader = <<<html
        <title>Reporte Usuarios SICAFIN MCM</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
      function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
      
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [30, 50, -1],
                    [30, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Reportes/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;

        $Consulta = ReportesDao::ConsultaUsuariosSICAFINMCM();
        $tabla = "";

        foreach ($Consulta as $key => $fila) {
            $tabla .= "<tr style='padding: 0px !important;'>";
            foreach ($fila as $key => $columna) {
                if ($key == 'ACTIVO') $columna = self::ValidaSN($columna);
                if ($key == 'PUESTO') $columna = self::QuitaDuplicados($columna);
                if ($key == 'FECHA_ALTA') $columna = self::FechaCompleta($columna);

                $tabla .= "<td style='padding: 0px !important;'>{$columna}</td>";
            }
            $tabla .= "</tr>";
        }
        //         foreach ($Consulta as $key => $value) {

        //             $tabla = <<<html
        //                 <tr style="padding: 0px !important;">
        //                     <td style="padding: 0px !important;">{$value['COD_USUARIO']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMBRE_COMPLETO']}</td>
        //                     <td style="padding: 0px !important;">{$value['FECHA_ALTA']}</td>
        //                     <td style="padding: 0px !important;">{$value['COD_SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA_JEFE']}</td>
        //                     <td style="padding: 0px !important;">{$value['ACTIVO']}</td>
        //                     <td style="padding: 0px !important;">{$value['PUESTO']}</td>
        //                 </tr>
        // html;
        //         }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("usuarios_SICAFIN_Reporte");
    }

    public function UsuariosCultiva()
    {
        $extraHeader = <<<html
        <title>Reporte Usuarios SICAFIN Cultiva</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
      function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
      
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [30, 50, -1],
                    [30, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Reportes/generarExcelCultiva/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;

        $Consulta = ReportesDao::ConsultaUsuariosSICAFINCultiva();
        $tabla = "";

        foreach ($Consulta as $key => $fila) {
            $tabla .= "<tr style='padding: 0px !important;'>";
            foreach ($fila as $key => $columna) {
                if ($key == 'ACTIVO') $columna = self::ValidaSN($columna);
                if ($key == 'PUESTO') $columna = self::QuitaDuplicados($columna);
                if ($key == 'FECHA_ALTA') $columna = self::FechaCompleta($columna);

                $tabla .= "<td style='padding: 0px !important;'>{$columna}</td>";
            }
            $tabla .= "</tr>";
        }

        //         foreach ($Consulta as $key => $value) {

        //             $tabla = <<<html
        //                 <tr style="padding: 0px !important;">
        //                     <td style="padding: 0px !important;">{$value['COD_USUARIO']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMBRE_COMPLETO']}</td>
        //                     <td style="padding: 0px !important;">{$value['FECHA_ALTA']}</td>
        //                     <td style="padding: 0px !important;">{$value['COD_SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA_JEFE']}</td>
        //                     <td style="padding: 0px !important;">{$value['ACTIVO']}</td>
        //                     <td style="padding: 0px !important;">{$value['PUESTO']}</td>
        //                 </tr>
        // html;
        //         }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("usuarios_SICAFIN_Reporte_Cultiva");
    }

    public function generarExcel()
    {


        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reporte");
        $objPHPExcel->getProperties()->setDescription("Descripcion");
        $objPHPExcel->setActiveSheetIndex(0);



        $estilo_titulo = array(
            'font' => array('bold' => true, 'name' => 'Calibri', 'size' => 11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_encabezado = array(
            'font' => array('bold' => true, 'name' => 'Calibri', 'size' => 11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_celda = array(
            'font' => array('bold' => false, 'name' => 'Calibri', 'size' => 11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID

        );


        $fila = 1;
        $adaptarTexto = true;

        $controlador = "Reportes";
        $columna = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
        $nombreColumna = array('COD USUARIO', 'NOMBRE_COMPLETO', 'FECHA_ALTA', 'COD_SUCURSAL', 'SUCURSAL', 'NOMINA', 'NOMINA_JEFE', 'ACTIVO', 'PUESTO');
        $nombreCampo = array(
            'COD_USUARIO', 'NOMBRE_COMPLETO', 'FECHA_ALTA', 'COD_SUCURSAL', 'SUCURSAL', 'NOMINA', 'NOMINA_JEFE', 'ACTIVO', 'PUESTO'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $fila, 'Reporte de Usuarios MCM');
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $fila . ':' . $columna[count($nombreColumna) - 1] . $fila);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($estilo_titulo);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->getAlignment()->setWrapText($adaptarTexto);

        $fila += 1;

        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila += 1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        $Layoutt = ReportesDao::ConsultaUsuariosSICAFINMCM();


        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
                if ($campo == 'ACTIVO') $value[$campo] = self::ValidaSN($value[$campo]);
                if ($campo == 'PUESTO') $value[$campo] = self::quitaDuplicados($value[$campo]);
                if ($campo == 'FECHA_ALTA') $value[$campo] = self::FechaCompleta($value[$campo]);

                $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_celda);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
            }
            $fila += 1;
        }


        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $columna[count($columna) - 1] . $fila)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for ($i = 0; $i < $fila; $i++) {
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
        }


        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte de Usuarios SICAFIN MCM' . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function generarExcelCultiva()
    {


        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reporte");
        $objPHPExcel->getProperties()->setDescription("Descripcion");
        $objPHPExcel->setActiveSheetIndex(0);



        $estilo_titulo = array(
            'font' => array('bold' => true, 'name' => 'Calibri', 'size' => 11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_encabezado = array(
            'font' => array('bold' => true, 'name' => 'Calibri', 'size' => 11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_celda = array(
            'font' => array('bold' => false, 'name' => 'Calibri', 'size' => 11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID

        );


        $fila = 1;
        $adaptarTexto = true;

        $controlador = "Reportes";
        $columna = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
        $nombreColumna = array('COD USUARIO', 'NOMBRE_COMPLETO', 'FECHA_ALTA', 'COD_SUCURSAL', 'SUCURSAL', 'NOMINA', 'NOMINA_JEFE', 'ACTIVO', 'PUESTO');
        $nombreCampo = array(
            'COD_USUARIO', 'NOMBRE_COMPLETO', 'FECHA_ALTA', 'COD_SUCURSAL', 'SUCURSAL', 'NOMINA', 'NOMINA_JEFE', 'ACTIVO', 'PUESTO'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $fila, 'Reporte de Usuarios CULTIVA');
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $fila . ':' . $columna[count($nombreColumna) - 1] . $fila);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($estilo_titulo);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->getAlignment()->setWrapText($adaptarTexto);

        $fila += 1;

        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila += 1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        $Layoutt = ReportesDao::ConsultaUsuariosSICAFINCultiva();


        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
                if ($campo == 'ACTIVO') $value[$campo] = self::ValidaSN($value[$campo]);
                if ($campo == 'PUESTO') $value[$campo] = self::QuitaDuplicados($value[$campo]);
                if ($campo == 'FECHA_ALTA') $value[$campo] = self::FechaCompleta($value[$campo]);

                $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_celda);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
            }
            $fila += 1;
        }


        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $columna[count($columna) - 1] . $fila)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for ($i = 0; $i < $fila; $i++) {
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
        }


        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte de Usuarios SICAFIN CULTIVA' . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function ValidaSN($dato)
    {
        if ($dato == 'S') return 'SI';
        if ($dato == 'N') return 'NO';
        return $dato;
    }

    public function FechaCompleta($fecha)
    {
        $fecha_objeto = DateTime::createFromFormat('d/m/y', $fecha);

        if ($fecha_objeto && $fecha_objeto->format('d/m/y') === $fecha) return $fecha_objeto->format('d/m/Y');
        return $fecha;
    }

    public function QuitaDuplicados($lista)
    {
        $arreglo = explode(",", $lista);
        $arreglo = array_unique($arreglo);
        return implode(", ", $arreglo);
    }
}
