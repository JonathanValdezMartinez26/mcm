<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Pagos as PagosDao;
use \App\models\CallCenter as CallCenterDao;



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


        if ($dia == 1) {
            $date_past = strtotime('-3 days', strtotime($fechaActual));
            $date_past = date('Y-m-d', $date_past);

            $inicio_f = $date_past;
            $fin_f = $fechaActual;
        } else {

            ///////agregar funcion para saber si es festivo
            $date_past = strtotime('-4 days', strtotime($fechaActual)); /////Aqui se dan o se quoitan dias
            $date_past = date('Y-m-d', $date_past);

            $inicio_f = $date_past;
            $fin_f = $fechaActual;
        }

        $status = PagosDao::ListaEjecutivosAdmin($credito);
        $getStatus = '';



        foreach ($status[0] as $key => $val2) {
            if ($status[1] == $val2['ID_EJECUTIVO']) {
                $select = 'selected';
            } else {
                $select = '';
            }

            $getStatus .= <<<html
                <option $select value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }
        if ($credito != '') {
            $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);

            $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
            $situacion_credito = $AdministracionOne[0]['SITUACION_NOMBRE'];


            if ($AdministracionOne[0]['NO_CREDITO'] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::set('usuario', $this->__usuario);
                View::render("pagos_admin_busqueda_message");
            } else {
                $Administracion = PagosDao::ConsultarPagosAdministracion($credito, $hora_cierre);
                foreach ($Administracion as $key => $value) {

                    if ($value['FIDENTIFICAPP'] ==  NULL) {
                        $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                        $mensaje = 'InfoAdmin();';
                    } else {
                        $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                        $mensaje = 'InfoPhone();';
                    }

                    if ($value['DESIGNATION_ADMIN'] == 'SI') { /////
                        /// /
                        ///
                        ///
                        /// aqui poner que si los pagos son de app no se pueden modificar, consulte con operaciones
                        ///
                        ///['SITUACION_NOMBRE']
                        ///
                        $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
html;
                    } else {
                        $date_past = strtotime('-4 days', strtotime($fechaActual));
                        $date_past = date('Y-m-d', $date_past);

                        $fecha_base = strtotime($value['FECHA']);
                        $fecha_base = date('Y-m-d', $fecha_base);

                        $inicio_f = $date_past;




                        if ($inicio_f == $fecha_base) {
                            $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
html;
                        } else {
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
                    <td style="padding: 0px !important;">{$value['FECHA_TABLA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;">{$value['EJECUTIVO']}</td>
                    <td style="padding: 0px !important;"><span class="fa fa-key"></span> {$value['NOMBRE_CDGPE']}</td>
                    <td style="padding: 0px !important;" class="center">{$editar}</td>
                </tr>
html;
                }
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('inicio_f', $inicio_f);
                View::set('fin_f', $fin_f);
                View::set('status', $getStatus);
                View::set('usuario', $this->__usuario);
                View::render("pagos_admin_busqueda");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_admin_all");
        }
    }

    public function AjusteHoraCierre()
    {
        $extraHeader = <<<html
        <title>Ajuste Cierre Caja</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
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
            
        });
       
        function enviar_add_horario(){	
             sucursal = document.getElementById("sucursal").value; 
             
            if(sucursal == '')
                {
                    
                      swal("Atención", "Ingrese un monto mayor a $0", "warning");
                      document.getElementById("monto").focus();
                        
                }
            else
                {
                    
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/HorariosAdd/',
                    data: $('#Add_AHC').serialize(),
                    success: function(respuesta) {
                         if(respuesta=='1'){
                      
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                         swal(respuesta, {
                                      icon: "error",
                                    });
                         //location.reload();
                         
                        }
                    }
                    });
                }
    }
    
        function enviar_update_horario(){	
          
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/HorariosUpdate/',
                    data: $('#Update_AHC').serialize(),
                    success: function(respuesta) {
                         if(respuesta=='1'){
                      
                         swal("Registro actualizado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                         swal(respuesta, {
                                      icon: "error",
                                    });
                         //location.reload();
                         
                        }
                    }
                    });
    }
      
      
      </script>
html;

        $tabla = '';
        $horaActual = date("H:i:s");
        $opciones_suc = '';

        $ComboSucursales = CallCenterDao::getComboSucursalesHorario();


        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
        }

        $Administracion = PagosDao::ConsultarHorarios();


        foreach ($Administracion as $key => $value) {

            if ($value['HORA_PRORROGA'] == 'NULL') {
                $prorroga = 'NO TIENE';
            } else {
                $prorroga = $value['HORA_PRORROGA'];
            }

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CODIGO']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">De (08:00:00 a.m) A (<strong>{$value['HORA_CIERRE']}</strong> a.m)</td>
                    <td style="padding: 0px !important;">$prorroga</td>
                    <td style="padding: 0px !important;">{$value['FECHA_ALTA']}</td>
                     <td style="padding: 0px !important;">
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarHorario('{$value['CDGCO']}', '{$value['NOMBRE']}' , '{$value['HORA_CIERRE']}');"><i class="fa fa-edit"></i></button>
                     </td>
                </tr>
html;
        }

        View::set('tabla', $tabla);
        View::set('usuario', $this->__usuario);
        View::set('opciones_suc', $opciones_suc);

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("horarios_caja_sucursal");
    }

    public function DiasFestivos()
    {
        $extraHeader = <<<html
        <title>Días Festivos</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
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
            
        });

      </script>
html;

        $tabla = '';


        $Administracion = PagosDao::ConsultarDiasFestivos();


        foreach ($Administracion as $key => $value) {

            if ($value['HORA_PRORROGA'] == 'NULL') {
                $prorroga = 'NO TIENE';
            } else {
                $prorroga = $value['HORA_PRORROGA'];
            }

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['DESCRIPCION']}</td>
                    <td style="padding: 0px !important;"><strong>{$value['FECHA_CAPTURA']}</strong></td>
                     <td style="padding: 0px !important;">
                        <button style="display: none;" type="button" class="btn btn-success btn-circle" onclick="EditarHorario('{$value['CDGCO']}', '{$value['NOMBRE']}' , '{$value['HORA_CIERRE']}');"><i class="fa fa-edit"></i></button>
                     </td>
                </tr>
html;
        }

        View::set('tabla', $tabla);
        View::set('usuario', $this->__usuario);
        View::set('opciones_suc', $opciones_suc);
        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("dias_festivos_caja_sucursal");
    }

    public function CorteEjecutivo()
    {
        $extraHeader = <<<html
        <title>Corte de Pagos App</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;


        $extraFooter = <<<html
      <script>
      
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
            
        });
       
        function enviar_add_horario(){	
             sucursal = document.getElementById("sucursal").value; 
             
            if(sucursal == '')
                {
                    
                      swal("Atención", "Ingrese un monto mayor a $0", "warning");
                      document.getElementById("monto").focus();
                        
                }
            else
                {
                    
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/HorariosAdd/',
                    data: $('#Add_AHC').serialize(),
                    success: function(respuesta) {
                         if(respuesta=='1'){
                      
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                         swal(respuesta, {
                                      icon: "error",
                                    });
                         //location.reload();
                         
                        }
                    }
                    });
                }
    }
    
        function enviar_update_horario(){	
          
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/HorariosUpdate/',
                    data: $('#Update_AHC').serialize(),
                    success: function(respuesta) {
                         if(respuesta=='1'){
                      
                         swal("Registro actualizado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                         swal(respuesta, {
                                      icon: "error",
                                    });
                         //location.reload();
                         
                        }
                    }
                    });
    }
    
        function editar_pago(id, comentario, tipo, monto, nuevo_monto, incidencia)
        {
                 let USDollar = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            });

                 if(incidencia == 1)
                     {
                         document.getElementById("nuevo_monto").value = nuevo_monto;
                     }
                 else 
                     {
                         document.getElementById("nuevo_monto").value = monto;
                     }
                document.getElementById("monto_detalle").value = USDollar.format(monto);
                document.getElementById("comentario_detalle").value = comentario;
                document.getElementById("id_registro").value = id;
        
                 select = document.querySelector('#tipo_pago_detalle');
                 select.value = tipo;
        
                $('#modal_agregar_horario').modal('show');
        }
        
        function enviar_add_edit_app(){	
             nuevo_monto = document.getElementById("nuevo_monto").value; 
             
            if(nuevo_monto == '')
                {
                    if(nuevo_monto == 0)
                        {
                             swal("Atención", "Ingrese un monto mayor a $0", "warning");
                             document.getElementById("monto").focus();
                             
                        }
                }
            else
                {
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosEditApp/',
                    data: $('#Add_Edit_Pago').serialize(),
                    success: function(respuesta) {
                         if(respuesta=='1'){
                      
                        document.getElementById("nuevo_monto").value = "";
                        
                         swal("Registro editado exitosamente", {
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
        
        function check_pagos(id)
        {
          
            var checkbox = document.getElementById(id);
            var isChecked = document.getElementById(id).checked;
            if(isChecked == false)
                {
                    var returnVal = confirm("¿Estas seguro de que deseas desactivar esta casilla?");
                    if(returnVal == true)
                    {
                        estatus = 0;
                           //////////////////////////
                           $.ajax({
                            type: 'POST',
                            url: '/Pagos/ValidaCorrectoPago/',
                            data: 'estatus='+estatus+'&id_check='+id,
                            success: function(respuesta) {
                                 if(respuesta=='1'){
                              
                                 swal("Registro desactivado exitosamente", {
                                              icon: "success",
                                            });
                                location.reload();
                                return false;
                                }
                                else {
                                 swal(respuesta, {
                                              icon: "error",
                                            });
                                 location.reload();
                                }
                            }
                            });
                           //////////////////////////
                        return false;
                    }
                    else 
                    {
                       alert("No paso nada");
                       return false;
                    }
                }
                else 
                {
                   var returnVal = confirm("Estas seguro de Calcular?");
                   if(returnVal == true)
                       {
                           estatus = 1;
                           //////////////////////////
                           $.ajax({
                            type: 'POST',
                            url: '/Pagos/ValidaCorrectoPago/',
                            data: 'estatus='+estatus+'&id_check='+id,
                            success: function(respuesta) {
                                 if(respuesta=='1'){
                              
                                 swal("Registro actualizado exitosamente", {
                                              icon: "success",
                                            });
                                location.reload();
                                //return false;
                                }
                                else {
                                 swal(respuesta, {
                                              icon: "error",
                                            });
                                 //location.reload();
                                }
                            }
                            });
                           //////////////////////////
                           
                       }
                   else 
                       {
                           document.getElementById(id).checked = false;
                           return false;
                       }
                    }
            
        }
        
         function boton_resumen_pago()
        {
             validados = document.getElementById("validados_r");
             contenido_validados = validados.innerHTML;
             
             total = document.getElementById("total_r");
             contenido_total = total.innerHTML;
             
             operacion = parseInt(contenido_total) - parseInt(contenido_validados);
            
           
           
           if(contenido_validados == contenido_total)
               {
                   $('#modal_resumen').modal({backdrop: 'static', keyboard: false}, 'show');
               }
           else 
               {
                    swal("Atención", "Debe validar todos los pagos (tiene " + operacion+ " registros pendientes)", "warning");
               }
        }
        
         function boton_ticket(barcode)
        {
             $('#all').attr('action', '/Pagos/Ticket/'+barcode+'/');
             $('#all').attr('target', '_blank');
             $("#all").submit();
        }
        
         function boton_terminar(barcode)
        {
            var resume_table = document.getElementById("terminar_resumen");
            total = document.getElementById("validados_r_total");
            sum_contador = 1;
            contenido = parseInt(total.innerHTML) + 1;
            
            for (var i = 1, row; row = resume_table.rows[i]; i++) {
                
              sum_contador++;
              
                    col = row.cells[0];
                    pk = col.innerText;
                  
                $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosAddApp/',
                    data: 'cortecaja_pk='+pk+'&barcode='+barcode,
                    success: function(respuesta) {
                                swal({
                                    title: "Procesando Pagos",
                                    text: "Espere por favor...",
                                    timer: 100,
                                    onOpen: function() {
                                        swal.showLoading()
                                    }
                                })
                        }
                    });
                
                 if(contenido == sum_contador)
                     {
                         //alert("Son iguales");
                         location.reload();
                     }
                 else
                     {
                         //alert("No son iguales");
                     }
               
            }
          
        }
       
      
      </script>
html;

        $tabla = '';

        $ejecutivo = $_GET['MTYQW'];
        $fecha = $_GET['FEC'];
        $suc = $_GET['SUC'];
        $barcode = $_GET['BCODE'];

        if ($ejecutivo == '' || $fecha == '' || $suc == '' || $barcode == '') {
            $Administracion = PagosDao::ConsultarPagosApp();

            foreach ($Administracion as $key => $value) {
                $pago = number_format($value['TOTAL_PAGOS'], 2);
                $multa = number_format($value['TOTAL_MULTA'], 2);
                $refinanciamiento = number_format($value['TOTAL_REFINANCIAMIENTO'], 2);
                $descuento = number_format($value['TOTAL_DESCUENTO'], 2);
                $garantia = number_format($value['GARANTIA'], 2);
                $monto_total = number_format($value['MONTO_TOTAL'], 2);

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['BARRAS']}</td>
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_PAGOS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;"><strong>{$value['FECHA_D']}</strong></td>
                    <td style="padding: 0px !important;">$ {$pago}</td>
                    <td style="padding: 0px !important;">$ {$multa}</td>
                    <td style="padding: 0px !important;">$ {$refinanciamiento}</td>
                    <td style="padding: 0px !important;">$ {$descuento}</td>
                    <td style="padding: 0px !important;">$ {$garantia}</td>
                    <td style="padding: 0px !important;">$ {$monto_total}</td>
                    <td style="padding: 0px !important;">
                        <a href="/Pagos/CorteEjecutivo/?MTYQW={$value['CDGOCPE']}&FEC={$value['FECHA']}&BCODE={$value['BARRAS']}&SUC={$value['COD_SUC']}" type="button" class="btn btn-success btn-circle"><i class="fa fa-edit"></i> Procesar Pagos</a>
                     </td>
                </tr>
html;
            }
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::render("view_pagos_app_ejecutivos");
        } else {
            $Administracion = PagosDao::ConsultarPagosAppDetalle($ejecutivo, $fecha, $suc);
            $validar = $Administracion[0];

            if ($validar == NULL) {
                //////////////////////aqui
                $Administracion = PagosDao::ConsultarPagosAppDetalleImprimir($ejecutivo, $fecha, $suc);
                $Ejec = $Administracion[0][0];
                foreach ($Administracion[0] as $key => $value) {

                    if ($value['TIPO'] == 'P') {
                        $tipo_pago = 'PAGO';
                    } else if ($value['TIPO'] == 'M') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'G') {
                        $tipo_pago = 'GARANTIA';
                    } else if ($value['TIPO'] == 'D') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'R') {
                        $tipo_pago = 'REFINANCIAMIENTO';
                    }


                    $monto = number_format($value['MONTO'], 2);
                    $nuevo_monto = number_format($value['NUEVO_MONTO'], 2);
                    $id_check = $value['CORTECAJA_PAGOSDIA_PK'];
                    if ($value['TIPO'] == 'P' || $value['TIPO'] == 'M') {
                        $color_celda = "";
                        $boton_visible = "";
                        $check_visible = '';
                    } else {
                        $color_celda = "background-color: #FFC733 !important;";
                        $boton_visible = "disabled";
                        $check_visible = 'display:none;';
                    }

                    if ($value['ESTATUS_CAJA'] == 1) {
                        $selected = 'checked';
                    } else {
                        $selected = '';
                    }

                    if ($value['INCIDENCIA'] == 1) {
                        $campo = '<div><del>$' . $monto . '</del></div> <div style="font-size: 20px!important;"> $' . $nuevo_monto . '</div>';
                    } else {
                        $campo = '<div style="font-size: 20px!important;">$' . $monto . '</div>';
                    }

                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 10px !important; $color_celda">{$value['CORTECAJA_PAGOSDIA_PK']}</td>
                    <td style="padding: 10px !important; text-align: left; $color_celda">
                        <div>#CRÉDITO: <b>{$value['CDGNS']}</b></div>
                        <div>NOMBRE: <b>{$value['NOMBRE']}</b></div>
                        
                        <div>CICLO: <b>{$value['CICLO']}</b></div>
                        <div>FECHA DE PAGO: <b>{$value['FECHA']}</b></div>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$tipo_pago}</td>
                    <td style="padding: 10px !important; $color_celda">
                        {$campo}
                         <input style="{$check_visible}" class="form-check-input" type="checkbox" value="" id="$id_check" name="$id_check" onclick="check_pagos('$id_check');" $selected disabled>
                          <label style="{$check_visible}" class="form-check-label" for="flexCheckDefault">
                            Validado
                         </label>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$value['COMENTARIO_INCIDENCIA']}</td>
                    <td style="padding: 10px !important; $color_celda">{$value['FIDENTIFICAPP']}</td>
                    
                     <td style="padding-top: 30px !important;">
                        <b>Pago Procesado</b>
                     </td>
                </tr>
html;
                }


                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('ejecutivo', $ejecutivo);
                View::set('DetalleGlobal', $Administracion[1]);
                View::set('Ejecutivo', $Ejec['EJECUTIVO']);
                View::set('barcode', $barcode);
                View::render("view_pagos_app_detalle_imprimir");
            } else {
                $AdministracionResumen = PagosDao::ConsultarPagosAppResumen($ejecutivo, $fecha, $suc);
                $Ejec = $Administracion[0][0];
                foreach ($Administracion[0] as $key => $value) {

                    if ($value['TIPO'] == 'P') {
                        $tipo_pago = 'PAGO';
                    } else if ($value['TIPO'] == 'M') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'G') {
                        $tipo_pago = 'GARANTIA';
                    } else if ($value['TIPO'] == 'D') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'R') {
                        $tipo_pago = 'REFINANCIAMIENTO';
                    }


                    $monto = number_format($value['MONTO'], 2);
                    $nuevo_monto = number_format($value['NUEVO_MONTO'], 2);
                    $id_check = $value['CORTECAJA_PAGOSDIA_PK'];
                    if ($value['TIPO'] == 'P' || $value['TIPO'] == 'M') {
                        $color_celda = "";
                        $boton_visible = "";
                        $check_visible = '';
                    } else {
                        $color_celda = "background-color: #FFC733 !important;";
                        $boton_visible = "disabled";
                        $check_visible = 'display:none;';
                    }

                    if ($value['ESTATUS_CAJA'] == 1) {
                        $selected = 'checked';
                    } else {
                        $selected = '';
                    }

                    if ($value['INCIDENCIA'] == 1) {
                        $campo = '<div><del>$' . $monto . '</del></div> <div style="font-size: 20px!important;"> $' . $nuevo_monto . '</div>';
                    } else {
                        $campo = '<div style="font-size: 20px!important;">$' . $monto . '</div>';
                    }
                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 10px !important; $color_celda">{$value['CORTECAJA_PAGOSDIA_PK']}</td>
                    <td style="padding: 10px !important; text-align: left; $color_celda">
                        <div>#CRÉDITO: <b>{$value['CDGNS']}</b></div>   
                        <div>NOMBRE: <b>{$value['NOMBRE']}</b></div>
                        <div>CICLO: <b>{$value['CICLO']}</b></div>
                        <div>FECHA DE PAGO: <b>{$value['FECHA']}</b></div>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$tipo_pago}</td>
                    <td style="padding: 10px !important; $color_celda">
                        {$campo}
                         <input style="{$check_visible}" class="form-check-input" type="checkbox" value="" id="$id_check" name="$id_check" onclick="check_pagos('$id_check');" $selected>
                          <label style="{$check_visible}" class="form-check-label" for="flexCheckDefault">
                            Validado
                         </label>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$value['COMENTARIO_INCIDENCIA']}</td>
                    <td style="padding: 10px !important; $color_celda">{$value['FIDENTIFICAPP']}</td>
                    
                     <td style="padding-top: 30px !important;">
                        <button type="button" class="btn btn-success btn-circle" onclick="editar_pago('{$value['CORTECAJA_PAGOSDIA_PK']}', '{$value['COMENTARIO_INCIDENCIA']}', '{$value['TIPO']}', '{$value['MONTO']}', '{$value['NUEVO_MONTO']}', '{$value['INCIDENCIA']}');"><i class="fa fa-edit"></i> Editar Pago</button>
                     </td>
                </tr>
html;
                }

                $AdministracionOne = PagosDao::ConsultarCierreCajaCajera($this->__usuario);
                $situacion_credito = $AdministracionOne[0]['SITUACION_NOMBRE'];
                $fechaActual = date("Y-m-d");
                $horaActual = date("H:i:s");
                $dia = date("N");

                $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
                if ($hora_cierre == '') {
                    $hora_cierre = '10:00:00';
                } else {
                    $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
                }

                if ($horaActual <= $hora_cierre) {
                    if ($dia == 1) {
                        $date_past = strtotime('-3 days', strtotime($fechaActual));
                        $date_past = date('Y-m-d', $date_past);
                    } else {
                        $date_past = strtotime('-1 days', strtotime($fechaActual));
                        $date_past = date('Y-m-d', $date_past);
                    }

                    $inicio_f = $date_past;
                    $fin_f = $fechaActual;
                } else {
                    $inicio_f = $fechaActual;
                    $fin_f = $fechaActual;
                }

                foreach ($AdministracionResumen[0] as $key => $value_resumen) {

                    $ejecutivo = $value_resumen['EJECUTIVO'];
                    $cdgpe_ejecutivo = $value_resumen['CDGPE'];


                    if ($value_resumen['TIPO'] == 'P') {
                        $tipo_pago = 'PAGO';
                    } else if ($value_resumen['TIPO'] == 'M') {
                        $tipo_pago = 'MULTA';
                    } else if ($value_resumen['TIPO'] == 'G') {
                        $tipo_pago = 'GARANTIA';
                    } else if ($value_resumen['TIPO'] == 'D') {
                        $tipo_pago = 'MULTA';
                    } else if ($value_resumen['TIPO'] == 'R') {
                        $tipo_pago = 'REFINANCIAMIENTO';
                    }

                    if ($value_resumen['INCIDENCIA'] == 1) {
                        $campo_resumen = '$' . number_format($value_resumen['NUEVO_MONTO'], 2);
                    } else {
                        $campo_resumen = '$' . number_format($value_resumen['MONTO'], 2);
                    }

                    $tabla_resumen .= <<<html
                <tr>
                    <td style="display: none;" id="pk" style="padding: 10px !important; background: #9d9d9d">{$value_resumen['CORTECAJA_PAGOSDIA_PK']}</td>
                    <td id="codigo" style="text-align: left; padding: 3px !important;">
                        <b> {$value_resumen['CDGNS']}</b>
                    </td>
                    <td id="nombre" style="text-align: left; padding: 3px !important;">
                        {$value_resumen['NOMBRE']}
                    </td>
                    <td id="ciclo" style="padding: 3px !important;"><b>{$value_resumen['CICLO']}</b></td>
                    <td id="tipo" style="padding: 3px !important;">{$tipo_pago}</td>
                     
                    <td id="monto" style="background: #173b00; color: #fdfdfd; padding: 3px !important; width:94px !important;"><b>{$campo_resumen}</b></td>
                   
                </tr>
html;
                }
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('ejecutivo', $ejecutivo);
                View::set('cdgpe_ejecutivo', $cdgpe_ejecutivo);
                View::set('tabla_resumen', $tabla_resumen);
                View::set('DetalleGlobal', $Administracion[1]);
                View::set('Ejecutivo', $Ejec['EJECUTIVO']);
                View::set('inicio_f', $inicio_f);
                View::set('fin_f', $fin_f);
                View::set('barcode', $barcode);
                View::set('fechaActual', $fechaActual);
                View::render("view_pagos_app_detalle");
            }
        }
    }

    public function Ticket($barcode)
    {
        $mpdf = new \mPDF('c');
        $mpdf->defaultPageNumStyle = 'I';
        $mpdf->h2toc = array('H5' => 0, 'H6' => 1);

        $style = <<<html
      <style>
     
       .titulo{
          width:100%;
          margin-top: 30px;
          color: #b92020;
          margin-left:auto;
          margin-right:auto;
        }
        
        body {
          padding: 50px;
        }
        
        * {
          box-sizing: border-box;
        }
        
        .receipt-main {
          display: inline-block;
          width: 100%;
          padding: 15px;
          font-size: 12px;
          border: 1px solid #000;
        }
        
        .receipt-title {
          text-align: center;
          text-transform: uppercase;
          font-size: 20px;
          font-weight: 600;
          margin: 0;
        }
          
        .receipt-label {
          font-weight: 600;
        }
        
        .text-large {
          font-size: 16px;
        }
        
        .receipt-section {
          margin-top: 10px;
        }
        
        .receipt-footer {
          text-align: center;
          background: #ff0000;
        }
        
        .receipt-signature {
          height: 80px;
          margin: 50px 0;
          padding: 0 50px;
          background: #fff;
          
          .receipt-line {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
          }
          
          p {
            text-align: justify;
            margin: 0;
            font-size: 17px;
          }
        }
      </style>
html;
        $complemento = PagosDao::getByIdReporte($barcode);

        $cant_total = "$" . number_format($complemento[0]['TOTAL'], 2);

        $tabla = <<<html

        <div class="receipt-main">
         <table class="table">
             <tr>
                 <th style="width: 600px;" class="text-right">
                    <p class="receipt-title"><b>Recibo de Pago</b></p>
                 </th>
                 <th style="width: 10px;" class="text-right">
                    <img src="img/logo.png" alt="Esta es una descripcion alternativa de la imagen para cuando no se pueda mostrar" width="60" height="50" align="left"/>
                 </th>
             </tr>
        </table>
         
          <div class="receipt-section pull-left">
            <span class="receipt-label text-large">#FOLIO:</span>
            <span class="text-large"><b>{$barcode}</b></span>
          </div>
          
           <div class="receipt-section pull-left">
            <span class="receipt-label text-large">FECHA DE COBRO:</span>
            <span class="text-large">{$complemento[0]['FECHA_GRUPO']}</span>
          </div>
          
          
          <div class="clearfix"></div>
          
          <div class="receipt-section">
        
            <p>Recibí del ejecutiv(a) <b>{$complemento[0]['EJECUTIVO']}</b>, la cantidad de <b>{$cant_total} M.N</b>, 
            por concepto de recoleccion de <b>pagos varios</b> <u>({$complemento[0]['TOTAL_VALIDADOS']} pagos)</u> de Financiera Más con Menos, sucursal <u>{$complemento[0]['NOMBRE_SUC']}</u>, con aplicación a la fecha: <b>31/10/2023</b>.
             </p>
             <p>Así mismo el ejecutivo firma de conformidad, la entrega a detalle de los siguientes pagos, en donde se especifica número de crédito, ciclo, nombre completo del cliente, tipo de pago y monto:</p>
          </div>
          
          <hr>
          
        
       <div class="table-responsive-sm">
          <table class="table">
              <thead>
                 <tr>
                     <th># Crédito</th>
                     <th>Nombre del Cliente</th>
                     <th>Ciclo</th>
                     <th width="19%" class="text-right">Tipo</th>
                     <th class="text-right">Monto</th>
                 </tr>
              </thead>
                  <tbody>
                     
html;


        foreach ($complemento[1] as $key => $value) {
            if ($value['TIPO'] == 'P') {
                $tipo_pago = 'PAGO';
                if ($value['INCIDENCIA'] == '1') {
                    $procede = "$" . number_format($value['NUEVO_MONTO'], 2);
                } else {
                    $procede = "$" . number_format($value['MONTO'], 2);
                }
            } else if ($value['TIPO'] == 'M') {
                $tipo_pago = 'MULTA';
                if ($value['INCIDENCIA'] == '1') {
                    $procede = "$" . number_format($value['NUEVO_MONTO'], 2);
                } else {
                    $procede = "$" . number_format($value['MONTO'], 2);
                }
            } else if ($value['TIPO'] == 'G') {
                $tipo_pago = 'GARANTIA';
                $procede = '$00.00';
            } else if ($value['TIPO'] == 'D') {
                $tipo_pago = 'MULTA';
                $procede = '$00.00';
            } else if ($value['TIPO'] == 'R') {
                $tipo_pago = 'REFINANCIAMIENTO';
                $procede = '$00.00';
            }

            $tabla .= <<<html
                   
            <tr style="background-color:#B8B8B8;">
            <td style="height:auto; width: 80px;background-color:#E4E4E4; text-align: center;">{$value['CDGNS']}</td>
            <td style="height:auto; width: 300px;background-color:#E4E4E4; ">{$value['NOMBRE']}</td>
            <td style="height:auto; width: 60px;background-color:#E4E4E4; text-align: center;">{$value['CICLO']}</td>
            <td style="height:auto; width: 80px;background-color:#E4E4E4; text-align: center;">{$tipo_pago}</td>
            <td class="center" style="height:auto; width: 100px;background-color:#E4E4E4; text-align: center;">{$procede}</td>
            </tr>
html;
        }

        $tabla .= <<<html
      </table>
      </div>
      <hr>
      <br>
      <br>
      <br>
      
        <table class="table">
             <tr>
                 <th style="width: 370px;" class="text-right">
                 <b>Ejecutiv(a)</b>
                 <br>
                 <br>
                 <br>
                 _____________________
                 <br>
                 <b>{$complemento[0]['EJECUTIVO']}</b>
                 <br>
                 <b>Firma de conformidad</b>
                 
                 
                 </th>
                 <th style="width: 270px;" class="text-right">
                 <b>Cajer(a)</b>
                 <br>
                 <br>
                 <br>
                 _____________________
                 <br>
                 <b>$this->__nombre</b>
                 <br>
                 <b>Firma de conformidad</b>
                 
                 
                 </th>
             </tr>
        </table>
      
</div>

html;

        $fechaActual = date('Y-m-d H:i:s');


        $mpdf->WriteHTML($style, 1);
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:opensans;">Este recibo de pago se genero el día ' . $fechaActual . '<br>{PAGENO}</div>');
        print_r($mpdf->Output());
        exit;
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
        if (
            $this->__perfil == 'ADMIN'
            || $this->__perfil == 'ACALL'
            || $this->__usuario == 'PMAB'
            || $this->__usuario == 'PAES'
            || $this->__usuario == 'COCS'
            || $this->__usuario == 'LGFR'
        ) {
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
                if ($value['FIDENTIFICAPP'] ==  NULL) {
                    $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                    $mensaje = 'InfoAdmin();';
                } else {
                    $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                    $mensaje = 'InfoPhone();';
                }

                $monto = number_format($value['MONTO'], 2);
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
            if ($Consulta[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('getSucursales', $getSucursales);
                View::set('fechaActual', $fechaActual);
                View::render("pagos_consulta_busqueda_message");
            } else {
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

    public function PagosAdd()
    {
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

    public function HorariosAdd()
    {
        $pagos = new \stdClass();
        $fecha_registro = MasterDom::getDataAll('fecha_registro');
        $pagos->_fecha_registro = $fecha_registro;

        $sucursal = MasterDom::getDataAll('sucursal');
        $pagos->_sucursal = $sucursal;

        $hora = MasterDom::getDataAll('hora');
        $pagos->_hora = $hora;

        $id = PagosDao::insertHorarios($pagos);
        return $id;
    }

    public function HorariosUpdate()
    {
        $horario = new \stdClass();

        $sucursal = MasterDom::getDataAll('sucursal_e');
        $horario->_sucursal = $sucursal;

        $hora = MasterDom::getDataAll('hora_e');
        $horario->_hora = $hora;

        $id = PagosDao::updateHorarios($horario);
        return $id;
    }

    public function ValidaCorrectoPago()
    {
        $update = new \stdClass();

        $estatus = MasterDom::getDataAll('estatus');
        $update->_estatus = $estatus;

        $id_check = MasterDom::getDataAll('id_check');
        $update->_id_check = $id_check;

        $id = PagosDao::updateEstatusValidaPago($update);
        return $id;
    }

    public function PagosEdit()
    {
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

    public function Delete()
    {

        $cdgns = $_POST['cdgns'];
        $fecha = $_POST['fecha'];
        $usuario = $_POST['usuario'];
        $secuencia = $_POST['secuencia'];

        $id = PagosDao::DeleteProcedure($cdgns, $fecha, $usuario, $secuencia);
        return $id;
    }
    public function PagosEditApp()
    {

        $edit = new \stdClass();

        $edit->_id_registro = $_POST['id_registro'];
        $edit->_fecha_registro = $_POST['fecha_registro'];
        $edit->_tipo_pago_detalle = $_POST['tipo_pago_detalle'];
        $edit->_nuevo_monto = $_POST['nuevo_monto'];
        $edit->_comentario_detalle = $_POST['comentario_detalle'];
        $edit->_tipo_pago = $_POST['tipo_pago_detalle'];


        $id = PagosDao::updatePagoApp($edit);
        //return $id;

    }

    public function PagosAddApp()
    {

        $add_app = $_POST['cortecaja_pk'];
        $barcode = $_POST['barcode'];

        $id = PagosDao::AddPagoApp($add_app, $barcode);
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
         
         function BotonPago(estatus, ciclo)
         {
            if(estatus == 'LIQUIDADO')
                {
                    let ciclo_anterior = '';
                    if(ciclo >=10)
                    {
                         ciclo_anterior = (ciclo - 1);   
                    }
                    else {
                        ciclo_anterior = '0'+(ciclo - 1);
                    }
                    
                    
                    var txt_ciclo = document.getElementById("ciclo");
                    var option = document.createElement("option");
                    var option1 = document.createElement("option");
                    
                    
                    select = $("#tipo");
                    select.empty();
                    select.append($("<option>", {
                        value: 'M',
                        text: 'MULTA GESTORES'
                      }));
                     
                    if(ciclo != '01')
                    {
                        $('#ciclo').empty();
                        
                        option.text = ciclo;
                        option.value = ciclo;
                        txt_ciclo.add(option); 
                        
                        
                        option1.text = ciclo_anterior;
                        option1.value = ciclo_anterior;
                        txt_ciclo.add(option1);    
                        
                        
                    }
                }
         }
         
         function MultaCiclo(op, ciclo)
         {
              var value = op.value;  
              var txt_ciclo = document.getElementById("ciclo");
              var option = document.createElement("option");
               let ciclo_anterior = '';
               
               if(ciclo >=10)
               {
                   ciclo_anterior = (ciclo - 1);   
               }
               else {
                    ciclo_anterior = '0'+(ciclo - 1);
               }
              
              if(value == 'M')
              {
                    if(ciclo != '01')
                    {
                        option.text = ciclo_anterior;
                        option.value = ciclo_anterior;
                        txt_ciclo.add(option);    
                    }
                    
              }
              else 
              {
                   $('#ciclo').empty();
                   option.text = ciclo;
                   option.value = ciclo;
                   txt_ciclo.add(option);  
                  
              }
         }
    
      </script>
html;


        $credito = $_GET['Credito'];
        $tabla = '';

        $fechaActual = date("Y-m-d");
        $horaActual = date("H:i:s");


        $dia = date("N");


        //$dia = 2;

        $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);
        $situacion_credito = $AdministracionOne[0]['SITUACION_NOMBRE'];

        $fue_dia_festivo = $AdministracionOne[2]['TOT'];

        $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
        if ($hora_cierre == '') {
            $hora_cierre = '10:00:00';
        } else {
            $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
        }

        if ($horaActual <= $hora_cierre) {
            if ($dia == 1) {
                if($fue_dia_festivo == 4)
                {
                    $date_past = strtotime('-6 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
                else if($fue_dia_festivo == 3)
                {
                    $date_past = strtotime('-5 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
                else if($fue_dia_festivo == 2)
                {
                    $date_past = strtotime('-4 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
                else if($fue_dia_festivo == 1)
                {
                    $date_past = strtotime('-3 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
                else
                {
                    $date_past = strtotime('-3 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }

            } else {
                if ($fue_dia_festivo == 1 && $dia == 2) {
                    $date_past = strtotime('-4 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else if ($fue_dia_festivo == 1 && $dia != 2) {
                    $date_past = strtotime('-2 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else {
                    $date_past = strtotime('-1 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
            }

            $inicio_f = $date_past;
            $fin_f = $fechaActual;

            //$fin_f = $date_past;
        } else {
            $inicio_f = $fechaActual;
            $fin_f = $fechaActual;
        }


        $status = PagosDao::ListaEjecutivosAdmin($credito);
        foreach ($status[0] as $key => $val2) {
            if ($status[1] == $val2['ID_EJECUTIVO']) {
                $select = 'selected';
            } else {
                $select = '';
            }

            $getStatus .= <<<html
                <option $select value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }
        if ($credito != '') {

            if ($AdministracionOne[0]['NO_CREDITO'] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::set('usuario', $this->__usuario);
                View::render("pagos_registro_busqueda_message");
            } else {
                $Administracion = PagosDao::ConsultarPagosAdministracion($credito, $hora_cierre);
                foreach ($Administracion as $key => $value) {

                    if ($value['FIDENTIFICAPP'] ==  NULL) {
                        $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                        $mensaje = 'InfoAdmin();';
                    } else {
                        $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                        $mensaje = 'InfoPhone();';
                    }

                    if ($value['DESIGNATION'] == 'SI') {
                        /////
                        /// /
                        ///
                        ///
                        /// aqui poner que si los pagos son de app no se pueden modificar, consulte con operaciones
                        ///
                        ///
                        ///
                        $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
html;
                    } else {



                        if($fue_dia_festivo == 4)
                        {
                            $date_past_b = strtotime('-6 days', strtotime($fechaActual));
                            $date_past_b = date('Y-m-d', $date_past_b);
                        }
                        else if($fue_dia_festivo == 3)
                        {
                            $date_past_b = strtotime('-5 days', strtotime($fechaActual));
                            $date_past_b = date('Y-m-d', $date_past_b);
                        }
                        else if($fue_dia_festivo == 2)
                        {
                            $date_past_b = strtotime('-4 days', strtotime($fechaActual));
                            $date_past_b = date('Y-m-d', $date_past_b);
                        }
                        else if($fue_dia_festivo == 1)
                        {
                            $date_past_b = strtotime('-3 days', strtotime($fechaActual));
                            $date_past_b = date('Y-m-d', $date_past_b);
                        }
                        else
                        {
                            $date_past_b = strtotime('-3 days', strtotime($fechaActual));
                            $date_past_b = date('Y-m-d', $date_past_b);
                        }





                        $fecha_base = strtotime($value['FECHA']);
                        $fecha_base = date('Y-m-d', $fecha_base);

                        $inicio_b = $date_past_b;

                        ///////////////////////////////////////////////////////////////////////////////////////////////////
                        if (($inicio_b == $fecha_base) ||  $fecha_base >= $date_past_b && $AdministracionOne[2]['FECHA_CAPTURA'] <= $AdministracionOne[2]['FECHA_CAPTURA']) // aqui poner el dia en que se estaran capturando
                        {

                            if ($horaActual <= $hora_cierre) {
                                $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                     
html;
                            } else {
                                $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
                      
html;
                            }
                        } else {
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
                View::set('cdgco', $this->__cdgco);
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

    public function PagosConsultaUsuarios()
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

        $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);

        $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
        if ($hora_cierre == '') {
            $hora_cierre = '10:00:00';
        } else {
            $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
        }

        if ($horaActual <= $hora_cierre) {
            if ($dia == 1) {
                $date_past = strtotime('-3 days', strtotime($fechaActual));
                $date_past = date('Y-m-d', $date_past);
            } else {
                $date_past = strtotime('-1 days', strtotime($fechaActual));
                $date_past = date('Y-m-d', $date_past);
            }

            $inicio_f = $date_past;
            $fin_f = $fechaActual;
        } else {
            $inicio_f = $fechaActual;
            $fin_f = $fechaActual;
        }


        $status = PagosDao::ListaEjecutivosAdmin($credito);
        foreach ($status[0] as $key => $val2) {
            if ($status[1] == $val2['ID_EJECUTIVO']) {
                $select = 'selected';
            } else {
                $select = '';
            }

            $getStatus .= <<<html
                <option $select value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }
        if ($credito != '') {

            if ($AdministracionOne[0]['NO_CREDITO'] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::set('usuario', $this->__usuario);
                View::render("pagos_consulta_p_busqueda_message");
            } else {
                $Administracion = PagosDao::ConsultarPagosAdministracion($credito, $hora_cierre);
                foreach ($Administracion as $key => $value) {

                    if ($value['FIDENTIFICAPP'] ==  NULL) {
                        $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                        $mensaje = 'InfoAdmin();';
                    } else {
                        $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                        $mensaje = 'InfoPhone();';
                    }

                    $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
html;

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
                View::render("pagos_consulta_p_busqueda");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_consulta_p_all");
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
                           
                        }
                    }
                    });
                }
    }
        function FunprecesarPagos() {
           alert("procesando...");
           ///////
        
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
                if ($value['TIPO'] == 'P') {
                    $tipo_pago = 'PAGO';
                }
                if ($value['TIPO_PAGO'] == 'G') {
                    $tipo_pago = 'GARANTÍA';
                }
                if ($value['TIPO_PAGO'] == 'M') {
                }
                if ($value['TIPO_PAGO'] == 'A') {
                }
                if ($value['TIPO_PAGO'] == 'W') {
                }
                if ($value['ESTATUS_CAJA'] == '0') {
                    if ($value['INCIDENCIA'] == 1) {
                        $estatus = 'PENDIENTE, CON MODIFICACION';
                    } else {
                        $estatus = 'PENDIENTE';
                    }
                }
                //////////////////////////////////////

                if ($value['INCIDENCIA'] == 1) {
                    $incidencia = '<br><span class="count_top" style="font-size: 20px; color: gold"><i class="fa fa-warning"></i></span> <b>Incidencia:</b>' . $value['COMENTARIO_INCIDENCIA'];
                    $monto = '<span class="count_top" style="font-size: 16px; color: #017911">Monto a recibir: $' . number_format($value['NUEVO_MONTO']) . '</span><br>
                              <span class="count_top" style="font-size: 15px; color: #ff0066">Monto registrado: $' . number_format($value['MONTO']) . '</span>';
                    $botones = "";
                } else {
                    $incidencia = '';
                    $monto = '$ ' . number_format($value['MONTO']);

                    $botones =  <<<html
                    
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$tipo_pago}', '{$value['MONTO']}','{$estatus}', '{$value['EJECUTIVO']}', '{$value['SITUACION_NOMBRE']}');"><i class="fa fa-edit"></i></button>
                
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
            if ($CorteCaja[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            } else {
                View::set('tabla', $tabla);
                View::set('CorteCajaById', $CorteCajaById);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            }
        } else {

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
            if ($CorteCaja[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_all"); ////CAmbiar a una en donde diga que no hay registros
            } else {
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

        if ($Inicial == '' && $Final == '') {
            View::set('fechaActual', $fechaActual);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_layout_all");
        } else {
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
                if ($Layout[0] == '') {
                    View::set('header', $this->_contenedor->header($extraHeader));
                    View::set('footer', $this->_contenedor->footer($extraFooter));
                    View::set('fechaActual', $fechaActual);
                    View::render("pagos_layout_busqueda_message");
                } else {
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

    public function generarExcel()
    {

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

        $controlador = "Pagos";
        $columna = array('A', 'B', 'C', 'D');
        $nombreColumna = array('Fecha', 'Referencia', 'Monto', 'Moneda');
        $nombreCampo = array('FECHA', 'REFERENCIA', 'MONTO', 'MONEDA');



        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila += 1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        $Layoutt = PagosDao::GeneraLayoutContable($fecha_inicio, $fecha_fin);
        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
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
        header('Content-Disposition: attachment;filename="Layout ' . $controlador . '.xlsx"');
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

    public function generarExcelConsulta()
    {

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

        $controlador = "Pagos";
        $columna = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $nombreColumna = array('Sucursal', 'Codigo', 'Fecha', 'Cliente', 'Nombre', 'Ciclo', 'Monto', 'Tipo', 'Ejecutivo', 'Registro');
        $nombreCampo = array('NOMBRE_SUCURSAL', 'SECUENCIA', 'FECHA', 'CDGNS', 'NOMBRE', 'CICLO', 'MONTO', 'TIPO', 'EJECUTIVO', 'FREGISTRO');


        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila += 1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        $Layoutt = PagosDao::ConsultarPagosFechaSucursal($Sucursal, $fecha_inicio, $fecha_fin);
        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
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
        header('Content-Disposition: attachment;filename="Consulta Pagos Global ' . $controlador . '.xlsx"');
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
}
