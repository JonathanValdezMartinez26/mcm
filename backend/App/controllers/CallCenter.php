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

        if ($credito != '' && $ciclo != '') {
            $Administracion = CallCenterDao::getAllDescription($credito, $ciclo);

            if($Administracion[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::render("callcenter_cliente_message_all");
            }
            else
            {
                View::set('Administracion', $Administracion);
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("callcenter_cliente_all");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('credito', $credito);
            View::set('ciclo', $ciclo);
            View::render("callcenter_all");

        }
    }

    public function Pendientes()
    {

    }

    public function Historico()
    {

    }


}
