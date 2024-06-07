<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\Operaciones AS OperacionesDao;

class Cultiva extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());

    }

    public function generarExcel(){

        $Fecha = $_GET['Inicial'];
        $Fecha2 = $_GET['Final'];

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reporte");
        $objPHPExcel->getProperties()->setDescription("Descripcion");
        $objPHPExcel->setActiveSheetIndex(0);



        $estilo_titulo = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_encabezado = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_celda = array(
            'font' => array('bold' => false,'name'=>'Calibri','size'=>11,'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID

        );


        $fila = 1;
        $adaptarTexto = true;

        $controlador = "Operaciones";
        $columna = array('A','B','C','D');
        $nombreColumna = array( 'SUCURSAL', 'NOMBRE_GRUPO', 'CLIENTE', 'DOMICILIO');
        $nombreCampo = array('SUCURSAL','NOMBRE_GRUPO','CLIENTE','DOMICILIO'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Consulta de Solicitudes Cultiva');
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$fila.':'.$columna[count($nombreColumna)-1].$fila);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->applyFromArray($estilo_titulo);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->getAlignment()->setWrapText($adaptarTexto);

        $fila +=1;

        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila +=1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        if($Fecha != '')
        {
            $Layoutt = OperacionesDao::ConsultaGruposCultiva($Fecha, $Fecha2);
        }
        else
        {
            $fechaActual = date('Y-m-d');
            $Layoutt = OperacionesDao::ConsultaGruposCultiva($fechaActual, $fechaActual);
        }

        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
                $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            }
            $fila +=1;
        }


        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$columna[count($columna)-1].$fila)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for ($i=0; $i <$fila ; $i++) {
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
        }


        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Cultiva Reporte Clientes'.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function index()
    {
        $extraHeader = <<<html
        <title>Consulta Altas Grupo Cultiva</title>
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
            
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Cultiva/generarExcel/?Inicial='+fecha1 + '&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;


        $fechaActual = date('Y-m-d');
        $Fecha = $_GET['Inicial'];
        $FechaFinal = $_GET['Final'];

        if ($Fecha != '') {
            $Consulta = OperacionesDao::ConsultaGruposCultiva($Fecha, $FechaFinal);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">{$value['CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['DOMICILIO']}</td>
                    <td style="padding: 0px !important;">{$value['SOLICITUD']}</td>
                </tr>
html;
                View::set('Inicial', $Fecha);
                View::set('Final', $FechaFinal);

            }

        } else {
            $Consulta = OperacionesDao::ConsultaGruposCultiva($fechaActual, $fechaActual);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                   <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                   <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">{$value['CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['DOMICILIO']}</td>
                    <td style="padding: 0px !important;">{$value['SOLICITUD']}</td>
                </tr>
html;
            }
            View::set('Inicial', date("Y-m-d"));
            View::set('Final', date("Y-m-d"));
        }
        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("zz_cultiva_consulta_clientes");


    }

    public function ReingresarClientesCredito()
    {
        $extraHeader = <<<html
        <title>Reingresar Clientes Cultiva</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
       
       ponerElCursorAlFinal('Credito');
       
       function ActivarCredito(cdgcl, fecha, motivo){	
           
            if(motivo == '')
                {
                     swal("Atención", "Ingrese un monto mayor a $0", "warning");
                     document.getElementById("monto_e").focus();
                  
                }
            else
                {
                    $.ajax({
                    type: 'POST',
                    url: '/Cultiva/ReactivarCredito/',
                    data: "cdgcl="+cdgcl,
                    success: function(respuesta) {
                         if(respuesta=='1'){
                    
                                swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }else 
                            {
                                swal(respuesta, {
                                      icon: "error",
                                    });
                            }
                    }
                    });
                }
    }
    
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
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
            
        });
      
      </script>
html;

        $credito = $_GET['Credito'];

        if ($credito != '') {

            $Clientes = OperacionesDao::ReingresarClientesCredito($credito);

            foreach ($Clientes[0] as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 10px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 10px !important;">{$value['CDGCL']}</td>
                    <td style="padding: 10px !important;">{$value['NOMBRE_CLIENTE']}</td>
                    <td style="padding: 10px !important;">{$value['FECHA_BAJA']}</td>
                    <td style="padding: 10px !important;">{$value['MOTIVO_BAJA']}</td>
                    <td> <button type="button" class="btn btn-danger btn-circle" onclick="ActivarCredito('{$value['CDGCL']}', '{$value['FECHA_BAJA_REAL']}', '{$value['CODIGO_MOTIVO']}');"><i class="fa fa-check"></i></button></td>
                </tr>
html;
            }
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('Nombre', $Clientes[1]['NOMBRE']);
            View::render("reingresar_clientes_cultiva_sec");

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("reingresar_clientes_cultiva_ini");
        }
    }

    public function ReactivarCredito()
    {
        $cliente = new \stdClass();

        $cdgcl = MasterDom::getDataAll('cdgcl');
        $cliente->_cdgcl = $cdgcl;


        $id = OperacionesDao::updateCliente($cliente);
        return $id;
    }
}
