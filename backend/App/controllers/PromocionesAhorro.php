<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Ahorro as AhorroDao;
use Mpdf\mPDF;

class PromocionesAhorro extends Controller
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
    <title>Apertura de cuentas </title>
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
    
        function boton_contrato(numero_contrato) {
          const host = window.location.origin
          
          let plantilla = "<!DOCTYPE html>"
          plantilla += '<html lang="es">'
          plantilla += '<head>'
          plantilla += '<meta charset="UTF-8">'
          plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
          plantilla += '<link rel="shortcut icon" href="' + host + '/img/logo.png">'
          plantilla += '<title>Contrato ' + numero_contrato + '</title>'
          plantilla += '</head>'
          plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
          plantilla +=
              '<iframe src="' + host + '/Ahorro/Imprime_Contrato/' +
              numero_contrato +
              '/" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
          plantilla += "</body>"
          plantilla += "</html>"
      
          const blob = new Blob([plantilla], { type: "text/html" })
          const url = URL.createObjectURL(blob)
          window.open(url, "_blank")
        }
    
        const showError = (mensaje) => swal(mensaje, { icon: "error" })
        const showSuccess = (mensaje) => swal({ text: mensaje, icon: "success" })
        
        const boton_genera_contrato = async (e, cliente) => {
            e.preventDefault()
            try {
                const continuar = await swal({
                    title:
                        "¿Está seguro de continuar con la apertura de la cuenta de ahorro del cliente: " +
                        cliente +
                        "?",
                    text: "",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                })
        
                if (continuar) {
                    const noCredito = document.querySelector("#cdgns").value
                    const datos = $("#registroInicialAhorro").serializeArray()
                    datos.push({ name: "credito", value: noCredito })
        
                    const respuesta = await $.ajax({
                        type: "POST",
                        url: "/Ahorro/AgregaContrato/",
                        data: $.param(datos)
                    })
                    
                    if (respuesta == "")
                        return showError(
                            "No pudimos generar el contrato, reintenta o contacta a tu Analista Soporte."
                        )
                    
                    const contrato = JSON.parse(respuesta)
                    const notOK = await showSuccess("Se ha generado el contrato: " + contrato.contrato)
                    
                    document.querySelector("#contrato").value = contrato.contrato
                    document.querySelector("#codigo_cl").value = noCredito
                    $("#modal_agregar_pago").modal("show")
                    
                    boton_contrato(contrato.contrato)
                }
            } catch (error) {
                console.error(error)
            }
            return false
        }
    </script>
html;

    $cliente = $_GET['Cliente'];
    $BuscaCliente = AhorroDao::ConsultaClientes($cliente);


    View::set('header', $this->_contenedor->header($extraHeader));
    View::set('footer', $this->_contenedor->footer($extraFooter));
    View::set('Cliente', $BuscaCliente);
    View::render("inversiones_menu");

  }

  public function AgregaContrato()
  {
    $contrato = AhorroDao::AgregaContratoAhorro($_POST);
    echo $contrato;
  }

  public function Imprime_Contrato($numero_contrato)
  {
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
                    <img src="img/logo.png" alt="Esta es una descripción alternativa de la imagen para cuando no se pueda mostrar" width="60" height="50" align="left"/>
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


    $nombreArchivo = "Contrato " . $numero_contrato;
    $mpdf = new \mPDF('c');
    $mpdf->defaultPageNumStyle = 'I';
    $mpdf->h2toc = array('H5' => 0, 'H6' => 1);
    $mpdf->SetTitle($nombreArchivo);
    $mpdf->WriteHTML($style, 1);
    $mpdf->WriteHTML($tabla, 2);
    $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:opensans;">Este recibo de pago se genero el día ' . date('Y-m-d H:i:s') . '<br>{PAGENO}</div>');

    $mpdf->Output($nombreArchivo . '.pdf', 'I');

    exit;
  }

  ///////////////////////////////////////////////////////////
  public function CuentasClientes()
  {
  }
}
