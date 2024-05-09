<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\CallCenter AS CallCenterDao;

class CallCenter extends Controller{

    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
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
          if(this.value == 'N')
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
             
             nombre_aval_cl = document.getElementById("nombre_aval_cl").value; 
             id_aval_cl = document.getElementById("id_aval_cl").value; 
             
             completo = $('input[name="completo"]:checked').val();
             llamada = document.getElementById("titulo");
             contenido = llamada.innerHTML;
             
             
             if(contenido == '2')
                 {
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                     
                                      const agregar_CL = document.getElementById('agregar_CL');
                                      agregar_CL.disabled = true; 
                                      
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      const agregar_CL = document.getElementById('agregar_CL');
                                      agregar_CL.disabled = true; 
                                      
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
             fecha_trabajo = document.getElementById("fecha_solicitud_av").value; 
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
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      const agregar_AV = document.getElementById('agregar_av');
                                      agregar_AV.disabled = true; 
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      const agregar_AV = document.getElementById('agregar_av');
                                      agregar_AV.disabled = true; 
                                      
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
                            if(estatus_solicitud == '')
                               {
                                  swal("Necesita seleccionar el estatus final de la solicitud", {icon: "warning",});
                               }
                               else
                                   {
                                       const agregar_TS = document.getElementById('terminar_solicitud');
                                       agregar_TS.disabled = true; 
                
                                        $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/ResumenEjecutivo/',
                                        data: $('#Add_comentarios').serialize()+ "&cdgco_res="+cdgco_res+ "&ciclo_cl_res="+ciclo_cl_res+ "&cliente_id_res="+cliente_id+ "&comentarios_iniciales="+comentarios_iniciales+ "&comentarios_finales="+comentarios_finales+ "&estatus_solicitud="+estatus_solicitud+ "&vobo_gerente="+vobo_gerente ,
                                        success: function(respuesta) 
                                        {
                                            if(respuesta=='1')
                                            {               
                                              swal("Se guardo correctamente la información.",
                                              {
                                              icon: "success",
                                              buttons: {
                                                catch: {
                                                  text: "Aceptar",
                                                  value: "catch",
                                                }
                                              },
                                              
                                            })
                                            .then((value) => {
                                              switch (value) {
                                                case "catch":
                                                 window.location.href = '/CallCenter/Pendientes/'; //Will take you to Google.
                                                 break;
                                              }
                                            });
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
    
        function check_2610()
        {
             llamada = document.getElementById("titulo");
             contenido = llamada.innerHTML;
             
             
            swal({
            title: "¿Está segura de continuar con el registro de una solicitud con Información Inconsistente?",
            text: "",
            icon: "warning",
            buttons: ["Cancelar", "Continuar"],
            dangerMode: false
            })
            .then((willDelete) => {
            if (willDelete) {
                                     
            const agregar_CL = document.getElementById('agregar_CL');
            agregar_CL.disabled = true; 
                                      
            $.ajax({
                    type: 'POST',
                    url: '/CallCenter/PagosAddEncuestaCL/',
                    data: $('#Add_cl').serialize()+'&contenido='+contenido+'&completo=0',
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
                            document.getElementById('check_2610').checked = false;
                            return false;
                         }
            });
                     
        }
    
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $suc = $_GET['Suc'];
        $reg = $_GET['Reg'];
        $fec = $_GET['Fec'];
        $act = $_GET['Act'];


        $opciones_suc = '';
        $cdgco_all = array();
        $cdgco_suc = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        //var_dump($ComboSucursales);

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS MIS SUCURSALES</option>
html;
        foreach ($ComboSucursales as $key => $val2) {

            if($suc == $val2['CODIGO'])
            {
                $sel = 'selected';
            }else{
                $sel = '';
            }

            $opciones_suc .= <<<html
                <option {$sel} value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
            array_push($cdgco_all, $val2['CODIGO']);
        }


        //var_dump($AdministracionOne[4]['NUMERO_INTENTOS_AV']);

        if ($credito != '' && $ciclo != '' && $fec != '') {


            $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

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
                View::set('reg', $reg);
                View::set('cdgpe', $this->__usuario);
                View::set('pendientes', 'Mis ');

                if($act == 'N'){
                    View::render("callcenter_cliente_all_disable");
                }
                else{
                    View::render("callcenter_cliente_all");
                }
            }
        } else {

            if($credito == '' && $ciclo == '')
            {
                if($suc == '000' || $suc == '')
                {
                    $Solicitudes = CallCenterDao::getAllSolicitudes($cdgco_all);
                    //var_dump("1");
                }
                else
                {
                    //var_dump("1010");
                    if($this->__perfil == 'ADMIN' || $this->__perfil == 'ACALL')
                    {
                        $Solicitudes = CallCenterDao::getAllSolicitudes('');
                    }
                    else
                    {
                        array_push($cdgco_suc, $suc);
                        $Solicitudes = CallCenterDao::getAllSolicitudes($cdgco_suc);
                    }

                }
            }
            else
            {
                array_push($cdgco_suc, $suc);
                $Solicitudes = CallCenterDao::getAllSolicitudes($cdgco_suc);
                //var_dump($Solicitudes);
                //var_dump("1");
            }
            //var_dump($Solicitudes);

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
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
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
                if($value['COMENTARIO_PRORROGA'] == '')
                {
                    $icon_cp_a = 'fa-close';
                    $color_cp_a = 'danger';
                }
                else{
                    $icon_cp_a = 'fa-check';
                    $color_cp_a = 'success';
                }

                if($value['VOBO_REG'] == NULL)
                {
                    $vobo = '';
                }
                else{
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';

                }
                if($value['PRORROGA'] == 2)
                {
                    $prorroga = '<hr><div><b>TIENE ACTIVA LA PRORROGA </b><span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #9101b2"><span class="fa fa-bell"> </span> </span></div><hr>';
                    $comentario_prorroga = '<div><span class="label label-'.$color_cp_a.'"><span class="fa '.$icon_cp_a.'"></span></span> Comentarios Prorroga</div>';
                }
                else{
                    $prorroga = '';
                    $comentario_prorroga = '';
                }

                if($value['REACTIVACION'] != '400')
                {
                    $reactivacion = '';
                }
                else{
                    $reactivacion = '<hr><div><b>SE REACTIVO LA SOLICITUD </b><span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #006c75"><span class="fa fa-bell"> </span> </span></div><hr>';
                }
                //var_dump($vobo);

                if(substr($value['TEL_CL'],0,1) == '(')
                {
                    $format = $value['TEL_CL'];
                }
                else{
                    $format = "(".substr($value['TEL_CL'],0,3).")"." ".substr($value['TEL_CL'],3,3)." - ".substr($value['TEL_CL'],6,4);

                }
                //var_dump($value['TEL_CL']);


                if($value['ID_SCALL'] == ''){
                    $id_scall = '0000';
                }else{
                    $id_scall = $value['ID_SCALL'];
                }


                if($value['RECOMENDADO'] != '')
                {
                    $recomendado = '<div><b>CAMPAÑA ACTIVA</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>RECOMIENDA MÁS Y PAGA MENOS <em></em></b><hr>';
                }


                $tabla .= <<<html
                <tr style="padding: 0px !important; ">
                    <td style="padding: 5px !important; width:65px !important;">
                    
                    <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                    <hr>
                    <div><label>{$value['CDGNS']}-{$value['CICLO']}</label></div>
                    
                    
                    </td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;">
                            <span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label> <br><label><span class="fa fa-phone"></span> {$format}</label>
                           
                    </td>                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                         <br>
                       
                        $prorroga
                        $reactivacion
                         {$recomendado}
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    $comentario_prorroga
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=S&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>$titulo_boton</b>
                        </a>
                    </td>
                </tr>
html;
            }




            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('cdgpe', $this->__usuario);
            View::set('sucursal', $opciones_suc);
            View::set('pendientes', 'Mis ');
            View::render("callcenter_pendientes_all");

        }
    }
    public function Busqueda()
    {
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
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
        
        $('#doce_cl').on('change', function() {
          if(this.value == 'N')
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
         
      </script>
html;

        $credito = $_GET['Credito'];

        if ($credito != '') {

                $Administracion = CallCenterDao::getAllSolicitudesBusquedaRapida($credito);
                foreach ($Administracion as $key => $value) {

                    if($value['ESTATUS_GENERAL'] == "SIN HISTORIAL")
                    {
                        $ver_resumen = '';

                    }else{
                        $ver_resumen = <<<html
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
                    }

                    $monto = number_format($value['MONTO'], 2);
                    $tabla .= <<<html
                     <tr style="padding: 0px !important; ">
                    <td style="padding: 5px !important; width:65px !important; width:125px !important;">
                        <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                        <hr>
                        <div><label>Crédito: {$value['CDGNS']}</label></div>
                        <div><label>Ciclo: {$value['CICLO']}</label></div>
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CDGCO']}) <b>{$value['NOMBRE_SUCURSAL']}</b>
                        <br>
                        <span class="fa fa-briefcase"></span> CLAVE EJECUTIVO: {$value['ID_EJECUTIVO']}
                        <br>
                        <span class="fa fa-briefcase"></span> FECHA DE CAPTURA ADMINISTRADORA: <b>{$value['FECHA_SOL']}</b>
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left; width:225px !important;">
                         ESTATUS CLIENTE:  <br><b>{$value['ESTATUS_CL']}</b>
                        <br>
                        <br>
                         ESTATUS AVAL: <br><b>{$value['ESTATUS_AV']}</b>
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left; width:225px !important;">
                         <span class="fa fa-calendar"></span> FECHA DE VALIDACIÓN: <br> <b>{$value['FECHA_TRABAJO']}</b>
                        <br>
                       
                       
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left">
                         <span></span> ESTATUS GENERAL:  <br><b>{$value['ESTATUS_GENERAL']}</b>
                        <br>
                        <br>
                         <span></span> LOCALÍCELA EN: <br><b>{$value['BANDEJA']}</b>
                    </td>
                  
                    
                    <td style="padding-top: 22px !important;">
                        {$ver_resumen}
                    </td>
                </tr>
html;
                }

                View::set('tabla', $tabla);
                View::set('credito', $credito);
                View::set('usuario', $this->__usuario);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("busqueda_registro_rapida");

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("callcenter_busqueda_rapida");
        }

    }

    public function Prorroga()
    {
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;        $extraFooter = <<<html
      <script>
      
      function ProrrogaAutorizar(id_call)
         {
              swal({
              title: "¿Está segura de autorizar la prorroga?",
              text: '',
              icon: "warning",
              buttons: ["Denegar Solicitud", "Autorizar"],
              dangerMode: false
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ProrrogaUpdate/',
                        data: 'prorroga=2'+'&id_call='+id_call,
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Autorizada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
              else {
                 $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ProrrogaUpdate/',
                        data: 'prorroga=3'+'&id_call='+id_call,
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Denegada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
            });
         }
      
       function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        
        $('#doce_cl').on('change', function() {
          if(this.value == 'N')
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
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                                              swal("Se guardo correctamente la información.",
                                              {
                                              icon: "success",
                                              buttons: {
                                                catch: {
                                                  text: "Aceptar",
                                                  value: "catch",
                                                }
                                              },
                                              
                                            })
                                            .then((value) => {
                                              switch (value) {
                                                case "catch":
                                                 window.location.href = '/CallCenter/Pendientes/'; //Will take you to Google.
                                                 break;
                                              }
                                            });
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
        $fec = $_GET['Fec'];
        $opciones_suc = '';
        $cdgco_all = array();
        $cdgco_suc = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        //var_dump($ComboSucursales);

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS LAS SUCURSALES</option>
html;
        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
            array_push($cdgco_all, $val2['CODIGO']);
        }

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

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

            if($credito == '' && $ciclo == '' && $suc != '')
            {
                if($suc == '000')
                {
                    $Solicitudes = CallCenterDao::getAllSolicitudesProrroga($cdgco_all);
                }
                else
                {
                    array_push($cdgco_suc, $suc);
                    $Solicitudes = CallCenterDao::getAllSolicitudesProrroga($cdgco_suc);
                }
            }
            else
            {
                $Solicitudes = CallCenterDao::getAllSolicitudesProrroga($cdgco_all);
                //var_dump($Solicitudes);
            }


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
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
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
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
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
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ProrrogaAutorizar('{$value['ID_SCALL']}');" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>Autorizar Prorroga</b>
                        </a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('sucursal', $opciones_suc);
            View::render("callcenter_prorroga_all");

        }
    }

    public function Reactivar()
    {
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;        $extraFooter = <<<html
      <script>
      
      function ReactivarAutorizar(id_call)
         {
              swal({
              title: "¿Está segura de autorizar la reactivación de la solicitud?",
              text: '',
              icon: "warning",
              buttons: ["Denegar Solicitud", "Autorizar"],
              dangerMode: false
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ReactivarSolicitudAdminPost/',
                        data: 'id_call='+id_call+'&opcion=SI',
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Autorizada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
              else {
                 $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ReactivarSolicitudAdminPost/',
                       data: 'id_call='+id_call+'&opcion=NO',
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Denegada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
            });
         }
      
       function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        
        $('#doce_cl').on('change', function() {
          if(this.value == 'N')
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
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
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
                                              swal("Se guardo correctamente la información.",
                                              {
                                              icon: "success",
                                              buttons: {
                                                catch: {
                                                  text: "Aceptar",
                                                  value: "catch",
                                                }
                                              },
                                              
                                            })
                                            .then((value) => {
                                              switch (value) {
                                                case "catch":
                                                 window.location.href = '/CallCenter/Pendientes/'; //Will take you to Google.
                                                 break;
                                              }
                                            });
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
        $fec = $_GET['Fec'];
        $opciones_suc = '';
        $cdgco_all = array();
        $cdgco_suc = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        //var_dump($ComboSucursales);

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS LAS SUCURSALES</option>
html;
        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
            array_push($cdgco_all, $val2['CODIGO']);
        }

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

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

            if($credito == '' && $ciclo == '' && $suc != '')
            {
                if($suc == '000')
                {
                    $Solicitudes = CallCenterDao::getAllSolicitudesReactivar($cdgco_all);
                }
                else
                {
                    array_push($cdgco_suc, $suc);
                    $Solicitudes = CallCenterDao::getAllSolicitudesReactivar($cdgco_suc);
                }
            }
            else
            {
                $Solicitudes = CallCenterDao::getAllSolicitudesReactivar($cdgco_all);
                //var_dump($Solicitudes);
            }


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
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
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
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
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
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ReactivarAutorizar('{$value['ID_SCALL']}');" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>Autorizar Reactivación</b>
                        </a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('sucursal', $opciones_suc);
            View::render("callcenter_reactivar_all");

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
        $fec = $_GET['Fec'];
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

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

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
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
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
                if($value['COMENTARIO_PRORROGA'] == '')
                {
                    $icon_cp_a = 'fa-close';
                    $color_cp_a = 'danger';
                }
                else{
                    $icon_cp_a = 'fa-check';
                    $color_cp_a = 'success';
                }

                if($value['VOBO_REG'] == NULL)
                {
                    $vobo = '';
                }
                else{
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';

                }

                if($value['PRORROGA'] == 2)
                {
                    $prorroga = '<hr><div><b>TIENE ACTIVA LA PRORROGA </b><span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #9101b2"><span class="fa fa-bell"> </span> </span></div><hr>';
                    $comentario_prorroga = '<div><span class="label label-'.$color_cp_a.'"><span class="fa '.$icon_cp_a.'"></span></span> Comentarios Prorroga</div>';
                }
                else{
                    $prorroga = '';
                    $comentario_prorroga = '';
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
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        $prorroga
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    $comentario_prorroga
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Reg={$value['CODIGO_REGION']}" class="btn btn-primary btn-circle" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>$titulo_boton</b>
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
    
        
        function DeleteCDGCO(cdgco)
        {
            $.ajax({
            type: 'POST',
            url: '/CallCenter/DeleteAsignaSuc/',
            data: "cdgco="+ cdgco,
            success: function(respuesta) 
            {
              if(respuesta=='1')
                {               
                      swal("Registro actualizado exitosamente", {
                                              icon: "success",
                                            });
                      location.reload();
                }
            }
          });
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
                     <label for="region">Sucursal *</label>
                     <select class="form-control" autofocus type="select" id="region" name="region">
                        {$opciones_region}
                     </select>
                     <small id="emailHelp" class="form-text text-muted">Selecciona la sucursal que deseas asignar</small>
                </div>
         </div>
html;


        $AnalistasAsignadas = CallCenterDao::getAllAnalistasAsignadas();

        foreach ($AnalistasAsignadas as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td>{$value['CDGPE']}</td>
                    <td>{$value['NOMBRE_EJEC']}</td>
                    <td>{$value['CDGCO']}</td>
                    <td style="text-align: left;"><b>{$value['NOMBRE']}</b></td>
                    <td>{$value['FECHA_INICIO']}</td>
                    <td>{$value['FECHA_FIN']}</td>
                    <td>{$value['FECHA_ALTA']}</td>
                    <td>{$value['CDGOCPE']}</td>
                    <td style="padding: 0px !important;">
                       <button type="button" class="btn btn-danger btn-circle" onclick="DeleteCDGCO('{$value['CDGCO']}')"><i class="fa fa-trash"></i></button>
                    </td>
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

    public function DeleteAsignaSuc(){

        $cdgco = MasterDom::getDataAll('cdgco');
        $id = CallCenterDao::DeleteAsignaSuc($cdgco);
    }

    public function Historico()
    {
        $extraHeader = <<<html
        <title>Histórico de Llamadas</title>
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
                    [13, 50, 'Todos'],
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
            
        });
      
       fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            cdgco = getParameterByName('Suc');
            usuario = getParameterByName('Was');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/CallCenter/HistorialGenera/?Inicial='+fecha1+'&Final='+fecha2+'&Suc='+cdgco);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
      
      function ProrrogaPedir(id_call, estatus, reactivacion)
         {
             
                if(reactivacion == '1')
                 {
                      swal("Actualmente tiene una REACTIVACION en espera de validación", {icon: "warning",});
                      return;   
                 }
                
                 if(estatus == '1')
                 {
                      swal("Su solicitud de PRORROGA esta siendo validada", {icon: "warning",});
                      return;
                      
                 }else if(estatus == '3'){
                    swal("Su solicitud de PRORROGA fue declinada", {icon: "warning",});
                    return;
                 }
                     
              swal({
              title: "¿Está segura de solicitar a su administradora prorroga para esta solicitud?",
              text: '',
              icon: "warning",
              buttons: ["Cancelar", "Continuar"],
              dangerMode: false
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ProrrogaUpdate/',
                        data: 'prorroga=1'+'&id_call='+id_call,
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
                            }
                        }
                        });
              }
              else {
                swal("Operación Cancelada", {icon: "warning",});
              }
            });
         }
         
      function VerResumen()
         {
               alert("Hola");
         }
         
         
      function ReactivarSolicitud(id_call, estatus, reactivacion )
         {
             
             if(estatus == '1')
                 {
                      swal("Actualmente tiene una PRORROGA en espera de validación", {icon: "warning",});
                      return;   
                 }
                
                 if(reactivacion == '1')
                 {
                      swal("Su solicitud de REACTIVACIÓN esta siendo validada", {icon: "warning",});
                      return;
                      
                 }else if(reactivacion == '3'){
                    swal("Su solicitud de REACTIVACIÓN fue declinada", {icon: "warning",});
                    return;
                 }
                 
                 
              swal({
              title: "¿Está segura de solicitar la reactivación de la solicitud?",
              text: 'Usted podrá seguir editando la solicitud',
              icon: "warning",
              buttons: ["Cancelar", "Continuar"],
              dangerMode: false
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ReactivarSolicitudEjec/',
                        data: 'id_call='+id_call,
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
                            }
                        }
                        });
              }
              else {
                swal("Operación Cancelada", {icon: "warning",});
              }
            });
         }
      
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Sucursal = $_GET['Suc'];



        $cdgco = array();

        //////////////////////////////////////////

        $opciones_suc = '';

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS MIS SUCURSALES INCLUIDAS OTRAS NO MOSTRADAS EN LA LISTA</option>
html;
        foreach ($ComboSucursales as $key => $val2) {

            if($Sucursal == $val2['CODIGO'])
            {
                $sel = 'selected';
            }else{
                $sel = '';
            }

            $opciones_suc .= <<<html
                <option {$sel} value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
            array_push($cdgco, $val2['CODIGO']);
        }

        ///////////////////////////////////////


        if ($Inicial != '' || $Final != '' || $Sucursal != '') {
            /////////////////////////////////
            $Consulta = CallCenterDao::getAllSolicitudesHistorico($Inicial, $Final, $cdgco, $this->__usuario, $this->__perfil, $Sucursal);
            foreach ($Consulta as $key => $value) {

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
                else if($value['ESTATUS_CL'] == '-')
                {
                    $color = 'danger';
                    $icon = 'fa-close';
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
                else if($value['ESTATUS_AV'] == '-')
                {
                    $color_a = 'danger';
                    $icon_a = 'fa-close';
                }
                else
                {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO')
                {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                }else
                {
                    $titulo_boton = 'Pedir Prorroga';
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
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if($value['VOBO_REG'] == NULL)
                {
                    $vobo = '';
                }
                else{
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if($value['PRORROGA'] == NULL)
                {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                }
                else
                {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    }else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    }else if($value['PRORROGA'] == '3'){
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    }else if($value['PRORROGA'] == '4'){
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if($value['REACTIVACION'] == NULL)
                {
                    $boton_titulo_reactivar = 'Reactivar';
                }
                else {

                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    }else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    }else if($value['REACTIVACION'] == '3')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }else if($value['REACTIVACION'] == '400')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }

                }

                //var_dump($value['PRORROGA']);

                if($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-')
                {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                </td>
html;
                }
                else
                {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ProrrogaPedir('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: $color_boton; color: $fuente " $des_prorroga><i class="fa fa-edit"></i> <b>$boton_titulo_prorroga</b>
                        </a>
                        <br>
                        <a type="button" class="btn btn-warning btn-circle" onclick="ReactivarSolicitud('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: #ffbcbc; color: #0D0A0A" ><i class="fa fa-repeat"></i> <b>$boton_titulo_reactivar</b>
                        </a>
                </td>
html;
                }
                if($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-')
                {
                    $ver_resumen = '';

                }else{
                    $ver_resumen = <<<html
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
                }
                if($value['RECOMENDADO'] != '' && $value['CICLO'] == '01')
                {
                    $recomendado = '<div><b>CAMPAÑA ACTIVA</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>RECOMIENDA MÁS Y PAGA MENOS <em></em></b><hr>';
                }
                else
                {
                    $recomendado = '';
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
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        <hr>
                         {$recomendado}
                        <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>
                        
                       
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                   
                    $ver_resumen
                    

                    </td>
                   
                     $botones_prorroga
                     
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::set('sucursal', $opciones_suc);
                View::render("historico_call_center_message_f");
            }
            else
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('sucursal', $opciones_suc);
                View::render("Historico_Call_Center");
            }
        }
        else {

            $Consulta = CallCenterDao::getAllSolicitudesHistorico($fechaActual, $fechaActual, $cdgco, $this->__usuario, $this->__perfil, $Sucursal);




            foreach ($Consulta as $key => $value) {

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
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                }else
                {
                    $titulo_boton = 'Pedir Prorroga';
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
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if($value['VOBO_REG'] == NULL)
                {
                    $vobo = '';
                }
                else{
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if($value['PRORROGA'] == NULL)
                {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                }
                else
                {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    }else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    }else if($value['PRORROGA'] == '3'){
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    }else if($value['PRORROGA'] == '4'){
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if($value['REACTIVACION'] == NULL)
                {
                    $boton_titulo_reactivar = 'Reactivar';
                }
                else {

                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    }else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    }else if($value['REACTIVACION'] == '3')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }else if($value['REACTIVACION'] == '400')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }
                }


                if($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-')
                {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                </td>
html;
                }
                else
                {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ProrrogaPedir('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: $color_boton; color: $fuente " $des_prorroga><i class="fa fa-edit"></i> <b>$boton_titulo_prorroga</b>
                        </a>
                        <br>
                        <a type="button" class="btn btn-warning btn-circle" onclick="ReactivarSolicitud('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: #ffbcbc; color: #0D0A0A" ><i class="fa fa-repeat"></i> <b>$boton_titulo_reactivar</b>
                        </a>
                </td>
html;
                }

                if($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-')
                {
                    $ver_resumen = '';

                }else{
                    $ver_resumen = <<<html
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
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
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        <hr>
                        <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>
                        
                    
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    
                    $ver_resumen
                    </td>
                   
                     $botones_prorroga
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::set('sucursal', $opciones_suc);
                View::render("historico_call_center_message_f");
            }
            else
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::set('sucursal', $opciones_suc);
                View::render("Historico_Call_Center");
            }

        }
    }

    public function HistoricoAnalistas()
    {
        $extraHeader = <<<html
        <title>Histórico de Llamadas Analistas</title>
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
                    [13, 50, 'Todos'],
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
            
        });
      
       fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            cdgco = getParameterByName('Suc');
            usuario = getParameterByName('Was');
            
             $("#export_excel_consulta_analistas").click(function(){
              $('#all').attr('action', '/CallCenter/HistorialGeneraAnalistas/?Inicial='+fecha1+'&Final='+fecha2+'&Suc='+cdgco);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
    
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Sucursal = $_GET['Suc'];

        $cdgco = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);

        foreach ($ComboSucursales as $key => $val2) {
            array_push($cdgco, $val2['CODIGO']);
        }


        if ($Inicial != '' || $Final != '') {
            /////////////////////////////////
            $Consulta = CallCenterDao::getAllSolicitudesHistorico($Inicial, $Final, '', $this->__usuario, $this->__perfil, $Sucursal);




            foreach ($Consulta as $key => $value) {

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
                else if($value['ESTATUS_CL'] == '-')
                {
                    $color = 'danger';
                    $icon = 'fa-close';
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
                else if($value['ESTATUS_AV'] == '-')
                {
                    $color_a = 'danger';
                    $icon_a = 'fa-close';
                }
                else
                {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO')
                {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                }else
                {
                    $titulo_boton = 'Pedir Prorroga';
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
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if($value['VOBO_REG'] == NULL)
                {
                    $vobo = '';
                }
                else{
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if($value['PRORROGA'] == NULL)
                {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                }
                else
                {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    }else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    }else if($value['PRORROGA'] == '3'){
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    }else if($value['PRORROGA'] == '4'){
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if($value['REACTIVACION'] == NULL)
                {
                    $boton_titulo_reactivar = 'Reactivar';
                }
                else {

                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    }else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    }else if($value['REACTIVACION'] == '3')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }else if($value['REACTIVACION'] == '400')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }

                }

                //var_dump($value['PRORROGA']);
                if($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-')
                {
                    $ver_resumen = '';

                }else{
                    $ver_resumen = <<<html
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
                }

                if($value['RECOMENDADO'] != '' && $value['CICLO'] == '01')
                {
                    $recomendado = '<div><b>CAMPAÑA ACTIVA</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>RECOMIENDA MÁS Y PAGA MENOS <em></em></b><hr>';
                }
                else
                {
                    $recomendado = '';
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
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        <hr>
                        {$recomendado}
                        <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>

                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                   
                    $ver_resumen

                    </td>
                   
                     
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::render("historico_analistas_message_f");
            }
            else
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::render("Historico_Analistas_Center");
            }
        }
        else {

            $Consulta = CallCenterDao::getAllSolicitudesHistorico($fechaActual, $fechaActual, '', $this->__usuario, $this->__perfil,$Sucursal);


            foreach ($Consulta as $key => $value) {

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
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                }else if($value['FIN_CL'] != '' || $value['FIN_AV'] != '')
                {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                }else
                {
                    $titulo_boton = 'Pedir Prorroga';
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
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if($value['VOBO_REG'] == NULL)
                {
                    $vobo = '';
                }
                else{
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if($value['PRORROGA'] == NULL)
                {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                }
                else
                {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    }else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    }else if($value['PRORROGA'] == '3'){
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    }else if($value['PRORROGA'] == '4'){
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if($value['REACTIVACION'] == NULL)
                {
                    $boton_titulo_reactivar = 'Reactivar';
                }
                else {

                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    }else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    }else if($value['REACTIVACION'] == '3')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }else if($value['REACTIVACION'] == '400')
                    {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }
                }

                if($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-')
                {
                    $ver_resumen = '';

                }else{
                    $ver_resumen = <<<html
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
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
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        <hr>
                        <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>
                        
                    
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    
                    $ver_resumen
                    </td>
                   
                </tr>
html;
            }
            if($Consulta[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::render("historico_analistas_message_f");
            }
            else
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::render("Historico_Analistas_Center");
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

        $encuesta->_nombre_aval_cl = MasterDom::getData('nombre_aval_cl');
        $encuesta->_id_aval_cl = MasterDom::getData('id_aval_cl');
        $encuesta->_telefono_aval_cl = MasterDom::getData('telefono_aval_cl');

        $encuesta->_llamada = MasterDom::getData('contenido');
        $encuesta->_completo = MasterDom::getData('completo');
        $encuesta->_cdgpe = $this->__usuario;


        $id = CallCenterDao::insertEncuestaCL($encuesta);
    }

    public function PagosAddEncuestaAV(){
        $encuesta = new \stdClass();
        $encuesta->_fecha_solicitud = MasterDom::getDataAll('fecha_solicitud_av');
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
        $encuesta->_comentarios_prorroga = MasterDom::getData('comentarios_prorroga');

        $id = CallCenterDao::UpdateResumen($encuesta);
    }

    public function ProrrogaUpdate(){
        $encuesta = new \stdClass();
        $encuesta->_prorroga = MasterDom::getData('prorroga');
        $encuesta->_id_call = MasterDom::getData('id_call');

        $id = CallCenterDao::UpdateProrroga($encuesta);
    }

    public function ReactivarSolicitudEjec(){
        $encuesta = new \stdClass();
        $encuesta->_id_call = MasterDom::getData('id_call');
        $encuesta->_opcion = MasterDom::getData('opcion');

        $id = CallCenterDao::ReactivarSolicitud($encuesta);
    }

    public function ReactivarSolicitudAdminPost(){
        $encuesta = new \stdClass();
        $encuesta->_id_call = MasterDom::getData('id_call');
        $encuesta->_opcion = MasterDom::getData('opcion');

        $id = CallCenterDao::ReactivarSolicitudAdmin($encuesta);
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

    public function HistorialGenera(){

        $cdgco_all = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        foreach ($ComboSucursales as $key => $val2) {
            array_push($cdgco_all, $val2['CODIGO']);
        }



        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];
        $Sucursal = $_GET['Suc'];


        if($fecha_fin == '' || $fecha_fin == '')
        {
            $fechaActual = date('Y-m-d');
            $fecha_inicio = $fechaActual;
            $fecha_fin = $fechaActual;
        }


        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reorte");
        $objPHPExcel->getProperties()->setDescription("Descripcion");
        $objPHPExcel->setActiveSheetIndex(0);



        $estilo_titulo = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>10, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_encabezado = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>10, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_celda = array(
            'font' => array('bold' => false,'name'=>'Calibri','size'=>9,'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID

        );





        $fila = 1;
        $adaptarTexto = true;

        $controlador = "CallCenter";
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N','O', 'P', 'Q', 'R', 'S', 'T', 'U','V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD');
        $nombreColumna = array('-','NOMBRE REGION','FECHA DE TRABAJO','SOLICITUD','INICIO','AGENCIA','EJECUTIVO','CLIENTE','NOMBRE DE CLIENTE','CICLO','TELEFONO CLIENTE','TIPO DE LLAMADA','¿Qué edad tiene?','¿Cuál es su fecha de nacimiento?','Me proporciona su domicilio completo por favor','¿Qué tiempo tiene viviendo en este domicilio?','Actualmente ¿cual es su principal fuente de ingresos?','¿Cuál es el nombre de su aval?','¿Que Relación tiene con su aval?','¿ Cual es la actividad económica de su aval?','Por favor me proporciona el número telefónico de su aval','¿Firmó su solicitud? ¿Cuando?','Me puede indicar ¿para qué utilizará su crédito?','¿Compartirá su crédito con alguna otra persona?','NOMBRE DEL AVAL','TELEFONO DE AVAL','TIPO DE LLAMADA','¿Qué edad tiene?','Me indica su fecha de nacimiento por favor','¿Cuál es su domicilio?','¿Qué tiempo lleva viviendo en este domicilio?','Actualmente  ¿cual es su principal fuente de ingresos?','¿Hace cuanto conoce a  “Nombre del cliente”?','¿Qué Relación tiene con “Nombre del cliente”?','¿Sabe a que se dedica el Sr. (nombre de cliente)?','Me puede proporcionar el numero telefónico de “cliente”','DIA/HORA DE LLAMADA 1 CL','DIA/HORA DE LLAMADA 2 CL','DIA/HORA DE LLAMADA 1 AV','DIA/HORA DE LLAMADA 1 AV','COMENTARIO INICIAL','COMENTARIO FINAL','ESTATUS','INCIDENCIA COMERCIAL - ADMINISTRACION','Vo Bo GERENTE REGIONAL','ANALISTA','SEMAFORO','FECHA DE DESEMBOLSO','$ ENTREGADA','$ PARCIALIDAD','MORA AL CORTE','#  SEMANAS CON ATRASO','MES','LLAMADA POSTVENTA','RECAPTURADA SI-NO','ANALISTA INICIAL');
        $nombreCampo = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N','O', 'P', 'Q', 'R', 'S', 'T', 'U','V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD',
            'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'ASS',
            'ATT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Solicitudes');
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$fila.':'.$columna[count($nombreColumna)-1].$fila);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->applyFromArray($estilo_titulo);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->getAlignment()->setWrapText($adaptarTexto);

        $objPHPExcel->getActiveSheet()->getStyle('A2:A2')->applyFromArray(
            array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFC7CE'))) );
        $objPHPExcel->getActiveSheet()->getStyle('B2:D2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C5D9F1'))));
        $objPHPExcel->getActiveSheet()->getStyle('E2:L2')->applyFromArray(
            array('fill' => array( 'type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '0070C0'))) );
        $objPHPExcel->getActiveSheet()->getStyle('M2:X2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C5D9F1'))));
        $objPHPExcel->getActiveSheet()->getStyle('Y2:AA2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '0070C0'))));


        $objPHPExcel->getActiveSheet()->getStyle('AB2:AJ2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C5D9F1'))));
        $objPHPExcel->getActiveSheet()->getStyle('AK2:AN2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '0070C0'))));
        $objPHPExcel->getActiveSheet()->getStyle('AO2:AU2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C00000'))));
        $objPHPExcel->getActiveSheet()->getStyle('AV2:BD2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFFCC'))));


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

        $Layoutt = CallCenterDao::getAllSolicitudesHistoricoExcel($fecha_inicio, $fecha_fin, $cdgco_all, $this->__usuario, $Sucursal);
        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
                $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            }
            $fila +=1;
        }




        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Llamadas Finalizadas '.$fecha_inicio. ' al '.$fecha_fin.'.xlsx"');
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

    public function HistorialGeneraAnalistas(){

        $cdgco_all = array();
        $analistas_all = array();

        $ComboSucursales = CallCenterDao::getComboSucursalesAllCDGCO();
        foreach ($ComboSucursales as $key => $val2) {
            array_push($cdgco_all, $val2['CODIGO']);
        }

        //$ComboAnalistas = CallCenterDao::getAllAnalistas();
        //foreach ($ComboAnalistas as $key => $val2) {
          //  array_push($analistas_all, $val2['USUARIO']);
        //}

        //$string_from_array = implode(', ', $analistas_all);

        //var_dump($string_from_array);




        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];

        if($fecha_fin == '' || $fecha_fin == '')
        {
            $fechaActual = date('Y-m-d');
            $fecha_inicio = $fechaActual;
            $fecha_fin = $fechaActual;
            $sucursal = '000';
        }
        else{
            $sucursal = '000';
        }


        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reorte");
        $objPHPExcel->getProperties()->setDescription("Descripcion");
        $objPHPExcel->setActiveSheetIndex(0);



        $estilo_titulo = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>10, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_encabezado = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>10, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_celda = array(
            'font' => array('bold' => false,'name'=>'Calibri','size'=>9,'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID

        );





        $fila = 1;
        $adaptarTexto = true;

        $controlador = "CallCenter";
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N','O', 'P', 'Q', 'R', 'S', 'T', 'U','V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD');
        $nombreColumna = array('-','NOMBRE REGION','FECHA DE TRABAJO','SOLICITUD','INICIO','AGENCIA','EJECUTIVO','CLIENTE','NOMBRE DE CLIENTE','CICLO','TELEFONO CLIENTE','TIPO DE LLAMADA','¿Qué edad tiene?','¿Cuál es su fecha de nacimiento?','Me proporciona su domicilio completo por favor','¿Qué tiempo tiene viviendo en este domicilio?','Actualmente ¿cual es su principal fuente de ingresos?','¿Cuál es el nombre de su aval?','¿Que Relación tiene con su aval?','¿ Cual es la actividad económica de su aval?','Por favor me proporciona el número telefónico de su aval','¿Firmó su solicitud? ¿Cuando?','Me puede indicar ¿para qué utilizará su crédito?','¿Compartirá su crédito con alguna otra persona?','NOMBRE DEL AVAL','TELEFONO DE AVAL','TIPO DE LLAMADA','¿Qué edad tiene?','Me indica su fecha de nacimiento por favor','¿Cuál es su domicilio?','¿Qué tiempo lleva viviendo en este domicilio?','Actualmente  ¿cual es su principal fuente de ingresos?','¿Hace cuanto conoce a  “Nombre del cliente”?','¿Qué Relación tiene con “Nombre del cliente”?','¿Sabe a que se dedica el Sr. (nombre de cliente)?','Me puede proporcionar el numero telefónico de “cliente”','DIA/HORA DE LLAMADA 1 CL','DIA/HORA DE LLAMADA 2 CL','DIA/HORA DE LLAMADA 1 AV','DIA/HORA DE LLAMADA 1 AV','COMENTARIO INICIAL','COMENTARIO FINAL','ESTATUS','INCIDENCIA COMERCIAL - ADMINISTRACION','Vo Bo GERENTE REGIONAL','ANALISTA','SEMAFORO','FECHA DE DESEMBOLSO','$ ENTREGADA','$ PARCIALIDAD','MORA AL CORTE','#  SEMANAS CON ATRASO','MES','LLAMADA POSTVENTA','RECAPTURADA SI-NO','ANALISTA INICIAL');
        $nombreCampo = array('A','B','C','D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N','O', 'P', 'Q', 'R', 'S', 'T', 'U','V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD',
            'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'ASS',
            'ATT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD'
        );


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Solicitudes');
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$fila.':'.$columna[count($nombreColumna)-1].$fila);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->applyFromArray($estilo_titulo);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->getAlignment()->setWrapText($adaptarTexto);

        $objPHPExcel->getActiveSheet()->getStyle('A2:A2')->applyFromArray(
            array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFC7CE'))) );
        $objPHPExcel->getActiveSheet()->getStyle('B2:D2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C5D9F1'))));
        $objPHPExcel->getActiveSheet()->getStyle('E2:L2')->applyFromArray(
            array('fill' => array( 'type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '0070C0'))) );
        $objPHPExcel->getActiveSheet()->getStyle('M2:X2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C5D9F1'))));
        $objPHPExcel->getActiveSheet()->getStyle('Y2:AA2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '0070C0'))));


        $objPHPExcel->getActiveSheet()->getStyle('AB2:AJ2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C5D9F1'))));
        $objPHPExcel->getActiveSheet()->getStyle('AK2:AN2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '0070C0'))));
        $objPHPExcel->getActiveSheet()->getStyle('AO2:AU2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C00000'))));
        $objPHPExcel->getActiveSheet()->getStyle('AV2:BD2')->applyFromArray(
            array( 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFFCC'))));


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


        $Layoutt = CallCenterDao::getAllSolicitudesHistoricoExcel($fecha_inicio, $fecha_fin, $cdgco_all, '', $sucursal);
        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
                $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            }
            $fila +=1;
        }


        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Llamadas Finalizadas '.$fecha_inicio. ' al '.$fecha_fin.'.xlsx"');
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
