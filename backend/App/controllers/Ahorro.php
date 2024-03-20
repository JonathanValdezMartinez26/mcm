<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\CajaAhorro as CajaAhorroDao;
use \App\models\Ahorro as AhorroDao;

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
            const showError = (mensaje) => swal(mensaje, { icon: "error" })
            const showSuccess = (mensaje) => swal({ text: mensaje, icon: "success" })
         
            const validarYbuscar = (e, buscar) => {
                if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
                if (e.keyCode === 13) buscaCliente()
            }
            
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado")
                
                if (!noCliente.value) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/BuscaContrato/",
                    data: { cliente: noCliente.value },
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) return showError(respuesta.mensaje)
                        const datosCliente = respuesta.datos
                        
                        document.querySelector("#nombre").value = datosCliente.NOMBRE
                        document.querySelector("#curp").value = datosCliente.CURP
                        document.querySelector("#contrato").value = datosCliente.CONTRATO
                        document.querySelector("#cliente").value = noCliente.value
                        document.querySelector("#saldoActual").value = parseFloat(datosCliente.SALDO).toFixed(2)
                        noCliente.value = ""
                    },
                    error: (error) => {
                        console.error(error)
                        limpiaDatosCliente()
                        showError("Ocurrió un error al buscar el cliente.")
                    }
                })
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#fecha_pago").value = getHoy()
                document.querySelector("#contrato").value = ""
                document.querySelector("#cliente").value = ""
                document.querySelector("#curp").value = ""
                document.querySelector("#nombre").value = ""
            }
             
            const getHoy = () => {
                const hoy = new Date()
                const dd = String(hoy.getDate()).padStart(2, "0")
                const mm = String(hoy.getMonth() + 1).padStart(2, "0")
                const yyyy = hoy.getFullYear()
                return dd + "-" + mm + "-" + yyyy
            }
             
            const boton_contrato = (numero_contrato) => {
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
                    '<iframe src="' + host + '/Ahorro/ImprimeContrato/' +
                    numero_contrato +
                    '/" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
                plantilla += "</body>"
                plantilla += "</html>"
            
                const blob = new Blob([plantilla], { type: "text/html" })
                const url = URL.createObjectURL(blob)
                window.open(url, "_blank")
            }
             
            const validaDeposito = (e) => {
                const monto = parseFloat(e.target.value) || 0
                const saldoActual = parseFloat(document.querySelector("#saldoActual").value)
                const esDeposito = document.querySelector("#deposito").checked
                document.querySelector("#monto_letra").value = primeraMayuscula(numeroLetras(monto))
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                const saldoFinal = (esDeposito ? saldoActual + monto : saldoActual - monto).toFixed(2)
                document.querySelector("#saldoFinal").value = saldoFinal
                compruebaSaldoFinal(saldoFinal)
            }
             
            const cambioMovimiento = (e) => {
                const esDeposito = document.querySelector("#deposito").checked
                const saldoActual = parseFloat(document.querySelector("#saldoActual").value)
                const monto = parseFloat(document.querySelector("#montoOperacion").value) || 0
                document.querySelector("#saldoFinal").value = (esDeposito ? saldoActual + monto : saldoActual - monto).toFixed(2)
                document.querySelector("#simboloOperacion").innerText = esDeposito ? "+" : "-"
                document.querySelector("#descOperacion").innerText = (esDeposito ? "Depósito" : "Retiro") + " a cuenta ahorro corriente"
                compruebaSaldoFinal(document.querySelector("#saldoFinal").value)
            }
             
            const compruebaSaldoFinal = saldoFinal => {
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#registraDepositoInicial").disabled = !(document.querySelector("#saldoFinal").value >= 0 && document.querySelector("#montoOperacion").value > 0)
                
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
                return convertir(parteEntera) + (numero == 1 ? ' peso ' : ' pesos ') + parteDecimal + '/100 M.N.'
            }
             
            const primeraMayuscula = (texto) => {
                return texto.charAt(0).toUpperCase() + texto.slice(1)
            }
             
            const registraOperacion = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/registraOperacion/",
                    data: $.param(datos),
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success){
                            console.log(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                        showSuccess(respuesta.mensaje)
                        imprimeTicket(document.querySelector("#contrato").value)
                        document.querySelector("#registroOperacion").reset()
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al registrar la operación.")
                    }
                })
            }
             
            const imprimeTicket = (ticket) => {
                const host = window.location.origin
                
                let plantilla = "<!DOCTYPE html>"
                plantilla += '<html lang="es">'
                plantilla += '<head>'
                plantilla += '<meta charset="UTF-8">'
                plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
                plantilla += '<link rel="shortcut icon" href="' + host + '/img/logo.png">'
                plantilla += '<title>Ticket: ' + ticket + '</title>'
                plantilla += '</head>'
                plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
                plantilla +=
                    '<iframe src="' + host + '/Ahorro/Ticket/' +
                    ticket +
                    '/" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
                plantilla += "</body>"
                plantilla += "</html>"
            
                const blob = new Blob([plantilla], { type: "text/html" })
                const url = URL.createObjectURL(blob)
                window.open(url, "_blank")
            }
        </script>
        html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_menu_ahorro");
    }

    public function BuscaContrato()
    {
        $datos = CajaAhorroDao::BuscaClienteContrato($_POST['cliente']);
        echo $datos;
    }

    public function RegistraOperacion()
    {
        $resutado =  CajaAhorroDao::RegistraOperacion($_POST);
        echo $resutado;
    }

    public function ContratoCuentaCorriente()
    {
        $saldoMinimoApertura = 100;

        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
        html;

        $extraFooter = <<<html
        <script>
            const saldoMinimoApertura = $saldoMinimoApertura
         
            const showError = (mensaje) => swal(mensaje, { icon: "error" })
            const showSuccess = (mensaje) => swal(mensaje, { icon: "success" })
            const showInfo = (mensaje) => swal(mensaje, { icon: "info" })
         
            const validarYbuscar = (e, buscar) => {
                if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
                if (e.keyCode === 13) buscaCliente()
            }
             
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado")
                 
                if (!noCliente.value) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/BuscaCliente/",
                    data: { cliente: noCliente.value },
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) return showError(respuesta.mensaje)
                        const datosCliente = respuesta.datos
                         
                        document.querySelector("#fechaRegistro").value = datosCliente.FECHA_REGISTRO
                        document.querySelector("#noCliente").value = noCliente.value
                        document.querySelector("#nombre").value = datosCliente.NOMBRE
                        document.querySelector("#curp").value = datosCliente.CURP
                        document.querySelector("#edad").value = datosCliente.EDAD
                        document.querySelector("#direccion").value = datosCliente.DIRECCION
                        noCliente.value = ""
                    },
                    error: (error) => {
                        console.error(error)
                        limpiaDatosCliente()
                        showError("Ocurrió un error al buscar el cliente.")
                    }
                })
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#fechaRegistro").value = ""
                document.querySelector("#noCliente").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#curp").value = ""
                document.querySelector("#edad").value = ""
                document.querySelector("#direccion").value = ""
            }
             
            const boton_contrato = (numero_contrato) => {
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
                    '<iframe src="' + host + '/Ahorro/ImprimeContratoAhorro/' +
                    numero_contrato +
                    '/" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
                plantilla += "</body>"
                plantilla += "</html>"
            
                const blob = new Blob([plantilla], { type: "text/html" })
                const url = URL.createObjectURL(blob)
                window.open(url, "_blank")
            }
            
            const generaContrato = async (e) => {
                e.preventDefault()
                const cliente = document.querySelector("#nombre").value
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
                        const noCredito = document.querySelector("#noCliente").value
                        const datos = $("#registroInicialAhorro").serializeArray()
                        datos.push({ name: "credito", value: noCredito })
            
                        const respuesta = await $.ajax({
                            type: "POST",
                            url: "/Ahorro/AgregaContratoAhorro/",
                            data: $.param(datos)
                        })
                        
                        if (respuesta == "")
                            return showError(
                                "No pudimos generar el contrato, reintenta o contacta a tu Analista Soporte."
                            )
                        
                        const contrato = JSON.parse(respuesta)
                        await showSuccess("Se ha generado el contrato: " + contrato.contrato)
                        
                        document.querySelector("#fecha_pago").value = getHoy()
                        document.querySelector("#contrato").value = contrato.contrato
                        document.querySelector("#codigo_cl").value = noCredito
                        document.querySelector("#nombre_cliente").value = document.querySelector("#nombre").value
                        boton_contrato(contrato.contrato)
                        
                        const depositoInicial = await showInfo("Debe registrar el depósito por apertura de cuenta.")
                        
                        if (depositoInicial) $("#modal_agregar_pago").modal("show")
                    }
                } catch (error) {
                    console.error(error)
                }
                return false
            }
             
            const getHoy = () => {
                const hoy = new Date()
                const dd = String(hoy.getDate()).padStart(2, "0")
                const mm = String(hoy.getMonth() + 1).padStart(2, "0")
                const yyyy = hoy.getFullYear()
                return dd + "-" + mm + "-" + yyyy
            }
                        
            const pagoApertura = (e) => {
                e.preventDefault()
                if (document.querySelector("#deposito").value < saldoMinimoApertura) return showError("El saldo inicial no puede ser menor a $" + saldoMinimoApertura)
                            
                const datos = $("#AddPagoApertura").serializeArray()
                            
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/pagoApertura/",
                    data: $.param(datos),
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        console.log(respuesta)
                        if (!respuesta.success) return showError(respuesta.mensaje)
                    
                        showSuccess(respuesta.mensaje)
                        document.querySelector("#registroInicialAhorro").reset()
                        document.querySelector("#AddPagoApertura").reset()
                        $("#modal_agregar_pago").modal("hide")
                        limpiaDatosCliente()
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
                return convertir(parteEntera) + (numero == 1 ? ' peso ' : ' pesos ') + parteDecimal + '/100 M.N.'
            }
        
            const primeraMayuscula = (texto) => {
                return texto.charAt(0).toUpperCase() + texto.slice(1)
            }
        </script>
        html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        View::render("caja_menu_contrato_ahorro");
    }

    public function BuscaCliente()
    {
        $datos = CajaAhorroDao::BuscaClienteNvoContrato($_POST['cliente']);
        echo $datos;
    }

    public function AgregaContratoAhorro()
    {
        $contrato = CajaAhorroDao::AgregaContratoAhorro($_POST);
        echo $contrato;
    }

    public function PagoApertura()
    {
        $pago = CajaAhorroDao::AddPagoApertura($_POST);
        echo $pago;
        return $pago;
    }


    public function ImprimeContratoAhorro($numero_contrato)
    {
        $style = <<<html
        <style>
            .titulo {
                width: 100%;
                margin-top: 30px;
                color: #b92020;
                margin-left: auto;
                margin-right: auto;
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

        $tabla = <<<html
        <div class="receipt-main">
            <table class="table">
                <tr>
                    <th style="width: 600px" class="text-right">
                        <p class="receipt-title"><b>Recibo de Pago</b></p>
                    </th>
                    <th style="width: 10px" class="text-right">
                        <img
                            src="img/logo.png"
                            alt="Esta es una descripción alternativa de la imagen para cuando no se pueda mostrar"
                            width="60"
                            height="50"
                            align="left"
                        />
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
            <hr />
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
                    <tbody></tbody>
                </table>
            </div>
        </div>
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
        <title>Reimprime TicketS</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [10, 50, -1],
                    [10, 50, 'Todos'],
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
        });
           
        function Reimprime_ticket(folio)
        {
              
              $('#modal_ticket').modal('show');
              document.getElementById("folio").value = folio;
             
        }
        
        function enviar_add_sol()
        {
             $('#modal_ticket').modal('hide');
             swal({
                   title: "¿Está segura de continuar?",
                   text: "",
                   icon: "warning",
                   buttons: ["Cancelar", "Continuar"],
                   dangerMode: false
                   })
                   .then((willDelete) => {
                   if (willDelete) {
                      
                        $.ajax({
                        type: 'POST',
                        url: '/Ahorro/AddSolicitudReimpresion/',
                        data: $('#Add').serialize(),
                        success: function(respuesta) {
                        if(respuesta=='1')
                        {
                            swal("Registro guardado exitosamente", {
                                       icon: "success",
                                 });
                            location.reload();
                                            
                        }
                        else {
                              $('#modal_encuesta_cliente').modal('hide')
                                      swal(respuesta, {
                                      icon: "error",
                                     });
                                                
                              }
                         }
                            });
                         }
                        else {
                                    $('#modal_ticket').modal('show');
                              }
                        });
        }
        </script>
html;

        $Consulta = AhorroDao::ConsultaTickets($this->__usuario);
        $tabla = "";

        foreach ($Consulta as $key => $value) {
            $monto = number_format($value['MONTO'], 2);

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                   <td style="padding: 0px !important;">{$value['CODIGO']} </td>
                    <td style="padding: 0px !important;" width="45" nowrap=""><span class="count_top" style="font-size: 14px"> &nbsp;&nbsp;<i class="fa fa-barcode" style="color: #787b70"></i> </span>{$value['CDG_CONTRATO']} &nbsp;</td>
                    <td style="padding: 0px !important;">{$value['FECHA_ALTA']} </td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;"></td>
                    <td style="padding: 0px !important;">{$value['CDGPE']}</td>
                    <td style="padding: 0px !important;" class="center">
                         <button type="button" class="btn btn-success btn-circle" onclick="Reimprime_ticket('{$value['CODIGO']}');"><i class="fa fa-print"></i></button>
                    </td>
                </td>
html;
        }

        $fecha_y_hora = date("Y-m-d H:i:s");



        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha_actual', $fecha_y_hora);
        View::render("caja_menu_reimprime_ticket");
    }

    public function AddSolicitudReimpresion()
    {
        $solicitud = new \stdClass();

        $solicitud->_folio = MasterDom::getData('folio');
        $solicitud->_cdgpe = $this->__usuario;


        $id = AhorroDao::insertSolicitudAhorro($solicitud);

        return 0;
    }

    public function Ticket($ticket)
    {
        $nombreArchivo = "Contrato " . $ticket;

        $mpdf = new \mPDF('UTF-8', array(90, 190));
        $mpdf->SetMargins(0, 0, 10);
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML('<div></div>', 2);

        // CABECERA
        $mpdf->SetFont('Helvetica', '', 19);
        $mpdf->Cell(60, 4, 'Más con Menos', 0, 1, 'C');
        $mpdf->Ln(2);
        $mpdf->SetFont('Helvetica', '', 8);
        $mpdf->Cell(60, 4, 'Financiera', 0, 1, 'C');
        $mpdf->Cell(60, 4, 'Dirección de la sucursal, C.P 00000', 0, 1, 'C');
        $mpdf->Cell(60, 4, '000 000 00000', 0, 1, 'C');

        // LEYENDA TIPO COMPROBANTE
        $mpdf->Ln(10);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, 'COMPROBANTE DE DEPOSITO', 0, 1, 'C');
        $mpdf->Ln(3);
        $mpdf->Cell(60, 0, str_repeat('*', 35), 0, 1, 'C');

        // DATOS OPERACION
        $mpdf->Ln(2);
        $mpdf->SetFont('Helvetica', '', 9);
        $mpdf->Cell(60, 4, 'Fecha de la operación: ' . $fecha_op, 0, 1, '');
        $mpdf->Cell(60, 4, 'Método de pago: Efectivo', 0, 1, '');
        $mpdf->MultiCell(60, 4, 'Recibió: NOMBRE DE LA CAJERA MCM', 0, 1, '');
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 0, str_repeat('_', 32), 0, 1, 'C');

        // DATOS CLIENTE
        $mpdf->Ln(5);
        $mpdf->SetFont('Helvetica', '', 9);
        $mpdf->MultiCell(60, 4, 'Nombre del cliente: ' . $nombre_cliente, 0, 1, '');
        $mpdf->Cell(60, 4, 'Código de cliente: 000000', 0, 1, '');
        $mpdf->Cell(60, 4, 'Código de contrato: 0000000000000', 0, 1, '');
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 0, str_repeat('_', 32), 0, 1, 'C');

        // FOLIO DE LA OPERACION
        $mpdf->Ln(5);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(60, 4, 'FOLIO DE LA OPERACIÓN', 0, 1, 'C');
        $mpdf->Cell(60, 4, '01050505051400000002024', 0, 1, 'C');

        // DETALLE DE LA OPERACION
        $mpdf->Ln(10);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, 'CUENTA DE AHORRO CORRIENTE', 0, 1, 'C');
        $mpdf->Ln(3);
        $mpdf->Cell(60, 0, str_repeat('*', 35), 0, 1, 'C');

        // MONTO DE LA OPERACION
        $mpdf->Ln(3);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, 'RECIBIMOS $1,200.00', 0, 1, 'C');
        $mpdf->SetFont('Helvetica', '', 8);
        $mpdf->MultiCell(60, 4, '(UN MIL DOSCIENTOS 00/100 M.N)', 0, 'C');
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 0, str_repeat('_', 32), 0, 1, 'C');

        // DESGLOSE DE LA OPERACION
        $mpdf->Ln(4);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(30, 10, 'SALDO ANTERIOR:', 0);
        $mpdf->Cell(30, 10, '$1,0000.00', 2, 0, 'R');
        $mpdf->Ln(8);
        $mpdf->Cell(30, 10, 'ABONO A CUENTA :', 0);
        $mpdf->Cell(30, 10, '$1,0000.00', 2, 0, 'R');
        $mpdf->Ln(8);
        $mpdf->Cell(30, 10, 'SALDO NUEVO: ', 0);
        $mpdf->Cell(30, 10, '$1,0000.00', 2, 0, 'R');

        // FIRMAS
        $mpdf->Ln(20);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(60, 4, 'Firma de conformidad', 0, 1, 'C');
        $mpdf->Ln(5);
        $mpdf->Cell(60, 0, str_repeat('_', 25), 0, 1, 'C');

        // PIE DE PAGINA
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:11px;font-family:Helvetica;"><br>Fecha de impresión: ' . date('Y-m-d H:i:s') . '</div>');

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
        exit;
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
