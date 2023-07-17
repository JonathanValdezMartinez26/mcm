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

    public function Consultar()
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
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::set('Administracion', $AdministracionOne);
                View::render("callcenter_cliente_all");
            }
        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('Administracion', $AdministracionOne);
            View::set('credito', $credito);
            View::set('ciclo', $ciclo);
            View::render("callcenter_all");

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

    public function Pendientes()
    {
        $extraHeader = <<<html
        <title>Clientes Pendientes de Validar</title>
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
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::set('Administracion', $AdministracionOne);
                View::render("callcenter_cliente_all");
            }
        } else {

            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('Administracion', $AdministracionOne);
            View::set('credito', $credito);
            View::set('ciclo', $ciclo);
            View::render("callcenter_all");

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
