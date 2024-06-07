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
        <title>Consulta de Pagos Cultiva</title>
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
              $('#all').attr('action', '/Operaciones/generarExcelPagosIC/?Inicial='+fecha1+'&Final='+fecha2);
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
                    <td style="padding: 0px !important;">{$value['CDGCL']}</td>
                    <td style="padding: 0px !important;">{$value['GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['ORIGEN']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">{$value['ADICIONAL']}</td>
                    <td style="padding: 0px !important;">{$value['A_PATERNO']}</td>
                    <td style="padding: 0px !important;">{$value['A_MATERNO']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_PERSONA']}</td>
                    <td style="padding: 0px !important;">{$value['RFC']}</td>
                    <td style="padding: 0px !important;">{$value['CURP']}</td>
                    <td style="padding: 0px !important;">{$value['RAZON_SOCIAL']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_NAC']}</td>
                    <td style="padding: 0px !important;">{$value['NACIONALIDAD']}</td>
                    <td style="padding: 0px !important;">{$value['DOMICILIO']}</td>
                    <td style="padding: 0px !important;">{$value['COLONIA']}</td>
                    <td style="padding: 0px !important;">{$value['CIUDAD']}</td>
                    <td style="padding: 0px !important;">{$value['PAIS']}</td>
                    <td style="padding: 0px !important;">{$value['SUC_ID_ESTADO']}</td>
                    <td style="padding: 0px !important;">{$value['TELEFONO']}</td>
                    <td style="padding: 0px !important;">{$value['ID_ACTIVIDAD_ECONO']}</td>
                    <td style="padding: 0px !important;">{$value['CALIFICACION']}</td>
                    <td style="padding: 0px !important;">{$value['ALTA']}</td>
                    <td style="padding: 0px !important;">{$value['ID_SUCURSAL_SISTEMA']}</td>
                    <td style="padding: 0px !important;">{$value['GENERO']}</td>
                    <td style="padding: 0px !important;">{$value['CORREO_ELECTRONICO']}</td>
                    <td style="padding: 0px !important;">{$value['FIRMA_ELECT']}</td>
                    <td style="padding: 0px !important;">{$value['PROFESION']}</td>
                    <td style="padding: 0px !important;">{$value['OCUPACION']}</td>
                    <td style="padding: 0px !important;">{$value['PAIS_NAC']}</td>
                    <td style="padding: 0px !important;">{$value['EDO_NAC']}</td>
                    <td style="padding: 0px !important;">{$value['LUGAR_NAC']}</td>
                    
                    <td style="padding: 0px !important;">{$value['NUMERO_DOCUMENTO']}</td>
                    <td style="padding: 0px !important;">{$value['CONOCIMIENTO']}</td>
                    <td style="padding: 0px !important;">{$value['INMIGRACION']}</td>
                    <td style="padding: 0px !important;">{$value['CUENTA_ORIGINAL']}</td>
                    <td style="padding: 0px !important;">{$value['SITUACION_CREDITO']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_DOCUMENTO']}</td>
                    <td style="padding: 0px !important;">{$value['INDICADOR_EMPLEO']}</td>
                    <td style="padding: 0px !important;">{$value['EMPRESAS']}</td>
                    <td style="padding: 0px !important;">{$value['INDICADOR_GOBIERNO']}</td>
                    <td style="padding: 0px !important;">{$value['PUESTO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_INICIO']}</td>
                    <td style="padding: 0px !important;">{$value['FEH_FIN']}</td>
                    <td style="padding: 0px !important;">{$value['CP']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::render("clientes_consulta_cultiva_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("clientes_consulta_cultiva_busqueda");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("clientes_consulta_cultiva_all");
        }
    }

    public function CuentasRelacionadas()
    {
        $extraHeader = <<<html
        <title>Cuentas Relacionadas de clientes Cultiva</title>
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
              $('#all').attr('action', '/Operaciones/generarExcelClientesCR/?Inicial='+fecha1+'&Final='+fecha2);
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
            $Consulta = OperacionesDao::CuentasRelacionadas($Inicial, $Final);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['CUENTA_RELACION']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">{$value['ADICIONAL']}</td>
                    <td style="padding: 0px !important;">{$value['A_PATERNO']}</td>
                    <td style="padding: 0px !important;">{$value['A_MATERNO']}</td>
                    <td style="padding: 0px !important;">{$value['DESCRIPCION_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['IDENTIFICA_CUENTA']}</td>
                    <td style="padding: 0px !important;">{$value['CONSERVA']}</td>
                    <td style="padding: 0px !important;">{$value['OFICINA_CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_INICIO_OPERACION']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::render("cuentas_relacionadas_cultiva_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cuentas_relacionadas_cultiva_busqueda");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("cuentas_relacionadas_consulta_all");
        }
    }

    public function PerfilTransaccional()
    {
        $extraHeader = <<<html
        <title>Perfil transaccional Cultiva</title>
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
              $('#all').attr('action', '/Operaciones/generarExcelClientesPT/?Inicial='+fecha1+'&Final='+fecha2);
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
            $Consulta = OperacionesDao::ConsultarPerfilTransaccional($Inicial, $Final);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CDGCL']}</td>
                    <td style="padding: 0px !important;">{$value['GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">{$value['INSTRUMENTO']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_MONEDA']}</td>
                    <td style="padding: 0px !important;">{$value['T_CAMBIO']}</td>
                    <td style="padding: 0px !important;">{$value['MONTO_PRESTAMO']}</td>
                    <td style="padding: 0px !important;">{$value['PLAZO']}</td>
                    <td style="padding: 0px !important;">{$value['FRECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['TOTAL_PAGOS']}</td>
                    <td style="padding: 0px !important;">{$value['MONTO_FIN_PAGO']}</td>
                    <td style="padding: 0px !important;">{$value['ADELANTAR_PAGO']}</td>
                    <td style="padding: 0px !important;">{$value['NUMERO_APORTACIONES']}</td>
                    <td style="padding: 0px !important;">{$value['MONTO_APORTACIONES']}</td>
                    <td style="padding: 0px !important;">{$value['CUOTA_PAGO']}</td>
                    <td style="padding: 0px !important;">{$value['SALDO']}</td>
                    <td style="padding: 0px !important;">{$value['ID_SUCURSAL_SISTEMA']}</td>
                    <td style="padding: 0px !important;">{$value['ORIGEN_RECURSO']}</td>
                    <td style="padding: 0px !important;">{$value['DESTINO_RECURSOS']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_INICIO_CREDITO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_FIN']}</td>
                    <td style="padding: 0px !important;">{$value['DESTINO']}</td>
                    <td style="padding: 0px !important;">{$value['ORIGEN']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_OPERACION']}</td>
                    <td style="padding: 0px !important;">{$value['INST_MONETARIO']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_CREDITO']}</td>
                    <td style="padding: 0px !important;">{$value['PRODUCTO']}</td>
                    <td style="padding: 0px !important;">{$value['PAIS_ORIGEN']}</td>
                    <td style="padding: 0px !important;">{$value['PAIS_DESTINO']}</td>
                    <td style="padding: 0px !important;">{$value['ALTA_CONTRATO']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_CONTRATO']}</td>
                    
                    <td style="padding: 0px !important;">{$value['TIPO_DOC']}</td>
                    <td style="padding: 0px !important;">{$value['LATLON']}</td>
                    <td style="padding: 0px !important;">{$value['LOCALIZACION']}</td>
                    <td style="padding: 0px !important;">{$value['CP']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::render("perfil_transaccional_cultiva_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("perfil_transaccional_cultiva_busqueda");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("perfil_transaccional_consulta_cultiva_all");
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


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Consulta de Pagos FecNac Cultiva');
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
        header('Content-Disposition: attachment;filename="Consulta de Pagos FecNac Cultiva '.$controlador.'.xlsx"');
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
        $columna = array(
            'A','B','C', 'D', 'E','F', 'G',
            'H','I', 'J', 'K','L','M','N', 'O',
            'P', 'Q','R','S','T','U','V','W',
            'X','Y', 'Z', 'AA','AB', 'AC','AD',
            'AE','AF','AG','AH','AI', 'AJ',
            'AK','AL', 'AM','AN','AO', 'AP','AQ', 'AR'
        );

        $nombreColumna = array(
            'ID CLIENTE','CUENTA','Origen','NOMBRE','ADICIONAL',
            'APELLIDO PATERNO','APELLIDO MATERNO','TIPO DE PERSONA',
            'RFC','CURP','RAZON SOCIAL O DENOMINACION','FECHA DE NACIMIENTO OCONSTITUCION',
            'NACIONALIDAD', 'DOMICILIO(calle- número exterior e interior (si aplica) y código postal)',
            'COLONIA', 'CIUDAD O POBLACION','PAIS','ESTADO',
            'TELEFONO OFICINA/PARTICULAR','ACTIVIDAD ECONOMICA',
            'CALIFICACIÓN','FECHA ALTA',
            'SUCURSAL','GENERO','CORREO ELEC.',
            'FIRMA ELEC.','PROFESION','OCUPACION','PAIS NAC.',
            'EDO. NAC.', 'LUGAR NAC.', 'NUMERO DE DOCUMENTO',
            'CONOCIMIENTO CLIENTE','REGISTR O NACIONAL DE INMIGRACION',
            'CUENTA ORIGINAL','SITUACIÓN CLIENTE',
            'TIPO DOCUMENTO','INDICADOR EMPLEO','EMPRESA LABORA(Ó)',
            'INDICADOR GOBIERNO','PUESTO','FECHA INICIO','FEH FIN','CP');


        $nombreCampo = array(
            'CDGCL',
            'GRUPO',
            'ORIGEN',
            'NOMBRE',
            'ADICIONAL',
            'A_PATERNO',
            'A_MATERNO',
            'TIPO_PERSONA',
            'RFC',
            'CURP',
            'RAZON_SOCIAL',
            'FECHA_NAC',
            'NACIONALIDAD',
            'DOMICILIO',
            'COLONIA',
            'CIUDAD',
            'PAIS',
            'SUC_ID_ESTADO',
            'TELEFONO',
            'ID_ACTIVIDAD_ECONO',
            'CALIFICACION',
            'ALTA',
            'ID_SUCURSAL_SISTEMA',
            'GENERO',
            'CORREO_ELECTRONICO',
            'FIRMA_ELECT',
            'PROFESION',
            'OCUPACION',
            'PAIS_NAC',
            'EDO_NAC',
            'LUGAR_NAC',
            'NUMERO_DOCUMENTO',
            'CONOCIMIENTO',
            'INMIGRACION',
            'CUENTA_ORIGINAL',
            'SITUACION_CREDITO',
            'TIPO_DOCUMENTO',
            'INDICADOR_EMPLEO',
            'EMPRESAS',
            'INDICADOR_GOBIERNO',
            'PUESTO',
            'FECHA_INICIO',
            'FEH_FIN',
            'CP'
        );

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Identificacion de Clientes Cultiva');
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

        $Layoutt = OperacionesDao::ConsultarClientes($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Identificacion de Clientes Cultiva '.$controlador.'.xlsx"');
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

    public function generarExcelClientesCR(){

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
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');
        $nombreColumna = array( 'CLIENTE','NUMERO DE CUENTA- CONTRATO-OPERACIÓN- PÓLIZA O NSS2','NO.CUENTA RELACIONADA',
            'NOMBRE DEL TITULAR DE LA CUENTA O DE LA PERSONA RELACIONAD A', 'ADICIONAL',
            'APELLIDO PATERNO', 'MATERNO', 'DESCRIPCION DE LA OPERACIÓN*', 'IDENTIFICA CUENTA como interna', 'CONSERVA CUENTA ORIGINAL', 'OFICINA CLIENTE', 'FECHA INICIO OPERACIÓN');

        $nombreCampo = array('CLIENTE','GRUPO','CUENTA_RELACION','NOMBRE',
            'ADICIONAL',
            'A_PATERNO',
            'A_MATERNO',
            'DESCRIPCION_OPERACION',
            'IDENTIFICA_CUENTA',
            'CONSERVA',
            'OFICINA_CLIENTE','FECHA_INICIO_OPERACION'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Cuentas Relacionadas PLD Cultiva');
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

        $Layoutt = OperacionesDao::CuentasRelacionadas($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Cuentas Relacionadas Cultiva'.$controlador.'.xlsx"');
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

    public function generarExcelClientesPT(){

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
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI');
        $nombreColumna = array(
            'ID CLIENTE',
            'Cuenta',
            'Nombre Completo',
            'INSTRUMENTO',
            'TIPO MONEDA',
            'T/CAMBIO',
            'MONTO Prest/INV.',
            'PLAZO',
            'FRECUENCIA',
            'TOTAL PAGOPS',
            'Monto C/Pago',
            'AUT. ADELANTAR PAGO',
            'NO.APORTACIONES',
            'Monto APORTACIONES',
            'CUOTA DEL PAGO / INVERSION',
            'SALDO',
            'SUCURSAL',
            'ORIGEN RECURSOS',
            'DESTINO RECURSOS',
            'FECHA INICIO CREDITO',
            'FECHA FIN CREDITO',
            'Destino/nacionalidad',
            'Origen/nacionalidad2',
            'TIPO OPERACIÓN',
            'INSTR MONETARIOS',
            'TIPO CRÉDITO',
            'CLAVE PRODUCTO',
            'PAIS ORIGEN',
            'PAIS DESTINO',
            'ALTA CONTRATO',
            'TIPO DE CONTRATO',
            'TIPO DE DOCUMENTO/FOLIO',
            'LATITUD/LONGITUD',
            'LOCALIZACION',
            'CP'
            );

        $nombreCampo = array(
            'CDGCL', 'GRUPO', 'NOMBRE', 'INSTRUMENTO', 'TIPO_MONEDA', 'T_CAMBIO', 'MONT_PRESTAMO',
            'PLAZO', 'FRECUENCIA', 'TOTAL_PAGOS', 'MONTO_FIN_PAGO', 'ADELANTAR_PAGO', 'NUMERO_APORTACIONES',
            'MONTO_APORTACIONES', 'CUOTA_PAGO', 'SALDO', 'ID_SUCURSAL_SISTEMA', 'ORIGEN_RECURSO',
            'DESTINO_RECURSOS', 'FECHA_INICIO_CREDITO', 'FECHA_FIN', 'DESTINO', 'ORIGEN', 'TIPO_OPERACION', 'INST_MONETARIO', 'TIPO_CREDITO',
            'PRODUCTO', 'PAIS_ORIGEN', 'PAIS_DESTINO', 'ALTA_CONTRATO', 'TIPO_CONTRATO', 'TIP_DOC', 'LATLON', 'LOCALIZACION', 'CP'
        );






        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Perfil Transaccional PLD Cultiva');
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

        $Layoutt = OperacionesDao::ConsultarPerfilTransaccional($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Perfil Transaccional Cultiva'.$controlador.'.xlsx"');
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
