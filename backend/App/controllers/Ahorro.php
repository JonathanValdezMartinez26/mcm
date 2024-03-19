<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\CajaAhorro as CajaAhorroDao;

class Ahorro extends Controller
{
  private $_contenedor;

  function __construct()
  {
    parent::__construct();
    $this->_contenedor = new Contenedor;
    View::set('header', $this->_contenedor->header());
    View::set('footer', $this->_contenedor->footer());
  }

    ///////////////////////////////////////////////////
    public function CuentaCorriente()
  {
    $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

    $extraFooter = <<<html
    <script>
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
              '<iframe src="' + host + '/Apertura/ImprimeContrato/' +
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
                        url: "/Apertura/AgregaContrato/",
                        data: $.param(datos)
                    })
                    
                    if (respuesta == "")
                        return showError(
                            "No pudimos generar el contrato, reintenta o contacta a tu Analista Soporte."
                        )
                    
                    const contrato = JSON.parse(respuesta)
                    await showSuccess("Se ha generado el contrato: " + contrato.contrato)
                    
                    document.querySelector("#contrato").value = contrato.contrato
                    document.querySelector("#codigo_cl").value = noCredito
                    boton_contrato(contrato.contrato)
                    
                    const depositoInicial = await swal({
                        title: "¿Desea registrar el depósito por apertura de cuenta?",
                        text: "",
                        icon: "info",
                        buttons: true,
                        dangerMode: true
                    })
                    
                    if (depositoInicial) $("#modal_agregar_pago").modal("show")
                }
            } catch (error) {
                console.error(error)
            }
            return false
        }          
        const pagoApertura = (e) => {
          e.preventDefault()
          if (document.querySelector("#deposito").value < saldoMinimoApertura) return showError("El saldo inicial no puede ser menor a $" + saldoMinimoApertura)
                    
          const datos = $("#AddPagoApertura").serializeArray()
                    
          $.ajax({
            type: "POST",
            url: "/Apertura/pagoApertura/",
            data: $.param(datos),
            success: (respuesta) => {
              respuesta = JSON.parse(respuesta)
              console.log(respuesta)
              if (!respuesta.success) return showError(respuesta.mensaje)
        
              showSuccess(respuesta.mensaje)
              document.querySelector("#registroInicialAhorro").reset()
              document.querySelector("#AddPagoApertura").reset()
              $("#modal_agregar_pago").modal("hide")
            },
            error: (error) => {
              console.error(error)
              showError("Ocurrió un error al registrar el pago de apertura.")
            }
          })
        }      
        const validaDeposito = (e) => {
          const monto = parseFloat(e.target.value)
          document.querySelector("#deposito").value = monto.toFixed(2)
          const saldoInicial = (monto - parseFloat(document.querySelector("#inscripcion").value)).toFixed(2)
          document.querySelector("#saldo_inicial").value = saldoInicial > 0 ? saldoInicial : "0.00"
          document.querySelector("#deposito_inicial_letra").value = primeraMayuscula(numeroLetras(monto))
    
          if (saldoInicial < saldoMinimoApertura) {
            document.querySelector("#saldo_inicial").setAttribute("style", "color: red")
            document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
            document.querySelector("#registraDepositoInicial").disabled = true
          } else {
            document.querySelector("#saldo_inicial").removeAttribute("style")
            document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
            document.querySelector("#registraDepositoInicial").disabled = false
          }
        }
        const numeroLetras = (numero) => {
          if (!numero) return ""
          const unidades = ["", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"]
          const especiales = [
              "",
              "once",
              "doce",
              "trece",
              "catorce",
              "quince",
              "dieciséis",
              "diecisiete",
              "dieciocho",
              "diecinueve",
              "veinte",
              "veintiún",
              "veintidós",
              "veintitrés",
              "veinticuatro",
              "veinticinco",
              "veintiséis",
              "veintisiete",
              "veintiocho",
              "veintinueve"
          ]
          const decenas = [
              "",
              "diez",
              "veinte",
              "treinta",
              "cuarenta",
              "cincuenta",
              "sesenta",
              "setenta",
              "ochenta",
              "noventa"
          ]
          const centenas = [
              "cien",
              "ciento",
              "doscientos",
              "trescientos",
              "cuatrocientos",
              "quinientos",
              "seiscientos",
              "setecientos",
              "ochocientos",
              "novecientos"
          ]
      
          const convertirMenorA1000 = (numero) => {
              let letra = ""
              if (numero >= 100) {
                  letra += centenas[(numero === 100 ? 0 : Math.floor(numero / 100))] + " "
                  numero %= 100
              }
              if (numero === 10 || numero === 20 || (numero > 29 && numero < 100)) {
                  letra += decenas[Math.floor(numero / 10)]
                  numero %= 10
                  letra += numero > 0 ? " y " : " "
              }
              if (numero != 20 && numero >= 11 && numero <= 29) {
                  letra += especiales[numero % 10 + (numero > 20 ? 10 : 0)] + " "
                  numero = 0
              }
              if (numero > 0) {
                  letra += unidades[numero] + " "
              }
              return letra.trim()
          }
      
          const convertir = (numero) => {
            if (numero === 0) {
                return "cero"
            }
        
            let letra = ""
        
            if (numero >= 1000000) {
                letra += convertirMenorA1000(Math.floor(numero / 1000000)) + (numero === 1000000 ? " millón " : " millones ")
                numero %= 1000000
            }
        
            if (numero >= 1000) {
                letra += (numero === 1000 ? "" : convertirMenorA1000(Math.floor(numero / 1000))) + " mil "
                numero %= 1000
            }
        
            letra += convertirMenorA1000(numero)
            return letra.trim()
          }
      
          const parteEntera = Math.floor(numero)
          const parteDecimal = Math.round((numero - parteEntera) * 100).toString().padStart(2, "0")
          return convertir(parteEntera) + (numero == 1 ? ' peso ' : ' pesos ') + parteDecimal + '/100'
      }
        const primeraMayuscula = (texto) => {
        return texto.charAt(0).toUpperCase() + texto.slice(1)
      }
        </script>
html;

    View::set('header', $this->_contenedor->header($extraHeader));
    View::set('footer', $this->_contenedor->footer($extraFooter));
    View::render("caja_menu_ahorro");

  }
    public function ContratoCuentaCorriente()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_contrato_ahorro");

    }

    ///////////////////////////////////////////////////
    public function ContratoInversion()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_contrato_inversion");

    }

    public function ConsultaInversion()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_estatus_inversion");

    }

    ///////////////////////////////////////////////////

    public function CuentaPeque()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_peque");

    }

    public function ContratoCuentaPeque()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_contrato_peque");

    }

    //////////////////////////////////////////////////
    public function EstadoCuenta()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_estado_cuenta");

    }

    //////////////////////////////////////////////////
    public function SaldosDia()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_saldos_dia");

    }

    //////////////////////////////////////////////////
    public function SolicitudRetiro()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_solicitud_retiro");

    }

    public function SolicitudRetiroHistorial()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_solicitud_retiro_historial");

    }

    //////////////////////////////////////////////////
    public function ReimprimeTicket()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_reimprime_ticket");

    }

    //////////////////////////////////////////////////
    public function Calculadora()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_calculadora");

    }





}
