<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Promociones AS PromocionesDao;

class Promociones extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());

    }


    public function Telarana()
    {
        $extraHeader = <<<html
        <title>Promociones - Telarana </title>
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
            
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Cultiva/generarExcel/?Inicial='+fecha1 + '&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;


        $Credito = $_GET['Credito'];

        if ($Credito != '') {

            $Recomienda = PromocionesDao::ConsultarDatosClienteRecomienda($Credito);


            $datetime1 = new \DateTime($Recomienda['INICIO']);

            $fechaActual = date("Y-m-d");
            $datetime2 = new \DateTime($fechaActual);

            $interval = $datetime1->diff($datetime2);
            $semanas = floor(($interval->format('%a') / 7)) . ' semanas';

            if($semanas >= 9)
            {
                $promocion_estatus =  <<<html
                    <div class="col-md-12 col-sm-12  tile_stats_count">
                            <span class="count_top" style="font-size: 19px"><i><i class="fa fa-calendar"></i></i> Estatus: DISPONIBLE</span>
                           <br>
                           <br>
                           <br>
                           <div class="col-md-12 col-sm-12 ">
                                    <button style="background: #109d0e !important; border-radius: 25px;" type="submit" name="agregar" class="btn btn-success btn-lg" value="enviar" onclick="FunprecesarPagos()"><span class="fa fa-check"></span> Calcular Descuento</button>
                           </div>
                           </br>
                    </div>;
html;
            }
            else if($Recomienda['DIAS_ATRASO'] >= 7)
            {
                $promocion_estatus =  <<<html
                    <div class="col-md-12 col-sm-12  tile_stats_count">
                            <span class="count_top" style="font-size: 19px"><i><i class="fa fa-calendar"></i></i> Estatus: NO</span>
                           <br>
                           <br>
                           <br>
                           <div class="col-md-12 col-sm-12 ">
                                    <button style="background: #109d0e !important; border-radius: 25px;" type="submit" name="agregar" class="btn btn-success btn-lg" value="enviar" onclick="FunprecesarPagos()"><span class="fa fa-check"></span> Calcular Descuento</button>
                           </div>
                           </br>
                    </div>;
html;
            }
            else
            {
                $promocion_estatus =  <<<html
                    <div class="col-md-12 col-sm-12  tile_stats_count">
                            <span class="count_top" style="font-size: 19px"><i><i class="fa fa-clock-o"></i></i> Estatus: NO APLICA POR PLAZO</span>
                            <div class="count" style="font-size: 16px"> Espere a la semana 10. Para continuar.</div>
                            
                            <td style="padding: 10px !important; text-align: left; width:165px !important;">
                                <div style="padding-bottom: 5px"><span class="label label-success" ><span class="fa fa-check"></span></span> Cliente sin atrasos</div>
                                <div style="padding-bottom: 5px"><span class="label label-success"><span class="fa fa-check"></span></span> Invitados sin atrasos</div>
                                <div style="padding-bottom: 5px"><span class="label label-success"><span style="font-size: 16px;" class="fa fa-close"></span></span> Estatus Final Solicitud</div>
                            </td>
                    </div>;
html;
            }



            if($Recomienda != NULL)
            {
                $Consulta = PromocionesDao::ConsultarClientesInvitados($Credito);
                foreach ($Consulta as $key => $value) {

                    if($value['ESTATUS_PAGADO'] == NULL)
                    {
                        $estatus_p = 'PENDIENTE';
                    }else
                    {
                        $estatus_p = '';
                    }


                    $tabla_clientes .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CL_INVITADO']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">$ {$value['CANTIDAD_ENTREGADA']}</td>
                    <td style="padding: 0px !important;">$ {$value['DESCUENTO']}</td>
                   
                    <td style="padding: 0px !important;">CICLO {$value['CICLO_INVITACION']} </td>
                    <td style="padding: 0px !important;"> {$value['DIAS_ATRASO']}</td>
                    <td style="padding: 0px !important;"> {$estatus_p} </td>
                    <td style="padding: 0px !important;"> NO </td>
                </tr>
html;

                }

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla_clientes', $tabla_clientes);
                View::set('Recomienda', $Recomienda);
                View::set('Semanas', $semanas);
                View::set('Promocion_estatus', $promocion_estatus);
                View::render("promociones_telarana_busqueda_all");


            }
            else
            {
                echo "El cliente no aplica para un descuento, ya que actualmente no tiene un credito activo";
            }

        }
        else
        {
            var_dump("Holaaa");
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("promociones_telarana_busqueda");
        }








    }
}