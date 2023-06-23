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
        $tabla = '';

        if ($credito != '' && $ciclo != '') {
            $Administracion = CallCenterDao::getAllDescription($credito, $ciclo);
            foreach ($Administracion as $key => $value) {
                $tabla .= <<<html
                <div class="Contenedor1_resultados__XMhF5">
                <div class="TarjetaCallCenter_tarjeta__3sZn0">
                <span class="TarjetaCallCenter_titulo__V-jAl">Datos del Credito</span>
                <table class="TarjetaCallCenter_tablaDatos__k8d8R">
                    <tbody>
                        <tr>
                            <td>
                                <strong>Monto</strong>
                            </td>
                            <td>
                                <strong>Plazo</strong></td>
                            <td>
                                <strong>Parcialidad</strong>
                            </td>
                            <td>
                                <strong>Día de Pago</strong>
                            </td>
                        </tr>
                        <tr>
                         <td>{$value['MONTO']}</td>
                         <td>{$value['PLAZO']}</td>
                         <td>{$value['PARCIALIDAD']}</td>
                         <td>{$value['DIA_PAGO']}</td>
                        </tr>
                    </tbody>
                </table>
                <span class="TarjetaCallCenter_titulo__V-jAl">Datos del Cliente</span>
                <table class="TarjetaCallCenter_tablaDatos__k8d8R">
                <tbody>
                <tr>
                    <td rowspan="4">
                        <strong>Identificación</strong>
                    </td>
                    <td>    
                        <strong>Nombre</strong>
                    </td>
                    <td>
                        <strong>Fecha de Nacimiento</strong>
                    </td>
                    <td>
                        <strong>Edad</strong>
                    </td>
                    <td>
                        <strong>Sexo</strong>
                    </td>
                    <td>
                        <strong>Edo. Civil</strong>
                    </td>
                    <td>
                        <strong>Telefono</strong>
                    </td>
                </tr>
                <tr>
                    <td>MA MARGARITA VICTORIA SAMANIEGO RICARDO</td>
                    <td>22/12/1962</td>
                    <td>60</td>
                    <td>F</td>
                    <td>VIUDO</td>
                    <td>2212176765</td>
                </tr>
                <tr><td colspan="6">
                <strong>Actividad Economica</strong>
                </td></tr><tr><td colspan="6">BAZAR</td></tr>
                </tbody>
                </table>
                <table class="TarjetaCallCenter_tablaDatos__k8d8R"><tbody>
                <tr><td rowspan="2"><strong>Domicilio</strong></td>
                <td>
                <strong>Calle</strong>
                </td><td><strong>Colonia</strong>
                </td><td><strong>Localidad</strong></td><td>
                <strong>Municipio</strong></td><td><strong>Estado</strong>
                </td><td><strong>CP</strong></td></tr><tr><td>SAN JOAQUIN 16</td><td>SN JUAN CUAUTLANCIN</td><td>Cuautlancingo</td><td>Cuautlancingo</td><td>PUEBLA</td><td>72764</td></tr></tbody></table><span class="TarjetaCallCenter_titulo__V-jAl">Datos del Aval</span><table class="TarjetaCallCenter_tablaDatos__k8d8R"><tbody><tr><td rowspan="4"><strong>Identificación</strong></td><td><strong>Nombre</strong></td><td><strong>Fecha de Nacimiento</strong></td><td><strong>Edad</strong></td><td><strong>Sexo</strong></td><td><strong>Edo. Civil</strong></td><td><strong>Telefono</strong></td></tr><tr><td>MA ALEJANDRA MORALES FLORES</td><td>07/06/1968</td><td>54</td><td>F</td><td>CASADO</td><td>2213680143</td></tr><tr><td colspan="6"><strong>Actividad Economica</strong></td></tr><tr><td colspan="6">VENTA DE COMIDA</td></tr></tbody></table><table class="TarjetaCallCenter_tablaDatos__k8d8R"><tbody><tr><td rowspan="2"><strong>Domicilio</strong></td><td><strong>Calle</strong></td><td><strong>Colonia</strong></td><td><strong>Localidad</strong></td><td><strong>Municipio</strong></td><td><strong>Estado</strong></td><td><strong>CP</strong></td></tr><tr><td>SAN JOAQUIN 8 1 VACIO</td>
                <td>SN JUAN CUAUTLANCIN</td><td>Cuautlancingo</td><td>Cuautlancingo</td><td>PUEBLA</td><td>72764</td></tr></tbody></table>
                </div></div>
html;
            }
            View::set('tabla', $tabla);
            View::set('Administracion', $AdministracionOne);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("callcenter_cliente_all");

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("callcenter_all");

        }
    }

    public function Pendientes()
    {
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

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];


        if(empty($fecha_inicio) || empty($fecha_fin))
        {
            View::render("pagos_layout_all");
        }
        else
        {
            ///////////////////////////////////////////////////////////////////////////////////
            $tabla = '';

            $Layout = PagosDao::GeneraLayoutContable($fecha_inicio, $fecha_fin);

            if ($Layout != '') {
                foreach ($Layout as $key => $value) {

                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['REFERENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['MONTO']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONEDA']}</td>
                </tr>
html;
                }
                if($Layout[0] == '')
                {
                    View::render("pagos_layout_busqueda_message");
                }
                else
                {
                    View::set('tabla', $tabla);
                    View::set('footer', $this->_contenedor->footer($extraFooter));
                    View::render("pagos_layout_busqueda");
                }

            } else {
                View::render("pagos_layout_all");
            }

            ////////////////////////////////////////////////////
        }


    }

    public function Historico()
    {
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

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];


        if(empty($fecha_inicio) || empty($fecha_fin))
        {
            View::render("pagos_layout_all");
        }
        else
        {
            ///////////////////////////////////////////////////////////////////////////////////
            $tabla = '';

            $Layout = PagosDao::GeneraLayoutContable($fecha_inicio, $fecha_fin);

            if ($Layout != '') {
                foreach ($Layout as $key => $value) {

                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['REFERENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['MONTO']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONEDA']}</td>
                </tr>
html;
                }
                if($Layout[0] == '')
                {
                    View::render("pagos_layout_busqueda_message");
                }
                else
                {
                    View::set('tabla', $tabla);
                    View::set('footer', $this->_contenedor->footer($extraFooter));
                    View::render("pagos_layout_busqueda");
                }

            } else {
                View::render("pagos_layout_all");
            }

            ////////////////////////////////////////////////////
        }


    }


}
