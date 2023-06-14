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

    public function index() {
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
            $("#checkAll").click(function () {
              if(checkAll==0){
                $("input:checkbox").prop('checked', true);
                checkAll = 1;
              }else{
                $("input:checkbox").prop('checked', false);
                checkAll = 0;
              }
            });
            $("#export_pdf").click(function(){
              $('#all').attr('action', '/Economicos/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
            $("#export_excel").click(function(){
              $('#all').attr('action', '/Economicos/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
       
        });
      </script>
html;

        $credito = $_POST['credito'];
        $ciclo = $_POST['ciclo'];
        $tabla = '';

        if ($credito != '' && $ciclo != '') {
            $CallCenter = CallCenterDao::getAllDescription($credito, $ciclo);

            $tabla .= <<<html
                <hr>
                <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                <span class="badge badge-secondary"><h5>Datos del Credito</h5></span>
                    <table  class="table table-striped table-bordered table-hover">
                    <tbody>
                        <tr><td><strong>Monto</strong></td><td><strong>Plazo</strong></td><td><strong>Parcialidad</strong></td><td><strong>Día de Pago</strong></td>
                        </tr><tr><td>$ {$CallCenter[0]['MONTO']}</td><td>{$CallCenter[0]['PLAZO']}</td><td>{$CallCenter[0]['PARCIALIDAD']}</td><td>{$CallCenter[0]['DIA_PAGO']}</td></tr>
                    </tbody>
                </table>
                    <span class="TarjetaCallCenter_titulo__V-jAl">Datos del Cliente</span>
                    <table class="table table-striped table-bordered table-hover"><tbody><tr><td rowspan="4"><strong>Identificación</strong></td><td><strong>Nombre</strong></td><td><strong>Fecha de Nacimiento</strong></td><td><strong>Edad</strong></td><td><strong>Sexo</strong></td><td><strong>Edo. Civil</strong></td><td><strong>Telefono</strong></td>
                    </tr><tr><td><b>{$CallCenter[1]['NOMBRE']}</b></td><td>{$CallCenter[1]['NACIMIENTO']}</td><td>{$CallCenter[1]['EDAD']}</td><td>{$CallCenter[1]['SEXO']}</td><td>{$CallCenter[1]['EDO_CIVIL']}</td><td>{$CallCenter[1]['TELEFONO']}</td>
                    </tr><tr><td colspan="6"><strong>Actividad Economica</strong></td></tr><tr><td colspan="6">{$CallCenter[1]['NACIMIENTO']}</td></tr>
                    </tbody></table><table class="table table-striped table-bordered table-hover"><tbody>
                    <tr><td rowspan="2"><strong>Domicilio</strong></td><td><strong>Calle</strong></td><td><strong>Colonia</strong></td>
                    <td><strong>Localidad</strong></td><td><strong>Municipio</strong></td><td><strong>Estado</strong></td><td><strong>CP</strong></td></tr>
                    <tr><td>{$CallCenter[1]['CALLE']}</td><td>{$CallCenter[1]['COLONIA']}</td><td>{$CallCenter[1]['LOCALIDAD']}</td><td>{$CallCenter[1]['MUNICIPIO']}</td>
                    <td>{$CallCenter[1]['ESTADO']}</td><td>{$CallCenter[1]['CP']}</td></tr></tbody></table>
                
                <span class="badge badge-secondary"><h5>Datos del Aval</h5></span>
                
                <table class="table table-striped table-bordered table-hover"><tbody><tr><td rowspan="4"><strong>Identificación</strong></td><td><strong>Nombre</strong></td><td><strong>Fecha de Nacimiento</strong></td><td><strong>Edad</strong></td><td><strong>Sexo</strong></td><td><strong>Edo. Civil</strong></td><td><strong>Telefono</strong></td></tr><tr><td><b>MA ALEJANDRA MORALES FLORES</b></td><td>07/06/1968</td><td>54</td><td>F</td><td>CASADO</td><td>2213680143</td></tr><tr><td colspan="6"><strong>Actividad Economica</strong></td></tr><tr><td colspan="6">VENTA DE COMIDA</td></tr></tbody></table>
                
                <table class="table table-striped table-bordered table-hover"><tbody><tr><td rowspan="2"><strong>Domicilio</strong></td><td><strong>Calle</strong></td><td><strong>Colonia</strong></td><td><strong>Localidad</strong></td><td><strong>Municipio</strong></td><td><strong>Estado</strong></td><td><strong>CP</strong></td></tr><tr><td>SAN JOAQUIN 8 1 VACIO</td>
                <td>SN JUAN CUAUTLANCIN</td><td>Cuautlancingo</td><td>Cuautlancingo</td><td>PUEBLA</td><td>72764</td></tr></tbody></table>
               </div></div>
html;

            View::set('tabla', $tabla);
            View::set('Administracion', $AdministracionOne);
            View::set('credito', $credito);
            View::set('ciclo', $ciclo);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("callcenter_cliente_all");

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("callcenter_all");

        }
    }


}
