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

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo);

        if ($credito != '' && $ciclo != '') {


            if($AdministracionOne[0] == '')
            {
                View::set('Administracion', $AdministracionOne);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::render("callcenter_cliente_message_all");
            }
            else
            {

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::render("callcenter_cliente_all");
            }
        } else {

            $Solicitudes = CallCenterDao::getAllSolicitudes();

            foreach ($Solicitudes as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important;"><span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important;">Pendiente</span></td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}" class="btn btn-success btn-circle" style="background: #029f3f"><i class="fa fa-edit"></i> Iniciar Validación</a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::render("callcenter_pendientes_all");

        }
    }

    public function Concentrado()
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


        $this->__usuario;

        $ListaEjecutivo = CallCenterDao::getAllDescription($credito, $ciclo);

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));


        View::set('Administracion', $ListaEjecutivo);
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
         
         function enviar_add(){	
             
                   
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
    
      </script>
html;

        $Analistas = CallCenterDao::getAllAnalistas();
        $getAnalistas = '';
        $opciones = '';

        foreach ($Analistas as $key => $val2) {

            $opciones .= <<<html
                <option value="{$val2['CDGCO']}">({$val2['USUARIO']}) {$val2['NOMBRE']}</option>
html;
        }

        $getAnalistas = <<<html
         <div class="col-md-12">
                <div class="form-group">
                     <label for="ejecutivo">Ejecutivo *</label>
                     <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                        {$opciones}
                     </select>
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
            View::set('tabla', $tabla);
            View::render("asignar_sucursales_analistas");


    }

    public function Historico()
    {

    }


}
