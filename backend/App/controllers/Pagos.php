<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Pagos AS PagosDao;


class Pagos extends Controller
{

    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());

    }

    public function index()
    {
        $extraHeader = <<<html
        <title>Administración de Pagos</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
        ponerElCursorAlFinal('Credito');
      
        function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
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
       function FunDelete_Pago(secuencia, fecha, usuario) {
             credito = getParameterByName('Credito');
             user = usuario;
             ////////////////////////////
             swal({
              title: "¿Segúro que desea eliminar el registro seleccionado?",
              text: "",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: "POST",
                        url: "/Pagos/Delete/",
                        data: {"cdgns" : credito, "fecha" : fecha, "secuencia": secuencia, "usuario" : user},
                        success: function(response){
                            if(response == '1 Proceso realizado exitosamente')
                                {
                                    swal("Registro fue eliminado correctamente", {
                                      icon: "success",
                                    });
                                    location.reload();
                                    
                                }
                            else
                                {
                                    swal(response, {
                                      icon: "error",
                                    });
                                    
                                }
                        }
                    });
                  /////////////////
              } else {
                swal("No se pudo eliminar el registro");
              }
            });
             }
        function enviar_add(){	
             monto = document.getElementById("monto").value; 
             
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             swal("Atención", "Ingrese un monto mayor a $0", "warning");
                             document.getElementById("monto").focus();
                             
                        }
                }
            else
                {
                    texto = $("#ejecutivo :selected").text();
                   
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosAdd/',
                    data: $('#Add').serialize()+ "&ejec="+texto,
                    success: function(respuesta) {
                         if(respuesta=='1 Proceso realizado exitosamente'){
                      
                        document.getElementById("monto").value = "";
                        
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                        $('#modal_agregar_pago').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                            location.reload();
                        }
                    }
                    });
                }
    }
        function enviar_edit(){	
           
             monto = document.getElementById("monto_e").value; 
             
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             swal("Atención", "Ingrese un monto mayor a $0", "warning");
                             document.getElementById("monto_e").focus();
                        }
                }
            else
                {
                    texto = $("#ejecutivo_e :selected").text();
                   
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosEdit/',
                    data: $('#Edit').serialize()+ "&ejec_e="+texto,
                    success: function(respuesta) {
                         if(respuesta=='1 Proceso realizado exitosamente'){
                      
                        document.getElementById("monto_e").value = "";
                        
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                        $('#modal_editar_pago').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                        }
                    }
                    });
                }
    }
        function Desactivado()
         {
             swal("Atención", "Usted no puede modificar este registro", "warning");
         }
         function InfoAdmin()
         {
             swal("Info", "Este registro fue capturado por una administradora en caja", "info");
         }
         function InfoPhone()
         {
             swal("Info", "Este registro fue capturado por un ejecutivo en campo y procesado por una administradora", "info");
         }
         
         function BotonPago(estatus)
         {
            if(estatus == 'LIQUIDADO')
                {
                    select = $("#tipo");
                     select.empty();
                     select.append($("<option>", {
                        value: 'M',
                        text: 'MULTA'
                      }));
                }
         }
    
      </script>
html;


        $credito = $_GET['Credito'];
        $tabla = '';

        $fechaActual = date("Y-m-d");
        $horaActual = date("H:i:s");
        $dia = date("N");


        if ($dia == 1)
        {
            $date_past = strtotime('-3 days', strtotime($fechaActual));
            $date_past = date('Y-m-d', $date_past);

            $inicio_f = $date_past;
            $fin_f = $fechaActual;
        }
        else
        {
            $date_past = strtotime('-2 days', strtotime($fechaActual));
            $date_past = date('Y-m-d', $date_past);

            $inicio_f = $date_past;
            $fin_f = $fechaActual;
        }

        $status = PagosDao::ListaEjecutivosAdmin($credito);
        $getStatus = '';

        foreach ($status[0] as $key => $val2) {
            if($status[1] == $val2['ID_EJECUTIVO'])
            {
                $select = 'selected';
            }
            else
            {
                $select = '';
            }

            $getStatus .= <<<html
                <option $select value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }
        if ($credito != '') {
            $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);


            if($AdministracionOne['NO_CREDITO'] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::set('usuario', $this->__usuario);
                View::render("pagos_admin_busqueda_message");

            }
            else
            {
            $Administracion = PagosDao::ConsultarPagosAdministracion($credito);
            foreach ($Administracion as $key => $value) {

                if($value['FIDENTIFICAPP'] ==  NULL)
                {
                    $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                    $mensaje = 'InfoAdmin();';
                }
                else
                {
                    $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                    $mensaje = 'InfoPhone();';
                }

                if($value['DESIGNATION_ADMIN'] == 'SI')
                {/////
                    /// /
                    ///
                    ///
                    /// aqui poner que si los pagos son de app no se pueden modificar, consulte con operaciones
                    ///
                    ///
                    ///
                    $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
html;
                }
                else
                {
                    $date_past = strtotime('-3 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);

                    $fecha_base = strtotime($value['FECHA']);
                    $fecha_base = date('Y-m-d', $fecha_base);

                    $inicio_f = $date_past;

                    if($inicio_f == $fecha_base)
                    {
                        $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
html;
                    }
                    else
                    {
                        $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
html;
                    }
                }
                $monto =number_format($value['MONTO'],2);

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap onclick="{$mensaje}">{$medio}</td>
                    <td style="padding: 0px !important;" width="45" nowrap>{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_TABLA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;">{$value['EJECUTIVO']}</td>
                    <td style="padding: 0px !important;" class="center">{$editar}</td>
                </tr>
html;
            }

                View::set('tabla', $tabla);
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('inicio_f', $inicio_f);
                View::set('fin_f', $fin_f);
                View::set('status', $getStatus);
                View::set('usuario', $this->__usuario);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_admin_busqueda");
            }

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_admin_all");
        }
    }

    public function PagosConsulta()
    {
        $extraHeader = <<<html
        <title>Consulta de Pagos</title>
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
            sucursal = getParameterByName('id_sucursal');
            
             $("#export_excel_consulta").click(function(){
             
              $('#all').attr('action', '/Pagos/generarExcelConsulta/?Inicial='+fecha1+'&Final='+fecha2+'&Sucursal='+sucursal);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
             
        });
      
            function Validar(){
                
                fecha1 = moment(document.getElementById("Inicial").innerHTML = inputValue);
                fecha2 = moment(document.getElementById("Final").innerHTML = inputValue);
                
                dias = fecha2.diff(fecha1, 'days');
                alert(dias);
                
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
         
         
         id_sucursal = getParameterByName('id_sucursal');
         if(id_sucursal != '')
             {
                 const select_e = document.querySelector('#id_sucursal');
                select_e.value = id_sucursal;
             }
         
    
      </script>
html;

        $fechaActual = date('Y-m-d');
        $id_sucursal = $_GET['id_sucursal'];
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];


        $sucursales = PagosDao::ListaSucursales($this->__usuario);
        $getSucursales = '';
        if($this->__perfil == 'ADMIN')
            {
                $getSucursales .= <<<html
                <option value="">TODAS</option>
html;
            }


        foreach ($sucursales as $key => $val2) {
            $getSucursales .= <<<html
                <option value="{$val2['ID_SUCURSAL']}">{$val2['SUCURSAL']}</option>
html;
        }


        if ($Inicial != '' && $Final != '') {
            $Consulta = PagosDao::ConsultarPagosFechaSucursal($id_sucursal, $Inicial, $Final);

            foreach ($Consulta as $key => $value) {
                if($value['FIDENTIFICAPP'] ==  NULL)
                {
                    $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                    $mensaje = 'InfoAdmin();';
                }
                else
                {
                    $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                    $mensaje = 'InfoPhone();';
                }

                $monto =number_format($value['MONTO'],2);
                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap onclick="{$mensaje}">{$medio}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_SUCURSAL']}</td>
                    <td style="padding: 0px !important;" width="45" nowrap>{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;">{$value['EJECUTIVO']}</td>
                    <td style="padding: 0px !important;">{$value['FREGISTRO']}</td>
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('getSucursales', $getSucursales);
                View::set('fechaActual', $fechaActual);
                View::render("pagos_consulta_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('getSucursales', $getSucursales);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_consulta_busqueda");
            }

        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::set('getSucursales', $getSucursales);
            View::render("pagos_consulta_all");
        }
    }

    public function PagosAdd(){
        $pagos = new \stdClass();
        $credito = MasterDom::getDataAll('cdgns');
        $pagos->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo');
        $pagos->_ciclo = $ciclo;

        $fecha = MasterDom::getDataAll('Fecha');
        $pagos->_fecha = $fecha;

        $monto = MasterDom::getDataAll('monto');
        $pagos->_monto = $monto;

        $tipo = MasterDom::getDataAll('tipo');
        $pagos->_tipo = $tipo;

        $nombre = MasterDom::getDataAll('nombre');
        $pagos->_nombre = $nombre;

        $usuario = $this->__usuario;
        $pagos->_usuario = $usuario;

        $pagos->_ejecutivo = MasterDom::getData('ejecutivo');

        $pagos->_ejecutivo_nombre = MasterDom::getData('ejec');

        $id = PagosDao::insertProcedure($pagos);
        return $id;
    }

    public function PagosEdit(){
        $pagos = new \stdClass();


        $secuencia = MasterDom::getDataAll('secuencia_e');
        $pagos->_secuencia = $secuencia;

        $credito = MasterDom::getDataAll('cdgns_e');
        $pagos->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo_e');
        $pagos->_ciclo = $ciclo;

        $fecha = MasterDom::getDataAll('Fecha_e');
        $pagos->_fecha = $fecha;

        $fecha_aux = MasterDom::getDataAll('Fecha_e_r');
        $pagos->_fecha_aux = $fecha_aux;

        $monto = MasterDom::getDataAll('monto_e');
        $pagos->_monto = $monto;

        $tipo = MasterDom::getDataAll('tipo_e');
        $pagos->_tipo = $tipo;

        $nombre = MasterDom::getDataAll('nombre_e');
        $pagos->_nombre = $nombre;

        $usuario = $this->__usuario;
        $pagos->_usuario = $usuario;

        $pagos->_ejecutivo = MasterDom::getData('ejecutivo_e');

        $pagos->_ejecutivo_nombre = MasterDom::getData('ejec_e');

        $id = PagosDao::EditProcedure($pagos);
        return $id;
    }

    public function Delete(){

        $cdgns = $_POST['cdgns'];
        $fecha = $_POST['fecha'];
        $usuario = $_POST['usuario'];
        $secuencia = $_POST['secuencia'];

        $id = PagosDao::DeleteProcedure($cdgns, $fecha, $usuario, $secuencia);
        return $id;

    }

    public function PagosRegistro()
    {
        $extraHeader = <<<html
        <title>Registro de Pagos</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
        ponerElCursorAlFinal('Credito');
      
        function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
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
        function FunDelete_Pago(secuencia, fecha, usuario) {
             credito = getParameterByName('Credito');
             user = usuario;
             ////////////////////////////
             swal({
              title: "¿Segúro que desea eliminar el registro seleccionado?",
              text: "",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: "POST",
                        url: "/Pagos/Delete/",
                        data: {"cdgns" : credito, "fecha" : fecha, "secuencia": secuencia, "usuario" : user},
                        success: function(response){
                            if(response == '1 Proceso realizado exitosamente')
                                {
                                    swal("Registro fue eliminado correctamente", {
                                      icon: "success",
                                    });
                                    location.reload();
                                    
                                }
                            else
                                {
                                    swal(response, {
                                      icon: "error",
                                    });
                                    
                                }
                        }
                    });
                  /////////////////
              } else {
                swal("No se pudo eliminar el registro");
              }
            });
             }
        function enviar_add(){	
             monto = document.getElementById("monto").value; 
             
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             swal("Atención", "Ingrese un monto mayor a $0", "warning");
                             document.getElementById("monto").focus();
                             
                        }
                }
            else
                {
                    texto = $("#ejecutivo :selected").text();
                   
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosAdd/',
                    data: $('#Add').serialize()+ "&ejec="+texto,
                    success: function(respuesta) {
                         if(respuesta=='1 Proceso realizado exitosamente'){
                      
                        document.getElementById("monto").value = "";
                        
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                         location.reload();
                        
                        }
                        else {
                        $('#modal_agregar_pago').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                            document.getElementById("monto").value = "";
                        }
                    }
                    });
                }
    }
        function enviar_edit(){	
           
             monto = document.getElementById("monto_e").value; 
             
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             swal("Atención", "Ingrese un monto mayor a $0", "warning");
                             
                        }
                }
            else
                {
                    texto = $("#ejecutivo_e :selected").text(); 
             
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosEdit/',
                    data: $('#Edit').serialize()+ "&ejec_e="+texto,
                    success: function(respuesta) {
                         if(respuesta=='1 Proceso realizado exitosamente'){
                      
                        document.getElementById("monto_e").value = "";
                        
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                        $('#modal_editar_pago').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                        }
                    }
                    });
                }
    }
        function Desactivado()
         {
             swal("Atención", "Usted no puede modificar este registro", "warning");
         }
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


        $credito = $_GET['Credito'];
        $tabla = '';

        $fechaActual = date("Y-m-d");
        $horaActual = date("H:i:s");
        $dia = date("N");

        if($this->__cdgco == '025' || $this->__cdgco == '026' || $this->__cdgco == '003'  || $this->__cdgco == '014'  || $this->__cdgco == '007'  || $this->__cdgco == '016')
        {
            // 025 - SUC TOLUCA 2// 026 - SUC TOLUCA 3 // 014 - SUC TOLUCA // 007 ZINA // 016
            if($horaActual <= '11:10:00')
            {
                if ($dia == 1)
                {
                    $date_past = strtotime('-3 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
                else
                {
                    $date_past = strtotime('-1 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }

                $inicio_f = $date_past;
                $fin_f = $fechaActual;
            }
            else
            {
                $inicio_f = $fechaActual;
                $fin_f = $fechaActual;
            }

        }
        else
        {
            if($horaActual <= '10:00:00')
            {
                if ($dia == 1)
                {
                    $date_past = strtotime('-3 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
                else
                {
                    $date_past = strtotime('-1 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }

                $inicio_f = $date_past;
                $fin_f = $fechaActual;
            }
            else
            {
                $inicio_f = $fechaActual;
                $fin_f = $fechaActual;
            }
        }



        $status = PagosDao::ListaEjecutivosAdmin($credito);
        foreach ($status[0] as $key => $val2) {
            if($status[1] == $val2['ID_EJECUTIVO'])
            {
                $select = 'selected';
            }
            else
            {
                $select = '';
            }

            $getStatus .= <<<html
                <option $select value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }
        if ($credito != '') {


            $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);

            if($AdministracionOne['NO_CREDITO'] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::set('usuario', $this->__usuario);
                View::render("pagos_registro_busqueda_message");
            }
            else
            {
                $Administracion = PagosDao::ConsultarPagosAdministracion($credito);
                foreach ($Administracion as $key => $value) {

                    if($value['FIDENTIFICAPP'] ==  NULL)
                    {
                        $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                        $mensaje = 'InfoAdmin();';
                    }
                    else
                    {
                        $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                        $mensaje = 'InfoPhone();';
                    }

                    if($value['DESIGNATION'] == 'SI')
                    {
                        /////
                        /// /
                        ///
                        ///
                        /// aqui poner que si los pagos son de app no se pueden modificar, consulte con operaciones
                        ///
                        ///
                        ///
                        $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
html;
                    }
                    else
                    {
                        $date_past_b = strtotime('-3 days', strtotime($fechaActual));
                        $date_past_b = date('Y-m-d', $date_past_b);

                        $fecha_base = strtotime($value['FECHA']);
                        $fecha_base = date('Y-m-d', $fecha_base);

                        $inicio_b = $date_past_b;

                        if($inicio_b == $fecha_base)
                        {
                            if($horaActual <= '10:05:00')
                            {
                                $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
html;
                            }
                            else
                            {
                                $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
html;
                            }

                        }
                        else
                        {
                            $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
html;
                        }
                    }

                    $monto = number_format($value['MONTO'], 2);
                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap onclick="{$mensaje}">{$medio}</td>
                    <td style="padding: 0px !important;" width="45" nowrap>{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;">{$value['EJECUTIVO']}</td>
                    <td style="padding: 0px !important;" class="center">{$editar}</td>
                </tr>
html;
                }

                View::set('tabla', $tabla);
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('inicio_f', $inicio_f);
                View::set('fin_f', $fin_f);
                View::set('fechaActual', $fechaActual);
                View::set('status', $getStatus);
                View::set('usuario', $this->__usuario);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_registro_busqueda");
            }

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_registro_all");
        }
    }

    public function CorteCaja()
    {
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
       
        function enviar_add(){	
             monto = document.getElementById("monto").value; 
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             alertify.confirm('Ingresa un monto, mayor a $0');
                        }
                }
            else
                {
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosAdd/',
                    data: $('#Add').serialize(),
                    success: function(respuesta) {
                        if(respuesta=='ok'){
                        alert('enviado'); 
                        document.getElementById("monto").value = "";
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                         location.reload();
                         
                        }
                        else {
                        $('#addnew').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                            document.getElementById("monto").value = "";
                        }
                    }
                    });
                }
    }
        function FunprecesarPagos() {
           alert("procesando...");
           ///////
           Swal.fire({
  title: 'Auto close alert!',
  'html':
    'I will close in <strong></strong> seconds.<br/><br/>' +
    '<button id="increase" class="btn btn-warning">' +
    'I need 5 more seconds!' +
    '</button><br/><br/>' +
    '<button id="stop" class="btn btn-danger">' +
    'Please stop the timer!!' +
    '</button><br/><br/>' +
    '<button id="resume" class="btn btn-success" disabled>' +
    'Phew... you can restart now!' +
    '</button><br/><br/>' +
    '<button id="toggle" class="btn btn-primary">' +
    'Toggle' +
    '</button>',
  timer: 10000,
  didOpen: () => {
        const content = Swal.getHtmlContainer()
    const $ = content.querySelector.bind(content)

    const stop = $('#stop')
    const resume = $('#resume')
    const toggle = $('#toggle')
    const increase = $('#increase')

    Swal.showLoading()

    function toggleButtons () {
        stop.disabled = !Swal.isTimerRunning()
      resume.disabled = Swal.isTimerRunning()
    }

    stop.addEventListener('click', () => {
            Swal.stopTimer()
      toggleButtons()
    })

    resume.addEventListener('click', () => {
            Swal.resumeTimer()
      toggleButtons()
    })

    toggle.addEventListener('click', () => {
            Swal.toggleTimer()
      toggleButtons()
    })

    increase.addEventListener('click', () => {
            Swal.increaseTimer(5000)
    })

    timerInterval = setInterval(() => {
            Swal.getHtmlContainer().querySelector('strong')
            .textContent = (Swal.getTimerLeft() / 1000)
                .toFixed(0)
    }, 100)
  },
  willClose: () => {
        clearInterval(timerInterval)
  }
})
           ///////////
           
        }
      </script>
html;


        $consolidado = $_GET['Consolidado'];
        $tabla = '';
        $CorteCajaById = PagosDao::getAllCorteCajaByID($consolidado);


        if ($consolidado != '') {
            $CorteCaja = PagosDao::getAllByIdCorteCaja(1);

            foreach ($CorteCaja as $key => $value) {

                //////////////////////////////////////
                if($value['TIPO'] == 'P')
                {
                    $tipo_pago = 'PAGO';
                }
                if($value['TIPO_PAGO'] == 'G')
                {
                    $tipo_pago = 'GARANTÍA';
                }
                if($value['TIPO_PAGO'] == 'M')
                {

                }
                if($value['TIPO_PAGO'] == 'A')
                {

                }
                if($value['TIPO_PAGO'] == 'W')
                {

                }
                if($value['ESTATUS_CAJA'] == '0')
                {
                    if($value['INCIDENCIA'] == 1)
                    {
                        $estatus = 'PENDIENTE, CON MODIFICACION';
                    }
                    else{
                        $estatus = 'PENDIENTE';
                    }


                }
                //////////////////////////////////////

                if($value['INCIDENCIA'] == 1)
                {
                    $incidencia = '<br><span class="count_top" style="font-size: 20px; color: gold"><i class="fa fa-warning"></i></span> <b>Incidencia:</b>'.$value['COMENTARIO_INCIDENCIA'];
                    $monto = '<span class="count_top" style="font-size: 16px; color: #017911">Monto a recibir: $' .number_format($value['NUEVO_MONTO']). '</span><br>
                              <span class="count_top" style="font-size: 15px; color: #ff0066">Monto registrado: $' .number_format($value['MONTO']).'</span>';
                    $botones = "";
                }else{
                    $incidencia = '';
                    $monto = '$ '.number_format($value['MONTO']);

                    $botones =  <<<html
                    
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$tipo_pago}', '{$value['MONTO']}','{$estatus}', '{$value['EJECUTIVO']}');"><i class="fa fa-edit"></i></button>
                
html;
                }
                $tabla .= <<<html
                <tr>
                <td><span class="count_top" style="font-size: 25px"><i class="fa fa-mobile"></i></span></td>
                <td> {$value['FECHA']}</td>
                <td> {$value['CDGNS']}</td>
                <td> {$value['NOMBRE']}</td>
                <td> {$value['CICLO']}</td>
                <td> {$tipo_pago}</td>
                <td>{$monto}</td>
                <td>{$estatus}</td>
                <td><i class="fa fa-user"></i>   {$value['EJECUTIVO']} {$incidencia}</td>
                
                <td class="center">
                {$botones}
                </td>
                </tr>
html;
            }

            /////////////////////////////////////////////////////////////////
            /// Sirve para decir que la consulta viene vacia, mandar mernsaje de vacio
            if($CorteCaja[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('CorteCajaById', $CorteCajaById);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            }

        }
        else {

            $CorteCaja = PagosDao::getAllCorteCaja();

            foreach ($CorteCaja as $key => $value) {
                $tabla .= <<<html
                <tr>
                <td><span class="count_top" style="font-size: 30px"><i class="fa fa-mobile"></i></span></td>
                <td> {$value['NUM_PAG']}</td>
                <td><i class="fa fa-user"></i>  {$value['CDGPE']}</td>
                <td>$ {$value['MONTO_TOTAL']}</td>
                <td>$ {$value['MONTO_PAGO']}</td>
                <td>$ {$value['MONTO_GARANTIA']}</td>
                <td>$ {$value['MONTO_DESCUENTO']}</td>
                <td>$ {$value['MONTO_REFINANCIAMIENTO']}</td>
                <td></td>
                <td class="center" >
                    <a href="/Pagos/CorteCaja/?Consolidado={$value['CDGPE']}" type="submit" name="id_coordinador" class="btn btn-success"><span class="fa fa-product-hunt" style="color:white"></span> Liberar Pagos</a>
                </td>
                </tr>
            
html;
            }
            /////////////////////////////////////////////////////////////////
            /// Sirve para decir que la consulta viene vacia, mandar mernsaje de vacio
            if($CorteCaja[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_all");////CAmbiar a una en donde diga que no hay registros
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_all");
            }
            //////////////////////////////////////////////////////////////////
        }


    }

    public function Layout()
    {

        $extraHeader = <<<html
        <title>Layout Contable</title>
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
                    [21, 50, -1],
                    [21, 50, 'Todos'],
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
            
             $("#export_excel").click(function(){
              $('#all').attr('action', '/Pagos/generarExcel/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
        });
          
          Inicial.max = new Date().toISOString().split("T")[0];
          Final.max = new Date().toISOString().split("T")[0];
         
    
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];

        if($Inicial == '' && $Final == '')
        {
            View::set('fechaActual', $fechaActual);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_layout_all");

        }
        else
        {
            ///////////////////////////////////////////////////////////////////////////////////
            $tabla = '';

            $Layout = PagosDao::GeneraLayoutContable($Inicial, $Final);
            if ($Layout != '') {
                foreach ($Layout as $key => $value) {

                    $monto = number_format($value['MONTO'], 2);
                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['REFERENCIA']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['MONEDA']}</td>
                </tr>
html;
                }
                if($Layout[0] == '')
                {
                    View::set('header', $this->_contenedor->header($extraHeader));
                    View::set('footer', $this->_contenedor->footer($extraFooter));
                    View::set('fechaActual', $fechaActual);
                    View::render("pagos_layout_busqueda_message");
                }
                else
                {
                    View::set('tabla', $tabla);
                    View::set('Inicial', $Inicial);
                    View::set('Final', $Final);
                    View::set('header', $this->_contenedor->header($extraHeader));
                    View::set('footer', $this->_contenedor->footer($extraFooter));
                    View::render("pagos_layout_busqueda");
                }


            } else {
                View::set('fechaActual', $fechaActual);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_layout_all");

            }

            ////////////////////////////////////////////////////
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

        $controlador = "Pagos";
        $columna = array('A','B','C','D');
        $nombreColumna = array('Fecha','Referencia','Monto','Moneda');
        $nombreCampo = array('FECHA','REFERENCIA','MONTO','MONEDA');



        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila +=1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        $Layoutt = PagosDao::GeneraLayoutContable($fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Layout '.$controlador.'.xlsx"');
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

    public function generarExcelConsulta(){

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];
        $Sucursal = $_GET['Sucursal'];

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

        $controlador = "Pagos";
        $columna = array('A','B','C','D','E','F','G','H','I','J');
        $nombreColumna = array('Sucursal','Codigo','Fecha','Cliente', 'Nombre', 'Ciclo', 'Monto', 'Tipo', 'Ejecutivo', 'Registro');
        $nombreCampo = array('NOMBRE_SUCURSAL','SECUENCIA','FECHA','CDGNS','NOMBRE','CICLO','MONTO', 'TIPO', 'EJECUTIVO', 'FREGISTRO');


        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila +=1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        $Layoutt = PagosDao::ConsultarPagosFechaSucursal($Sucursal, $fecha_inicio, $fecha_fin);
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
        header('Content-Disposition: attachment;filename="Consulta Pagos Global '.$controlador.'.xlsx"');
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
