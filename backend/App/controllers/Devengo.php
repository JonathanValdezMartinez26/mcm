<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Devengo AS DevengoDao;

class Devengo extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function getUsuario(){
      return $this->__usuario;
    }

    public function index()
    {
        $extraHeader = <<<html
        <title>Devengar Crédito</title>
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

        if($credito != '' || $ciclo != '')
        {
            $Administracion = DevengoDao::ConsultaExiste($credito, $ciclo);
            if($Administracion['CDGCLNS'] != '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('Administracion', $Administracion);
                View::set('credito',$credito);
                View::set('ciclo',$ciclo);
                View::render("devengo_busqueda_all");

            }
            else
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('credito',$credito);
                View::render("controlgarantias_busqueda_message");
            }

        }
        else
        {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer',$this->_contenedor->footer($extraFooter));
            View::render("devengo_all");
        }

    }

}
