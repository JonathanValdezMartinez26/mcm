<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Operaciones AS OperacionesDao;

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


    public function index()
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


        $fechaActual = date('Y-m-d');
        $Fecha = $_GET['Inicial'];
        $FechaFinal = $_GET['Final'];

        if ($Fecha != '') {
            $Consulta = OperacionesDao::ConsultaGruposCultiva($Fecha, $FechaFinal);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">{$value['CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['DOMICILIO']}</td>
                    <td style="padding: 0px !important;">{$value['SOLICITUD']}</td>
                </tr>
html;
                View::set('Inicial', $Fecha);
                View::set('Final', $FechaFinal);

            }

        } else {
            $Consulta = OperacionesDao::ConsultaGruposCultiva($fechaActual, $fechaActual);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                   <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                   <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">{$value['CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['DOMICILIO']}</td>
                    <td style="padding: 0px !important;">{$value['SOLICITUD']}</td>
                </tr>
html;
            }
            View::set('Inicial', date("Y-m-d"));
            View::set('Final', date("Y-m-d"));
        }
        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("zz_cultiva_consulta_clientes");


    }
}
