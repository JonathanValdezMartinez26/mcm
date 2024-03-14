<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\CajaAhorro as CajaAhorroDao;

class CajaAhorro extends Controller
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
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
            function getParameterByName(name) {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                    results = regex.exec(location.search)
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
            }
        
            $(document).ready(function () {
                $("#muestra-cupones").tablesorter()
                var oTable = $("#muestra-cupones").DataTable({
                    lengthMenu: [
                        [30, 50, -1],
                        [30, 50, "Todos"]
                    ],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: 0
                        }
                    ],
                    order: false
                })
                // Remove accented character from search input as well
                $("#muestra-cupones input[type=search]").keyup(function () {
                    var table = $("#example").DataTable()
                    table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw()
                })
                var checkAll = 0
            })
        
        </script>
html;

        $cliente = $_GET['Cliente'];

        $BuscaCliente = CajaAhorroDao::ConsultaClientesProducto($cliente);


        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));

        if ($cliente == '') return View::render("ahorro_caja_inicio");
        if ($BuscaCliente == '') return View::render("ahorro_caja_inicio");

        View::set('Cliente', $BuscaCliente);
        View::render("ahorro_caja_principal");
    }



    public function Imprime_Contrato($numero_contrato)
    {
        $mpdf = new \mPDF('c');
        $mpdf->defaultPageNumStyle = 'I';
        $mpdf->h2toc = array('H5' => 0, 'H6' => 1);

        $style = <<<html
      <style>

       .titulo{
          width:100%;
          margin-top: 30px;
          color: #b92020;
          margin-left:auto;
          margin-right:auto;
        }

        body {
          padding: 50px;
        }

        * {
          box-sizing: border-box;
        }

        .receipt-main {
          display: inline-block;
          width: 100%;
          padding: 15px;
          font-size: 12px;
          border: 1px solid #000;
        }

        .receipt-title {
          text-align: center;
          text-transform: uppercase;
          font-size: 20px;
          font-weight: 600;
          margin: 0;
        }

        .receipt-label {
          font-weight: 600;
        }

        .text-large {
          font-size: 16px;
        }

        .receipt-section {
          margin-top: 10px;
        }

        .receipt-footer {
          text-align: center;
          background: #ff0000;
        }

        .receipt-signature {
          height: 80px;
          margin: 50px 0;
          padding: 0 50px;
          background: #fff;

          .receipt-line {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
          }

          p {
            text-align: justify;
            margin: 0;
            font-size: 17px;
          }
        }
      </style>
html;
        ///$complemento = PagosDao::getByIdReporte($barcode);


        $tabla = <<<html

        <div class="receipt-main">
         <table class="table">
             <tr>
                 <th style="width: 600px;" class="text-right">
                    <p class="receipt-title"><b>Recibo de Pago</b></p>
                 </th>
                 <th style="width: 10px;" class="text-right">
                    <img src="img/logo.png" alt="Esta es una descripcion alternativa de la imagen para cuando no se pueda mostrar" width="60" height="50" align="left"/>
                 </th>
             </tr>
        </table>
         
          <div class="receipt-section pull-left">
            <span class="receipt-label text-large">#FOLIO:</span>
            <span class="text-large"><b></b></span>
          </div>
          
           <div class="receipt-section pull-left">
            <span class="receipt-label text-large">FECHA DE COBRO:</span>
            <span class="text-large"></span>
          </div>
          
          
          <div class="clearfix"></div>
          
         
          
          <hr>
          
        
       <div class="table-responsive-sm">
          <table class="table">
              <thead>
                 <tr>
                     <th># Crédito</th>
                     <th>Nombre del Cliente</th>
                     <th>Ciclo</th>
                     <th width="19%" class="text-right">Tipo</th>
                     <th class="text-right">Monto</th>
                 </tr>
              </thead>
                  <tbody>
                     
html;


        $fechaActual = date('Y-m-d H:i:s');


        $mpdf->WriteHTML($style, 1);
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:opensans;">Este recibo de pago se genero el día ' . $fechaActual . '<br>{PAGENO}</div>');
        print_r($mpdf->Output());
        exit;
    }

}
