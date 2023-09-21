<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \App\models\CallCenter AS CallCenterDao;

class CallCenter{

    private $_contenedor;

    function __construct(){
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function Pendientes()
    {
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;        $extraFooter = <<<html
      <script>
      
       function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        
        $('#doce_cl').on('change', function() {
          if(this.value == 'NO')
              {
                  swal("Atención", "Al finalizar la encuesta cancele la solicitud, no cumple con la política de seguridad de la pregunta #12", "warning");
              }
        });
      
        $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [6, 10, 20, 30, -1],
                    [6, 10, 20, 30, 'Todos'],
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
         
        function InfoDesactivaEncuesta()
        {
             swal("Atención", "Para continuar con la ENCUESTA del AVAL por favor, es nesesario completar la PRIMER LLAMADA del cliente. ", "warning");
        }
         
        function enviar_add_cl(){	
             fecha_trabajo = document.getElementById("fecha_cl").value; 
             ciclo = document.getElementById("ciclo_cl").value; 
             num_telefono = document.getElementById("movil_cl").value;  
             tipo_cl = document.getElementById("tipo_llamada_cl").value; 
             uno = document.getElementById("uno_cl").value; 
             dos = document.getElementById("dos_cl").value; 
             tres = document.getElementById("tres_cl").value; 
             cuatro = document.getElementById("cuatro_cl").value; 
             cinco = document.getElementById("cinco_cl").value; 
             seis = document.getElementById("seis_cl").value; 
             siete = document.getElementById("siete_cl").value; 
             ocho = document.getElementById("ocho_cl").value; 
             nueve = document.getElementById("nueve_cl").value; 
             diez = document.getElementById("diez_cl").value; 
             once = document.getElementById("once_cl").value; 
             doce = document.getElementById("doce_cl").value; 
             completo = $('input[name="completo"]:checked').val();
             llamada = document.getElementById("titulo");
             contenido = llamada.innerHTML;
             
             
             if(contenido == '2')
                 {
                     mensaje = "Está es la ultima llamada que podrá realizar al cliente y debera seleccionar un estatus final como: 'CANCELADA: NO LOCALIZADOS.', para terminar con la solicitud.";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "Solo podrá intentar contactar una vez más al cliente.";
                         }
                     
                 }
             
             
             
             if(completo == '0')
                 {
                     
                      if(tipo_cl == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }
                      else 
                          {
                                  swal({
                                  title: "¿Está segura de continuar con una llamada incompleta?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: true,
                                  dangerMode: true
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                            type: 'POST',
                                            url: '/CallCenter/PagosAddEncuestaCL/',
                                            data: $('#Add_cl').serialize()+'&contenido='+contenido,
                                            success: function(respuesta) {
                                                 if(respuesta=='1'){
                                                 swal("Registro guardado exitosamente", {
                                                              icon: "success",
                                                            });
                                                 location.reload();
                                                }
                                                else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                  
                                                }
                                            }
                                            });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "success",});
                                  }
                                });
                         }
                 }
             else 
                 {
                      if(tipo_cl == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }else if(uno  == '') {
                             swal("Seleccione una opción para la pregunta #1", {icon: "warning",});
                        }else if(dos  == '') {
                             swal("Seleccione una opción para la pregunta #2", {icon: "warning",});
                        }else if(tres  == '') {
                             swal("Seleccione una opción para la pregunta #3", {icon: "warning",});
                        }else if(cuatro  == '') {
                             swal("Seleccione una opción para la pregunta #4", {icon: "warning",});
                        }else if(cinco  == '') {
                             swal("Seleccione una opción para la pregunta #5", {icon: "warning",});
                        }else if(seis  == '') {
                             swal("Seleccione una opción para la pregunta #6", {icon: "warning",});
                        }else if(siete  == '') {
                             swal("Seleccione una opción para la pregunta #7", {icon: "warning",});
                        }else if(ocho  == '') {
                             swal("Seleccione una opción para la pregunta #8", {icon: "warning",});
                        }else if(nueve  == '') {
                             swal("Seleccione una opción para la pregunta #9", {icon: "warning",});
                        }else if(diez  == '') {
                             swal("Seleccione una opción para la pregunta #11", {icon: "warning",});
                        }else if(once  == '') {
                             swal("Seleccione una opción para la pregunta #11", {icon: "warning",});
                        }else if(doce  == '') {
                             swal("Seleccione una opción para la pregunta #12", {icon: "warning",});
                        }else
                        {
                            
                            ////////////////////////////////////777
                            swal({
                                  title: "¿Está segura de continuar?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: true,
                                  dangerMode: true
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/PagosAddEncuestaCL/',
                                        data: $('#Add_cl').serialize()+'&contenido='+contenido,
                                        success: function(respuesta) {
                                             if(respuesta=='1'){
                                          
                                             swal("Registro guardado exitosamente", {
                                                          icon: "success",
                                                        });
                                             location.reload();
                                            
                                            }
                                            else {
                                            $('#modal_encuesta_cliente').modal('hide')
                                             swal(respuesta, {
                                                          icon: "error",
                                                        });
                                                
                                            }
                                        }
                                        });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                            //////////////////////////////777
                        }
                 }
            
           
    }
    
        function enviar_add_av(){	
             fecha_trabajo = document.getElementById("fecha_av").value; 
             num_telefono = document.getElementById("movil_av").value;  
             tipo_av = document.getElementById("tipo_llamada_av").value; 
             uno = document.getElementById("uno_av").value; 
             dos = document.getElementById("dos_av").value; 
             tres = document.getElementById("tres_av").value; 
             cuatro = document.getElementById("cuatro_av").value; 
             cinco = document.getElementById("cinco_av").value; 
             seis = document.getElementById("seis_av").value; 
             siete = document.getElementById("siete_av").value; 
             ocho = document.getElementById("ocho_av").value; 
             nueve = document.getElementById("nueve_av").value; 
             completo = $('input[name="completo_av"]:checked').val();
             llamada = document.getElementById("titulo_av");
             contenido = llamada.innerHTML;
             
             
             if(contenido == '2')
                 {
                     mensaje = "Está es la ultima llamada que podrá realizar al cliente y debera seleccionar un estatus final como: 'CANCELADA: NO LOCALIZADOS.', para terminar con la solicitud.";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "Solo podrá intentar contactar una vez más al cliente.";
                         }
                     
                 }
             
             
             
             if(completo == '0')
                 {
                     
                      if(tipo_av == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }
                      else 
                          {
                                  swal({
                                  title: "¿Está segura de continuar con una llamada incompleta?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: true,
                                  dangerMode: true
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                            type: 'POST',
                                            url: '/CallCenter/PagosAddEncuestaAV/',
                                            data: $('#Add_av').serialize()+'&contenido_av='+contenido,
                                            success: function(respuesta) {
                                                 if(respuesta=='1'){
                                                 swal("Registro guardado exitosamente", {
                                                              icon: "success",
                                                            });
                                                 location.reload();
                                                }
                                                else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                    document.getElementById("monto").value = "";
                                                }
                                            }
                                            });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "success",});
                                  }
                                });
                         }
                 }
             else 
                 {
                      if(tipo_av == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }else if(uno  == '') {
                             swal("Seleccione una opción para la pregunta #1", {icon: "warning",});
                        }else if(dos  == '') {
                             swal("Seleccione una opción para la pregunta #2", {icon: "warning",});
                        }else if(tres  == '') {
                             swal("Seleccione una opción para la pregunta #3", {icon: "warning",});
                        }else if(cuatro  == '') {
                             swal("Seleccione una opción para la pregunta #4", {icon: "warning",});
                        }else if(cinco  == '') {
                             swal("Seleccione una opción para la pregunta #5", {icon: "warning",});
                        }else if(seis  == '') {
                             swal("Seleccione una opción para la pregunta #6", {icon: "warning",});
                        }else if(siete  == '') {
                             swal("Seleccione una opción para la pregunta #7", {icon: "warning",});
                        }else if(ocho  == '') {
                             swal("Seleccione una opción para la pregunta #8", {icon: "warning",});
                        }else if(nueve  == '') {
                             swal("Seleccione una opción para la pregunta #9", {icon: "warning",});
                        }else 
                        {
                            
                            ////////////////////////////////////777
                            swal({
                                  title: "¿Está segura de continuar?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: true,
                                  dangerMode: true
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/PagosAddEncuestaAV/',
                                        data: $('#Add_av').serialize()+'&contenido_av='+contenido,
                                        success: function(respuesta) {
                                             if(respuesta=='1'){
                                          
                                             swal("Registro guardado exitosamente", {
                                                          icon: "success",
                                                        });
                                             location.reload();
                                            
                                            }
                                            else {
                                            $('#modal_encuesta_cliente').modal('hide')
                                             swal(respuesta, {
                                                          icon: "error",
                                                        });
                                                document.getElementById("monto").value = "";
                                            }
                                        }
                                        });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                            //////////////////////////////777
                        }
                 }
            
           
    }
    
        function enviar_comentarios_add(){	
             cliente_encuesta = document.getElementById("cliente_encuesta").value; 
             cliente_id = document.getElementById("cliente_id").value; 
             
             cdgco_res = getParameterByName('Suc');
             ciclo_cl_res = getParameterByName('Ciclo');
             cliente_id_res = getParameterByName('Credito');
             
            if(cliente_encuesta != 'PENDIENTE'){
                ///////
                //Puede guardar comentarios iniciales pero no finales
                ////
                $.ajax({
                type: 'POST',
                url: '/CallCenter/Resumen/',
                data: $('#Add_comentarios').serialize()+ "&cdgco_res="+cdgco_res+ "&ciclo_cl_res="+ciclo_cl_res+ "&cliente_id_res="+cliente_id,
                success: function(respuesta) 
                {
                    if(respuesta=='1')
                    {               
                       swal("Registro guardado exitosamente", {
                                icon: "success",
                           });
                       location.reload();
                    }
                    else 
                    {
                        $('#modal_encuesta_cliente').modal('hide')
                        swal(respuesta, {
                        icon: "error",
                        });
                        document.getElementById("monto").value = "";
                    }
                }
               });
                
            }
            else
            {
                swal("Usted debe responder la encuesta del CLIENTE para poder guardar sus comentarios iniciales y poder continuar.", {icon: "warning",});
            }
            
           
    }
    
        function enviar_resumen_add(){	
             cliente_encuesta = document.getElementById("cliente_encuesta").value; 
             cliente_aval = document.getElementById("cliente_aval").value;
             comentarios_iniciales = document.getElementById("comentarios_iniciales").value;
             comentarios_finales = document.getElementById("comentarios_finales").value;
             estatus_solicitud = document.getElementById("estatus_solicitud").value;
             vobo_gerente = document.getElementById("vobo_gerente").value;
            
             
             
             cliente_id = document.getElementById("cliente_id").value; 
             cdgco_res = getParameterByName('Suc');
             ciclo_cl_res = getParameterByName('Ciclo');
             
             if(comentarios_iniciales == ''){
                swal("Necesita ingresar los comentarios inicales para la solicitud del cliente", {icon: "warning",});
             }
            else
                {
                     if(comentarios_finales == '')
                     {
                        swal("Necesita ingresar los comentarios finales para la solicitud del cliente", {icon: "warning",});
                     }
                    else
                    {
                        if(cliente_encuesta == 'PENDIENTE'){
                            swal("La encuesta del cliente no está marcada como validada", {icon: "danger",});
                        }
                        else
                        {
                            if(cliente_aval == 'PENDIENTE')
                                {
                                    swal("La encuesta del aval no está marcada como validada", {icon: "warning",});
                                }
                                else
                                {
                                    if(estatus_solicitud == '')
                                    {
                                        swal("Necesita seleccionar el estatus final de la solicitud", {icon: "warning",});
                                    }
                                    else
                                    {
                                        $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/ResumenEjecutivo/',
                                        data: $('#Add_comentarios').serialize()+ "&cdgco_res="+cdgco_res+ "&ciclo_cl_res="+ciclo_cl_res+ "&cliente_id_res="+cliente_id+ "&comentarios_iniciales="+comentarios_iniciales+ "&comentarios_finales="+comentarios_finales+ "&estatus_solicitud="+estatus_solicitud+ "&vobo_gerente="+vobo_gerente ,
                                        success: function(respuesta) 
                                        {
                                            if(respuesta=='1')
                                            {               
                                               swal("Registro guardado exitosamente", {
                                                        icon: "success",
                                                   });
                                               location.reload();
                                            }
                                            else 
                                            {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                swal(respuesta, {
                                                icon: "error",
                                                });
                                                document.getElementById("monto").value = "";
                                            }
                                        }
                                       });
                                    }                    
                                }
                        }    
                    }
                }
             
            
            
            
            
            
            
             
           
    }
    
    
    
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $suc = $_GET['Suc'];
        $opciones_suc = '';
        $cdgco = array();

        //var_dump( $this->usuario, $this->__usuario);
        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS MIS SUCURSALES</option>
html;
        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
            array_push($cdgco, $val2['CODIGO']);
        }

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo);

        //var_dump($AdministracionOne[4]['NUMERO_INTENTOS_AV']);

        if ($credito != '' && $ciclo != '') {

            if($AdministracionOne[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::render("callcenter_cliente_message_all");
            }
            else
            {

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('suc', $suc);
                View::set('pendientes', 'Mis ');
                View::render("callcenter_cliente_all");
            }
        } else {

            $Solicitudes = CallCenterDao::getAllSolicitudes($cdgco);

            foreach ($Solicitudes as $key => $value) {
               if($value['ESTATUS_CL'] == 'PENDIENTE')
                {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                }
                else if($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO')
                {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                }
                else
                {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if($value['ESTATUS_AV'] == 'PENDIENTE')
                {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                }
                else if($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO')
                {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                }
                else
                {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }








                if($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO')
                {
                    $titulo_boton = 'Seguir';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                }else
                {
                    $titulo_boton = 'Iniciar';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if($value['COMENTARIO_INICIAL'] == '')
                {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                }
                else{
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if($value['COMENTARIO_FINAL'] == '')
                {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                }
                else{
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if($value['ESTATUS_FINAL'] == '')
                {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                }
                else{
                    $icon_ef = 'fa-clock-o';
                    $color_ef = 'warning';
                }

                if($value['VOBO_REG'] == NULL)
                {
                    $vobo = '';
                }
                else{
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';

                }
                //var_dump($vobo);



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        <hr>
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:190px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud
                    <br>
                    </div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}" class="btn btn-primary btn-circle" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>$titulo_boton</b>
                        </a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('sucursal', $opciones_suc);
            View::set('pendientes', 'Mis ');
            View::render("callcenter_pendientes_all");

        }
    }

    public function Global()
    {
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
      
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [6, 10, 20, 30, -1],
                    [6, 10, 20, 30, 'Todos'],
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
         
         function InfoDesactivaEncuesta()
         {
             swal("Atención", "Para continuar con la ENCUESTA del AVAL por favor, es nesesario completar la PRIMER LLAMADA del cliente. ", "warning");
         }
         
    
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $reg = $_GET['Reg'];
        $suc = $_GET['Suc'];
        $opciones_suc = '';
        $cdgco = array();

        //var_dump( $this->usuario, $this->__usuario);
        $ComboSucursales = CallCenterDao::getComboSucursalesGlobales();

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS LAS SUCURSALES</option>
html;
        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
            array_push($cdgco, $val2['CODIGO']);
        }

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo);

        if ($credito != '' && $ciclo != '') {

            if($AdministracionOne[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::set('pendientes', 'Todos los ');
                View::render("callcenter_cliente_message_all");
            }
            else
            {

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('reg', $reg);
                View::set('suc', $suc);
                View::set('pendientes', 'Todos los ');
                View::render("callcenter_cliente_all");
            }
        } else {

            $Solicitudes = CallCenterDao::getAllSolicitudes($cdgco);

            foreach ($Solicitudes as $key => $value) {
                if($value['ESTATUS_CL'] == 'PENDIENTE UNA LLAMADA')
                {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                }
                else if($value['ESTATUS_CL'] == 'NO LOCALIZADO (CANCELAR)')
                {
                    $color = 'danger';
                    $icon = 'fa-exclamation-triangle';
                }
                else if($value['ESTATUS_CL'] == 'VALIDADO EN UNA LLAMADA' || $value['ESTATUS_CL'] == 'VALIDADO EN SEGUNDA LLAMADA')
                {
                    $color = 'success';
                    $icon = 'fa-check';
                }
                else if($value['ESTATUS_CL'] == 'PENDIENTE')
                {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                }

                if($value['ESTATUS_AV'] == 'PENDIENTE UNA LLAMADA')
                {
                    $color_av = 'warning';
                    $icon_av = 'fa-clock-o';
                }
                else if($value['ESTATUS_AV'] == 'NO LOCALIZADO (CANCELAR)')
                {
                    $color_av = 'danger';
                    $icon_av = 'fa-exclamation-triangle';
                }
                else if($value['ESTATUS_AV'] == 'VALIDADO EN UNA LLAMADA' || $value['ESTATUS_AV'] == 'VALIDADO EN SEGUNDA LLAMADA')
                {
                    $color_av = 'success';
                    $icon_av = 'fa-check';
                }
                else if($value['ESTATUS_AV'] == 'PENDIENTE')
                {
                    $color_av = 'primary';
                    $icon_av = 'fa-frown-o';
                }


                if($value['ESTATUS_CL'] == 'PENDIENTE' || $value['ESTATUS_AV'] == 'PENDIENTE')
                {
                    $titulo_boton = 'Iniciar';
                    $color_boton = '#029f3f';
                }else
                {
                    $titulo_boton = 'Continuar';
                    $color_boton = '#F0AD4E';
                }

                if($value['COMENTARIO_INICIAL'] == '')
                {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                }
                else{
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if($value['COMENTARIO_FINAL'] == '')
                {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                }
                else{
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if($value['ESTATUS_FINAL'] == '')
                {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                }
                else{
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if($value['VOBO_GERENTE_REGIONAL'] == '')
                {
                    $icon_vo = 'fa-close';
                    $color_vo = 'danger';
                }
                else{
                    $icon_vo = 'fa-check';
                    $color_vo = 'success';
                }



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_av" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_av"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:190px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    <div><span class="label label-$color_vo"><span class="fa $icon_vo"></span></span> VoBo Gerente Reg</div>
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Reg={$value['CODIGO_REGION']}" class="btn btn-primary btn-circle" style="background: $color_boton"><i class="fa fa-edit"></i> $titulo_boton
                        </a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('sucursal', $opciones_suc);
            View::set('pendientes', 'Todos los ');
            View::render("callcenter_pendientes_all");

        }
    }

    public function Concentrado()
    {
        $extraHeader = <<<html
        <title>Concentrado de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [6, 10, 20, 30, -1],
                    [6, 10, 20, 30, 'Todos'],
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

        $Fecha = $_GET['Fec'];
        $Region = $_GET['Reg'];
        $Estatus = $_GET['Est'];
        $fechaActual = date('Y-m-d');
        $opciones_region = "";

        if($Fecha == '')
        {
            $Fecha = $fechaActual;
        }

        $Regiones = CallCenterDao::getAllRegiones();

        $opciones_region .= <<<html
                <option value="0">(000) TODAS LAS REGIONES</option>
html;
        foreach ($Regiones as $key_r => $val_R) {
            $opciones_region .= <<<html
                <option value="{$val_R['CODIGO']}">({$val_R['CODIGO']}) {$val_R['NOMBRE']} ------- {$val_R['REGION']}</option>
html;
        }


            $Solicitudes = CallCenterDao::getAllSolicitudesConcentrado($Fecha, $Region);

            foreach ($Solicitudes as $key => $value) {

                ///////////
                if($value['LLAMADA_UNO'] == '' && $value['PRG_UNO_AV'] == '' && $value['HORA_LLAMADA_UNO'] == '')
                {
                    $titulo_estatus_a = "PENDIENTE";
                    $titulo_color_a = "Pendiente de validar";
                    $titulo_ver_expediente_a = "Estatus Encuesta";
                }
                else{
                    if($value['LLAMADA_UNO'] >= 1 && $value['PRG_UNO_AV'] != '')
                    {

                        if($value['HORA_LLAMADA_DOS'] == ' ')
                        {
                            $titulo_estatus_a = "FINALIZADA";
                            $titulo_color_a = "Validado en 1er llamada";
                            $validado_en_a = "Validado en 1 ";
                        }
                        else{

                            $titulo_estatus_a = "FINALIZADA";
                            $titulo_color_a = "Validado en 2da llamada";
                            $validado_en_a = "Validado en 1 ";
                        }


                    }
                    else
                    {
                        if($value['LLAMADA_UNO'] >= 1 && $value['HORA_LLAMADA_DOS'] == ' ')
                        {
                            $titulo_estatus_a = "PENDIENTE";
                            $titulo_color_a = "Pendiente 1 Llamada";
                            $titulo_boton_encuesta_fin_a = "Iniciar Encuesta 2° Llamada (AVAL)";
                            $titulo_ver_expediente_a = "Estatus Encuesta";
                        }
                        else
                        {
                            $titulo_estatus_a = "FINALIZADA";
                            $titulo_color_a = "NO LOCALIZADO (2 llamadas)";
                            $validado_en_a = "Validado en 1 ";
                        }

                    }
                }

                ///////////////


                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CLAVE']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE_CLIENTE']}</label></td>
                    <td style="padding-top: 22px !important;"><span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important;">$titulo_estatus_a</span></td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Reg={$value['CODIGO_REGION']}" class="btn btn-success btn-circle" style="background: #029f3f"><i class="fa fa-edit"></i> Iniciar Validación</a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('Region', $opciones_region);
            View::set('fechaActual', $Fecha);
            View::render("callcenter_concentrado_all");

    }

    public function Administracion()
    {
        $extraHeader = <<<html
        <title>Administrar Sucursales/Analistas</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
      
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
         
         function enviar_add()
         {	
                    fecha_inicio = new Date(document.getElementById("fecha_inicio").value); 
                    fecha_fin =  new Date(document.getElementById("fecha_fin").value);
                    let diferencia = fecha_fin.getTime() - fecha_inicio.getTime();
                    let diasDeDiferencia = diferencia / 1000 / 60 / 60 / 24;
                    console.log(diasDeDiferencia); // resultado: 357
                    
                    if(diasDeDiferencia == 0)
                        {swal("Las fechas no pueden ser iguales", {icon: "warning",});}
                        else if(diasDeDiferencia  <= 0)
                        {swal("Recuerda que la Fecha de Fin no puede ser menor a la Fecha de Inicio, verifique la información.", {icon: "warning",});
                        }else
                            {
                                $.ajax({
                                      type: 'POST',
                                      url: '/CallCenter/AsignarSucursal/',
                                      data: $('#Add_AS').serialize(),
                                      success: function(respuesta) {
                                          if(respuesta=='1'){
                                             swal("Registro guardado exitosamente", {
                                                  icon: "success",
                                                  });
                                                 location.reload();
                                             }
                                          else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                    document.getElementById("monto").value = "";
                                                }
                                            }
                                      });
                            }
        }
    
      </script>
html;

        $Analistas = CallCenterDao::getAllAnalistas();
        $Regiones = CallCenterDao::getAllRegiones();
        $getAnalistas = '';
        $getRegiones = '';
        $opciones = '';
        $opciones_region = '';

        foreach ($Analistas as $key => $val2) {

            $opciones .= <<<html
                <option  value="{$val2['USUARIO']}">({$val2['USUARIO']}) {$val2['NOMBRE']}</option>
html;
        }

        foreach ($Regiones as $key_r => $val_R) {

            $opciones_region .= <<<html
                <option  value="{$val_R['CODIGO']}">({$val_R['CODIGO']}) {$val_R['NOMBRE']}</option>
html;
    }


        $getAnalistas = <<<html
         <div class="col-md-12">
                <div class="form-group">
                     <label for="ejecutivo">Ejecutivo *</label>
                     <select class="form-control" autofocus type="select" id="ejecutivo" name="ejecutivo">
                        {$opciones}
                     </select>
                </div>
         </div>
html;
        $getRegiones = <<<html
         <div class="col-md-12">
                <div class="form-group">
                     <label for="region">Región *</label>
                     <select class="form-control" autofocus type="select" id="region" name="region">
                        {$opciones_region}
                     </select>
                     <small id="emailHelp" class="form-text text-muted">Asignarás todas las sucursales de la Región</small>
                </div>
         </div>
html;


        $AnalistasAsignadas = CallCenterDao::getAllAnalistasAsignadas();

        foreach ($AnalistasAsignadas as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CDGPE']}</td>
                    <td style="padding: 0px !important;">{$value['CDGCO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_INICIO']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_FIN']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_ALTA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGOCPE']}</td>
                </tr>
html;
        }

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('Analistas', $getAnalistas);
            View::set('Regiones', $getRegiones);
            View::set('tabla', $tabla);
            View::render("asignar_sucursales_analistas");
    }

    public function Historico()
    {
        $extraHeader = <<<html
        <title>Consulta de Encuestas Terminadas</title>
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

        $cdgco = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);

        foreach ($ComboSucursales as $key => $val2) {
            array_push($cdgco, $val2['CODIGO']);
        }


        if ($Inicial != '' || $Final != '') {
            /////////////////////////////////
            $Consulta = CallCenterDao::getAllSolicitudesHistorico($Inicial, $Final, $cdgco);

            foreach ($Consulta as $key => $value) {

                if($value['ESTATUS_CL'] == 'PENDIENTE UNA LLAMADA')
                {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                }
                else if($value['ESTATUS_CL'] == 'NO LOCALIZADO (CANCELAR)')
                {
                    $color = 'danger';
                    $icon = 'fa-exclamation-triangle';
                }
                else if($value['ESTATUS_CL'] == 'VALIDADO EN UNA LLAMADA' || $value['ESTATUS_CL'] == 'VALIDADO EN SEGUNDA LLAMADA')
                {
                    $color = 'success';
                    $icon = 'fa-check';
                }
                else if($value['ESTATUS_CL'] == 'PENDIENTE')
                {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                }

                if($value['ESTATUS_AV'] == 'PENDIENTE UNA LLAMADA')
                {
                    $color_av = 'warning';
                    $icon_av = 'fa-clock-o';
                }
                else if($value['ESTATUS_AV'] == 'NO LOCALIZADO (CANCELAR)')
                {
                    $color_av = 'danger';
                    $icon_av = 'fa-exclamation-triangle';
                }
                else if($value['ESTATUS_AV'] == 'VALIDADO EN UNA LLAMADA' || $value['ESTATUS_AV'] == 'VALIDADO EN SEGUNDA LLAMADA')
                {
                    $color_av = 'success';
                    $icon_av = 'fa-check';
                }
                else if($value['ESTATUS_AV'] == 'PENDIENTE')
                {
                    $color_av = 'primary';
                    $icon_av = 'fa-frown-o';
                }


                if($value['ESTATUS_CL'] == 'PENDIENTE' || $value['ESTATUS_AV'] == 'PENDIENTE')
                {
                    $titulo_boton = 'Iniciar Validación';
                    $color_boton = '#029f3f';
                }else
                {
                    $titulo_boton = 'Reanuda Validación';

                    $color_boton = '#F0AD4E';
                }



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important;">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_av" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_av"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Reg={$value['CODIGO_REGION']}" class="btn btn-primary btn-circle" style="background: $color_boton"><i class="fa fa-edit"></i> $titulo_boton
                        </a>
                    </td>
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
                View::render("Historico_Call_Center");
            }
           //////////////////////////////////
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


            $Consulta = CallCenterDao::getAllSolicitudesHistorico($fechaActual, $fechaActual, $cdgco);

            foreach ($Consulta as $key => $value) {

                if($value['ESTATUS_CL'] == 'PENDIENTE UNA LLAMADA')
                {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                }
                else if($value['ESTATUS_CL'] == 'NO LOCALIZADO (CANCELAR)')
                {
                    $color = 'danger';
                    $icon = 'fa-exclamation-triangle';
                }
                else if($value['ESTATUS_CL'] == 'VALIDADO EN UNA LLAMADA' || $value['ESTATUS_CL'] == 'VALIDADO EN SEGUNDA LLAMADA')
                {
                    $color = 'success';
                    $icon = 'fa-check';
                }
                else if($value['ESTATUS_CL'] == 'PENDIENTE')
                {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                }

                if($value['ESTATUS_AV'] == 'PENDIENTE UNA LLAMADA')
                {
                    $color_av = 'warning';
                    $icon_av = 'fa-clock-o';
                }
                else if($value['ESTATUS_AV'] == 'NO LOCALIZADO (CANCELAR)')
                {
                    $color_av = 'danger';
                    $icon_av = 'fa-exclamation-triangle';
                }
                else if($value['ESTATUS_AV'] == 'VALIDADO EN UNA LLAMADA' || $value['ESTATUS_AV'] == 'VALIDADO EN SEGUNDA LLAMADA')
                {
                    $color_av = 'success';
                    $icon_av = 'fa-check';
                }
                else if($value['ESTATUS_AV'] == 'PENDIENTE')
                {
                    $color_av = 'primary';
                    $icon_av = 'fa-frown-o';
                }


                if($value['ESTATUS_CL'] == 'PENDIENTE' || $value['ESTATUS_AV'] == 'PENDIENTE')
                {
                    $titulo_boton = 'Iniciar Validación';
                    $color_boton = '#029f3f';
                }else
                {
                    $titulo_boton = 'Reanuda Validación';

                    $color_boton = '#F0AD4E';
                }



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important;">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_av" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_av"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Reg={$value['CODIGO_REGION']}" class="btn btn-primary btn-circle" style="background: $color_boton"><i class="fa fa-edit"></i> $titulo_boton
                        </a>
                    </td>
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
                View::render("Historico_Call_Center");
            }

        }
    }

    public function PagosAddEncuestaCL(){
        $encuesta = new \stdClass();
        $fecha_solicitud = MasterDom::getDataAll('fecha_solicitud');
        $encuesta->_fecha_solicitud = $fecha_solicitud;
        $encuesta->_cdgre = MasterDom::getData('cdgre');
        $encuesta->_cliente = MasterDom::getData('cliente_id');
        $encuesta->_cdgco = MasterDom::getData('cdgco');
        $encuesta->_cdgns = MasterDom::getData('cdgns');
        $encuesta->_fecha = MasterDom::getData('fecha_cl');
        $encuesta->_ciclo = MasterDom::getData('ciclo_cl');
        $encuesta->_movil = MasterDom::getData('movil_cl');
        $encuesta->_tipo_llamada = MasterDom::getData('tipo_llamada_cl');
        $encuesta->_uno = MasterDom::getData('uno_cl');
        $encuesta->_dos = MasterDom::getData('dos_cl');
        $encuesta->_tres = MasterDom::getData('tres_cl');
        $encuesta->_cuatro = MasterDom::getData('cuatro_cl');
        $encuesta->_cinco = MasterDom::getData('cinco_cl');
        $encuesta->_seis = MasterDom::getData('seis_cl');
        $encuesta->_siete = MasterDom::getData('siete_cl');
        $encuesta->_ocho = MasterDom::getData('ocho_cl');
        $encuesta->_nueve = MasterDom::getData('nueve_cl');
        $encuesta->_diez = MasterDom::getData('diez_cl');
        $encuesta->_once = MasterDom::getData('once_cl');
        $encuesta->_doce = MasterDom::getData('doce_cl');
        $encuesta->_llamada = MasterDom::getData('contenido');
        $encuesta->_completo = MasterDom::getData('completo');

        $id = CallCenterDao::insertEncuestaCL($encuesta);
    }

    public function PagosAddEncuestaAV(){
        $encuesta = new \stdClass();
        $fecha_solicitud = MasterDom::getDataAll('fecha_solicitud_av');
        $encuesta->_fecha_solicitud = $fecha_solicitud;
        $encuesta->_cdgre = MasterDom::getData('cdgre_av');
        $encuesta->_cliente = MasterDom::getData('cliente_id_av');
        $encuesta->_cdgco = MasterDom::getData('cdgco_av');
        $encuesta->_fecha = MasterDom::getData('fecha_av');
        $encuesta->_ciclo = MasterDom::getData('ciclo_av');

        $encuesta->_movil = MasterDom::getData('movil_av');
        $encuesta->_tipo_llamada = MasterDom::getData('tipo_llamada_av');
        $encuesta->_uno = MasterDom::getData('uno_av');
        $encuesta->_dos = MasterDom::getData('dos_av');
        $encuesta->_tres = MasterDom::getData('tres_av');
        $encuesta->_cuatro = MasterDom::getData('cuatro_av');
        $encuesta->_cinco = MasterDom::getData('cinco_av');
        $encuesta->_seis = MasterDom::getData('seis_av');
        $encuesta->_siete = MasterDom::getData('siete_av');
        $encuesta->_ocho = MasterDom::getData('ocho_av');
        $encuesta->_nueve = MasterDom::getData('nueve_av');
        $encuesta->_llamada = MasterDom::getData('contenido_av');
        $encuesta->_completo = MasterDom::getData('completo_av');



        $id = CallCenterDao::insertEncuestaAV($encuesta);
    }

    public function Resumen(){
        $encuesta = new \stdClass();
        $encuesta->_cdgco = MasterDom::getData('cdgco_res');
        $encuesta->_cliente = MasterDom::getData('cliente_id_res');
        $encuesta->_ciclo = MasterDom::getData('ciclo_cl_res');
        $encuesta->_comentarios_iniciales = MasterDom::getDataAll('comentarios_iniciales');
        $encuesta->_comentarios_finales = MasterDom::getData('comentarios_finales');

        $id = CallCenterDao::UpdateResumen($encuesta);
    }

    public function ResumenEjecutivo(){
        $encuesta = new \stdClass();
        $encuesta->_cdgco = MasterDom::getData('cdgco_res');
        $encuesta->_cliente = MasterDom::getData('cliente_id_res');
        $encuesta->_ciclo = MasterDom::getData('ciclo_cl_res');
        $encuesta->_comentarios_iniciales = MasterDom::getDataAll('comentarios_iniciales');
        $encuesta->_comentarios_finales = MasterDom::getData('comentarios_finales');
        $encuesta->_estatus_solicitud = MasterDom::getData('estatus_solicitud');
        $encuesta->_vobo_gerente = MasterDom::getData('vobo_gerente');

        $id = CallCenterDao::UpdateResumenFinal($encuesta);
    }

    public function AsignarSucursal(){

        $asigna = new \stdClass();
        $asigna->_fecha_registro = MasterDom::getDataAll('fecha_registro');
        $asigna->_fecha_inicio = MasterDom::getData('fecha_inicio');
        $asigna->_fecha_fin = MasterDom::getData('fecha_fin');
        $asigna->_ejecutivo = MasterDom::getData('ejecutivo');
        $asigna->_region = MasterDom::getData('region');

        $id = CallCenterDao::insertAsignaSucursal($asigna);
    }


}
