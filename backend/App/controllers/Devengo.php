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
       
        function BotonReactivar(fecha, cdgns, ciclo, inicio, dev_diario, dias_dev, int_dev, dev_diario_sin_iva, iva_int, plazo, plazo_dias, fin) {
              swal({
              title: "¿Segúro que desea reactivar el credito: "+ cdgns + ", ciclo: " + ciclo +"?",
              text: "",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                    $.ajax({
                        type: "POST",
                        url: "/Devengo/Calcular/",
                        data: {"fecha" : fecha, "cdgns" : cdgns, "ciclo" : ciclo, "inicio" : inicio, "dev_diario" : dev_diario, "dias_dev" : dias_dev, "int_dev" : int_dev, "dev_diario_sin_iva" : dev_diario_sin_iva, "iva_int" : iva_int, "plazo" : plazo, "plazo_dias" : plazo_dias, "fin" : fin},
                        success: function(respuesta){
                            console.log(respuesta);
                            //alert(respuesta);
                            //if(respuesta != '0')
                            //{
                              //    swal("Registro fue eliminado correctamente", {
                               //       icon: "success",
                                 //   });
                                  //  location.reload();
    
                            //}
                            //else
                            //{
                             //   swal(respuesta, {
                              //      icon: "error",
                                //});
                            //}
                        }
                    });
              }
            });
    
        }
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];

        if($credito != '' || $ciclo != '')
        {
            $Administracion = DevengoDao::ConsultaExiste($credito, $ciclo);

            if($Administracion[0]['CDGCLNS'] != '')
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
                View::set('ciclo',$ciclo);
                View::render("devengo_all");
            }

        }
        else
        {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer',$this->_contenedor->footer($extraFooter));
            View::render("devengo_all");
        }

    }

    public function Calcular(){

        $fecha = $_POST['fecha'];
        $cdgns = $_POST['cdgns'];
        $ciclo = $_POST['ciclo'];
        $inicio = $_POST['inicio'];
        $dev_diario = $_POST['dev_diario'];
        $dias_dev = $_POST['dias_dev'];
        $int_dev = $_POST['int_dev'];
        $dev_diario_sin_iva = $_POST['dev_diario_sin_iva'];
        $iva_int = $_POST['iva_int'];
        $plazo = $_POST['plazo'];
        $plazo_dias = $_POST['plazo_dias'];
        $fin = $_POST['fin'];

        $query = "
        INSERT INTO DEVENGO_DIARIO
        (FECHA_CALC, CDGEM, CDGCLNS, CICLO, INICIO, DEV_DIARIO, DIAS_DEV, INT_DEV, CDGPE, FREGISTRO, DEV_DIARIO_SIN_IVA, IVA_INT, PLAZO, PERIODICIDAD, PLAZO_DIAS, FIN_DEVENGO, ESTATUS, CLNS)
        VALUES(TIMESTAMP '$fecha 00:00:00.000000', 'EMPFIN', '$cdgns', '$ciclo', TIMESTAMP '$inicio 00:00:00.000000', $dev_diario, $dias_dev, $int_dev, 'AMGM', TIMESTAMP '$fecha 00:00:00.000000', $dev_diario_sin_iva, $iva_int, $plazo,'S', $plazo_dias
        , TIMESTAMP '$fin 00:00:00.000000', 'RE', 'G')";
        //$devengo = DevengoDao::ProcedureGarantiasDelete($id, $secuencia);

        var_dump($query);

        //return $query;

    }

}
