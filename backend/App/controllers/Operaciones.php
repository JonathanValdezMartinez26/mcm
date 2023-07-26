<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Pagos AS PagosDao;
use \App\models\Operaciones AS OperacionesDao;

class Operaciones extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());

    }
    public function getUsuario()
    {
        return $this->__usuario;
    }
    public function ReportePLDPagos()
    {
        $extraHeader = <<<html
        <title>Consulta de Desembolsos Cultiva</title>
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
                    [13, 50, -1],
                    [132, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
             
        });
      
            function Validar(){
                
                fecha1 = moment(document.getElementById("Inicial").innerHTML = inputValue);
                fecha2 = moment(document.getElementById("Final").innerHTML = inputValue);
                
                dias = fecha2.diff(fecha1, 'days');alert(dias);
                
                if(dias == 1)
                    {
                        alert("si es");
                        return false;
                    }
                return false;
          }
      
         Inicial.max = new Date().toISOString().split("T")[0];
         Final.max = new Date().toISOString().split("T")[0];
          
         function InfoAdmin()
         {
             swal("Info", "Este registro fue capturado por una administradora en caja", "info");
         }
         function InfoPhone()
         {
             swal("Info", "Este registro fue capturado por un ejecutivo en campo y procesado por una administradora", "info");
         }
    
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];



        if ($Inicial != '' || $Final != '') {
            $Consulta = OperacionesDao::ConsultarPagos($Inicial, $Final);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['LOCALIDAD']}</td>
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CUENTA']}</td>
                    <td style="padding: 0px !important;">{$value['INSTRUMENTO_MONETARIO']}</td>
                    <td style="padding: 0px !important;">{$value['MONEDA']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONTO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['CLAVE_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CAJA']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CAJERO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_HORA']}</td>
                    <td style="padding: 0px !important;">{$value['NOTARJETA_CTA']}</td>
                    <td style="padding: 0px !important;">{$value['TIPOTARJETA']}</td>
                    <td style="padding: 0px !important;">{$value['COD_AUTORIZACION']}</td>
                    <td style="padding: 0px !important;">{$value['ATRASO']}</td>
                    <td style="padding: 0px !important;">{$value['OFICINA_CLIENTE']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::render("pagos_cobrados_consulta_cultiva_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_cobrados_consulta_cultiva_busqueda");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("pagos_cobrados_consulta_cultiva_all");
        }
    }

    public function ReportePLDPagosNacimiento()
    {
        $extraHeader = <<<html
        <title>Consulta de Desembolsos Cultiva con Fecha Nac</title>
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
                    [13, 50, -1],
                    [132, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
              $('#all').attr('action', '/Operaciones/generarExcelPagosF/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
             
        });
      
            function Validar(){
                
                fecha1 = moment(document.getElementById("Inicial").innerHTML = inputValue);
                fecha2 = moment(document.getElementById("Final").innerHTML = inputValue);
                
                dias = fecha2.diff(fecha1, 'days');alert(dias);
                
                if(dias == 1)
                    {
                        alert("si es");
                        return false;
                    }
                return false;
          }
      
         Inicial.max = new Date().toISOString().split("T")[0];
         Final.max = new Date().toISOString().split("T")[0];
          
         function InfoAdmin()
         {
             swal("Info", "Este registro fue capturado por una administradora en caja", "info");
         }
         function InfoPhone()
         {
             swal("Info", "Este registro fue capturado por un ejecutivo en campo y procesado por una administradora", "info");
         }
    
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];



        if ($Inicial != '' || $Final != '') {
            $Consulta = OperacionesDao::ConsultarPagosNacimiento($Inicial, $Final);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['LOCALIDAD']}</td>
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CUENTA']}</td>
                    <td style="padding: 0px !important;">{$value['INSTRUMENTO_MONETARIO']}</td>
                    <td style="padding: 0px !important;">{$value['MONEDA']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONTO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['CLAVE_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CAJA']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CAJERO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_HORA']}</td>
                    <td style="padding: 0px !important;">{$value['NOTARJETA_CTA']}</td>
                    <td style="padding: 0px !important;">{$value['TIPOTARJETA']}</td>
                    <td style="padding: 0px !important;">{$value['COD_AUTORIZACION']}</td>
                    <td style="padding: 0px !important;">{$value['ATRASO']}</td>
                    <td style="padding: 0px !important;">{$value['OFICINA_CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['FEC_NAC']}</td>
                    <td style="padding: 0px !important;">{$value['EDAD']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::render("pagos_cobrados_consulta_cultiva_busqueda_message_F");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_cobrados_consulta_cultiva_busqueda_F");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("pagos_cobrados_consulta_cultiva_all_F");
        }
    }

    public function ReportePLDDesembolsos()
    {
        $extraHeader = <<<html
        <title>Consulta de Desembolsos Cultiva</title>
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
                    [13, 50, -1],
                    [132, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
              $('#all').attr('action', '/Operaciones/generarExcel/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
             
        });
      
            function Validar(){
                
                fecha1 = moment(document.getElementById("Inicial").innerHTML = inputValue);
                fecha2 = moment(document.getElementById("Final").innerHTML = inputValue);
                
                dias = fecha2.diff(fecha1, 'days');alert(dias);
                
                if(dias == 1)
                    {
                        alert("si es");
                        return false;
                    }
                return false;
          }
      
         Inicial.max = new Date().toISOString().split("T")[0];
         Final.max = new Date().toISOString().split("T")[0];
          
         function InfoAdmin()
         {
             swal("Info", "Este registro fue capturado por una administradora en caja", "info");
         }
         function InfoPhone()
         {
             swal("Info", "Este registro fue capturado por un ejecutivo en campo y procesado por una administradora", "info");
         }
    
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];



        if ($Inicial != '' || $Final != '') {
            $Consulta = OperacionesDao::ConsultarDesembolsos($Inicial, $Final);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['LOCALIDAD']}</td>
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CUENTA']}</td>
                    <td style="padding: 0px !important;">{$value['INSTRUMENTO_MONETARIO']}</td>
                    <td style="padding: 0px !important;">{$value['MONEDA']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONTO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['CLAVE_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CAJA']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CAJERO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_HORA']}</td>
                    <td style="padding: 0px !important;">{$value['NOTARJETA_CTA']}</td>
                    <td style="padding: 0px !important;">{$value['TIPOTARJETA']}</td>
                    <td style="padding: 0px !important;">{$value['COD_AUTORIZACION']}</td>
                    <td style="padding: 0px !important;">{$value['ATRASO']}</td>
                    <td style="padding: 0px !important;">{$value['OFICINA_CLIENTE']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::render("pagos_consulta_cultiva_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_consulta_cultiva_busqueda");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("pagos_consulta_cultiva_all");
        }
    }

    public function IdentificacionClientes()
    {
        $extraHeader = <<<html
        <title>Identificación de clientes Cultiva</title>
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
                    [13, 50, -1],
                    [132, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
              $('#all').attr('action', '/Operaciones/generarExcel/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
             
        });
      
            function Validar(){
                
                fecha1 = moment(document.getElementById("Inicial").innerHTML = inputValue);
                fecha2 = moment(document.getElementById("Final").innerHTML = inputValue);
                
                dias = fecha2.diff(fecha1, 'days');alert(dias);
                
                if(dias == 1)
                    {
                        alert("si es");
                        return false;
                    }
                return false;
          }
      
         Inicial.max = new Date().toISOString().split("T")[0];
         Final.max = new Date().toISOString().split("T")[0];
          
      
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];



        if ($Inicial != '' || $Final != '') {
            $Consulta = OperacionesDao::ConsultarClientes($Inicial, $Final);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['LOCALIDAD']}</td>
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CUENTA']}</td>
                    <td style="padding: 0px !important;">{$value['INSTRUMENTO_MONETARIO']}</td>
                    <td style="padding: 0px !important;">{$value['MONEDA']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONTO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['CLAVE_RECEPTOR']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_CAJA']}</td>
                    <td style="padding: 0px !important;">{$value['ID_CAJERO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_HORA']}</td>
                    <td style="padding: 0px !important;">{$value['NOTARJETA_CTA']}</td>
                    <td style="padding: 0px !important;">{$value['TIPOTARJETA']}</td>
                    <td style="padding: 0px !important;">{$value['COD_AUTORIZACION']}</td>
                    <td style="padding: 0px !important;">{$value['ATRASO']}</td>
                    <td style="padding: 0px !important;">{$value['OFICINA_CLIENTE']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::render("pagos_consulta_cultiva_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_consulta_cultiva_busqueda");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("pagos_consulta_cultiva_all");
        }
    }

    public function generarExcel(){

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reorte");
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
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N','O', 'P', 'Q', 'R', 'S', 'T', 'V', 'W');
        $nombreColumna = array('LOCALIDAD','SUCURSAL','TIPO DE OPERACION','ID CLIENTE', 'NUMERO DE CTA-CONTRATO-OPERACIO- POLIZA O NUMERO DE SEGURIDAD SOCIAL', 'INSTRUMENTO MONETARIO', 'MONEDA', 'MONTO', 'FECHA DE LA OPERACION', 'TIPO RECEPTOR', 'CLAVE DCE RECEPTOR', 'NUM CAJA', 'ID-CAJERO', 'FECHA-HORA', 'NOTARJETA-CTA DEP', 'TIPOTARJETA', 'COD-AUTORIZACION', 'ATRASO', 'OFICINA CLIENTE');
        $nombreCampo = array('LOCALIDAD','SUCURSAL','TIPO_OPERACION','ID_CLIENTE',
            'NUM_CUENTA',
            'INSTRUMENTO_MONETARIO',
            'MONEDA',
            'MONTO',
            'FECHA_OPERACION',
            'TIPO_RECEPTOR','CLAVE_RECEPTOR','NUM_CAJA','ID_CAJERO','FECHA_HORA','NOTARJETA_CTA',
            'TIPOTARJETA','COD_AUTORIZACION','ATRASO','OFICINA_CLIENTE'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Consulta de Desembolsos Cultiva');
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

        $Layoutt = OperacionesDao::ConsultarDesembolsos($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Consulta de Desembolsos Cultiva '.$controlador.'.xlsx"');
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

    public function generarExcelPagos(){

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reorte");
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
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N','O', 'P', 'Q', 'R', 'S', 'T', 'V', 'W');
        $nombreColumna = array( 'LOCALIDAD','SUCURSAL','TIPO DE OPERACION','ID CLIENTE', 'NUMERO DE CTA-CONTRATO-OPERACIO- POLIZA O NUMERO DE SEGURIDAD SOCIAL', 'INSTRUMENTO MONETARIO', 'MONEDA', 'MONTO', 'FECHA DE LA OPERACION', 'TIPO RECEPTOR', 'CLAVE DCE RECEPTOR', 'NUM CAJA', 'ID-CAJERO', 'FECHA-HORA', 'NOTARJETA-CTA DEP', 'TIPOTARJETA', 'COD-AUTORIZACION', 'ATRASO', 'OFICINA CLIENTE');
        $nombreCampo = array('LOCALIDAD','SUCURSAL','TIPO_OPERACION','ID_CLIENTE',
            'NUM_CUENTA',
            'INSTRUMENTO_MONETARIO',
            'MONEDA',
            'MONTO',
            'FECHA_OPERACION',
            'TIPO_RECEPTOR','CLAVE_RECEPTOR','NUM_CAJA','ID_CAJERO','FECHA_HORA','NOTARJETA_CTA',
            'TIPOTARJETA','COD_AUTORIZACION','ATRASO','OFICINA_CLIENTE'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Consulta de Pagos Cultiva');
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

        $Layoutt = OperacionesDao::ConsultarPagos($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Consulta de Pagos Cultiva '.$controlador.'.xlsx"');
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

    public function generarExcelPagosF(){

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reorte");
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
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N','O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V');
        $nombreColumna = array( 'LOCALIDAD','SUCURSAL','TIPO DE OPERACION','ID CLIENTE', 'NUMERO DE CTA-CONTRATO-OPERACIO- POLIZA O NUMERO DE SEGURIDAD SOCIAL', 'INSTRUMENTO MONETARIO', 'MONEDA', 'MONTO', 'FECHA DE LA OPERACION', 'TIPO RECEPTOR', 'CLAVE DCE RECEPTOR', 'NUM CAJA', 'ID-CAJERO', 'FECHA-HORA', 'NOTARJETA-CTA DEP', 'TIPOTARJETA', 'COD-AUTORIZACION', 'ATRASO', 'OFICINA CLIENTE', 'FEC_NAC', 'EDAD', 'CICLO');
        $nombreCampo = array('LOCALIDAD','SUCURSAL','TIPO_OPERACION','ID_CLIENTE',
            'NUM_CUENTA',
            'INSTRUMENTO_MONETARIO',
            'MONEDA',
            'MONTO',
            'FECHA_OPERACION',
            'TIPO_RECEPTOR','CLAVE_RECEPTOR','NUM_CAJA','ID_CAJERO','FECHA_HORA','NOTARJETA_CTA',
            'TIPOTARJETA','COD_AUTORIZACION','ATRASO','OFICINA_CLIENTE', 'FEC_NAC', 'EDAD', 'CICLO'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Consulta de Pagos Cultiva');
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

        $Layoutt = OperacionesDao::ConsultarPagosNacimiento($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Consulta de Pagos Cultiva '.$controlador.'.xlsx"');
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

    public function generarExcelPagosIC(){

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reorte");
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
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N','O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ');

        $nombreColumna = array( 'ID CLIENTE','CUENTA','Origen','NOMBRE', 'ADICIONAL', 'APELLIDO PATERNO', 'APELLIDO MATERNO', 'TIPO DE PERSONA', 'RFC',
            'CURP', 'RAZON SOCIAL O DENOMINACION', 'FECHA DE NACIMIENTO OCONSTITUCION', 'NACIONALIDAD', 'DOMICILIO(calle- número exterior e interior (si aplica) y código postal)', 'COLONIA', 'CIUDAD O POBLACION',
            'PAIS', 'ESTADO', 'TELEFONO OFICINA/PARTICULAR', 'ACTIVIDAD ECONOMICA', 'CALIFICACIÓN', 'FECHA ALTA', 'SUCURSAL', 'GENERO', 'CORREO ELEC.', 'FIRMA ELEC.', 'PROFESION', 'OCUPACION',
            'PAIS NAC.', 'EDO. NAC.', 'LUGAR NAC.', 'NUMERO DE DOCUMENTO', 'CONOCIMIENTO CLIENTE', 'REGISTR O NACIONAL DE INMIGRACION', 'CUENTA ORIGINAL', 'SITUACIÓN CLIENTE', 'TIPO DOCUMENTO', 'INDICADOR EMPLEO', 'EMPRESA LABORA(Ó)',
            'INDICADOR GOBIERNO', 'PUESTO', 'FECHA INICIO', 'FEH FIN');

        $nombreCampo = array('LOCALIDAD','SUCURSAL','TIPO_OPERACION','ID_CLIENTE',
            'NUM_CUENTA',
            'INSTRUMENTO_MONETARIO',
            'MONEDA',
            'MONTO',
            'FECHA_OPERACION',
            'TIPO_RECEPTOR','CLAVE_RECEPTOR','NUM_CAJA','ID_CAJERO','FECHA_HORA','NOTARJETA_CTA',
            'TIPOTARJETA','COD_AUTORIZACION','ATRASO','OFICINA_CLIENTE', 'FEC_NAC', 'EDAD', 'CICLO'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Consulta de Pagos Cultiva');
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

        $Layoutt = OperacionesDao::ConsultarPagosNacimiento($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Consulta de Pagos Cultiva '.$controlador.'.xlsx"');
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
}
