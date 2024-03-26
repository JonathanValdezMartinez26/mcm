<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
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
                        limpiaDatosCliente()
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) return showError(respuesta.mensaje)
                        const datosCliente = respuesta.datos
                         
                        document.querySelector("#nombre").value = datosCliente.NOMBRE
                        document.querySelector("#curp").value = datosCliente.CURP
                        document.querySelector("#contrato").value = datosCliente.CONTRATO
                        document.querySelector("#cliente").value = datosCliente.CDGCL
                        document.querySelector("#saldoActual").value = parseFloat(datosCliente.SALDO).toFixed(2)
                        document.querySelector("#monto").disabled = false
                    },
                    error: (error) => {
                        console.error(error)
                        limpiaDatosCliente()
                        showError("Ocurrió un error al buscar el cliente.")
                    }
                })
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
            }
             
            const getHoy = () => {
                const hoy = new Date()
                const dd = String(hoy.getDate()).padStart(2, "0")
                const mm = String(hoy.getMonth() + 1).padStart(2, "0")
                const yyyy = hoy.getFullYear()
                return yyyy + "-" + mm + "-" + dd
            }
             
            let valKD = false
            const soloNumeros = (e) => {
                valKD = false
                if ((e.keyCode > 95 && e.keyCode < 106) || (e.keyCode > 47 && e.keyCode < 58)) {
                    valKD = true
                    return
                }
                if (e.keyCode === 110 || e.keyCode === 190 || e.keyCode === 8) {
                    valKD = true
                    return
                }
                return e.preventDefault()
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                const monto = parseFloat(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
                }
                 
                if (monto > 1000000) {
                    e.preventDefault()
                    e.target.value = 1000000.00
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(e.target.value))
                if (document.querySelector("#deposito").checked || document.querySelector("#retiro").checked) calculaSaldoFinal()
            }
             
            const calculaSaldoFinal = () => {
                const esDeposito = document.querySelector("#deposito").checked
                const saldoActual = parseFloat(document.querySelector("#saldoActual").value)
                const monto = parseFloat(document.querySelector("#monto").value) || 0
                document.querySelector("#montoOperacion").value =monto.toFixed(2)
                document.querySelector("#saldoFinal").value = (esDeposito ? saldoActual + monto : saldoActual - monto).toFixed(2)
                compruebaSaldoFinal(document.querySelector("#saldoFinal").value)
            }
             
            const cambioMovimiento = (e) => {
                const esDeposito = document.querySelector("#deposito").checked
                document.querySelector("#simboloOperacion").innerText = esDeposito ? "+" : "-"
                document.querySelector("#descOperacion").innerText = (esDeposito ? "Depósito" : "Retiro") + " a cuenta ahorro corriente"
                calculaSaldoFinal()
            }
             
            const compruebaSaldoFinal = saldoFinal => {
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(document.querySelector("#saldoFinal").value >= 0 && document.querySelector("#montoOperacion").value > 0)
                
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
                return primeraMayuscula(convertir(parteEntera)) + (numero == 1 ? ' peso ' : ' pesos ') + parteDecimal + '/100 M.N.'
            }
             
            const primeraMayuscula = (texto) => {
                return texto.charAt(0).toUpperCase() + texto.slice(1)
            }
             
            const registraOperacion = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                 
                if (!document.querySelector("#deposito").checked && !document.querySelector("#retiro").checked) {
                    return showError("Seleccione el tipo de operación a realizar.")
                }
                
                datos.forEach((dato) => {
                    if (dato.name === "esDeposito") {
                        dato.value = document.querySelector("#deposito").checked
                    }
                })
                 
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/registraOperacion/",
                    data: $.param(datos),
                    success: (respuesta) => {
                        console.log(respuesta)
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success){
                            console.log(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                        showSuccess(respuesta.mensaje)
                        imprimeTicket(respuesta.datos.ticket)
                        limpiaDatosCliente()
                    },
                    error: (error) => {
                        console.log(respuesta)
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
        $datos = CajaAhorroDao::BuscaClienteContrato($_POST);
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
                        if (!respuesta.success) {
                            limpiaDatosCliente()
                            return showError(respuesta.mensaje)
                        }
                        const datosCliente = respuesta.datos
                         
                        document.querySelector("#fechaRegistro").value = datosCliente.FECHA_REGISTRO
                        document.querySelector("#noCliente").value = noCliente.value
                        document.querySelector("#nombre").value = datosCliente.NOMBRE
                        document.querySelector("#curp").value = datosCliente.CURP
                        document.querySelector("#edad").value = datosCliente.EDAD
                        document.querySelector("#direccion").value = datosCliente.DIRECCION
                        noCliente.value = ""
                        habilitaBeneficiario(1, true)
                    },
                    error: (error) => {
                        console.error(error)
                        limpiaDatosCliente()
                        showError("Ocurrió un error al buscar el cliente.")
                    }
                })
            }
             
            const habilitaBeneficiario = (numBeneficiario, habilitar) => {
                document.querySelector("#beneficiario_" + numBeneficiario).disabled = !habilitar
                document.querySelector("#parentesco_" + numBeneficiario).disabled = !habilitar
                document.querySelector("#porcentaje_" + numBeneficiario).disabled = !habilitar
                document.querySelector("#btnBen" + numBeneficiario).disabled = !habilitar
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#registroInicialAhorro").reset()
                 
                document.querySelector("#fechaRegistro").value = ""
                document.querySelector("#noCliente").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#curp").value = ""
                document.querySelector("#edad").value = ""
                document.querySelector("#direccion").value = ""
                habilitaBeneficiario(1, false)
                document.querySelector("#ben2").style.opacity = "0"
                document.querySelector("#ben3").style.opacity = "0"
                document.querySelector("#btnGeneraContrato").disabled = true
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
                            "¿Está segura de continuar con la apertura de la cuenta de ahorro del cliente: " +
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
            
                        let respuesta = await $.ajax({
                            type: "POST",
                            url: "/Ahorro/AgregaContratoAhorro/",
                            data: $.param(datos)
                        })
                        
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) {
                            console.error(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                        
                        const contrato = respuesta.datos
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
                return dd + "-" + mm + "-" + yyyy + " " + hoy.getHours() + ":" + hoy.getMinutes() + ":" + hoy.getSeconds()
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
                        if (!respuesta.success) return showError(respuesta.mensaje)
                    
                        showSuccess(respuesta.mensaje)
                        document.querySelector("#registroInicialAhorro").reset()
                        document.querySelector("#AddPagoApertura").reset()
                        $("#modal_agregar_pago").modal("hide")
                        limpiaDatosCliente()
                        imprimeTicket(respuesta.datos.ticket)
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al registrar el pago de apertura.")
                    }
                })
            }
            
            let valKD = false
            const soloNumeros = (e) => {
                valKD = false
                if ((e.keyCode > 95 && e.keyCode < 106) || (e.keyCode > 47 && e.keyCode < 58)) {
                    valKD = true
                    return
                }
                if (e.keyCode === 110 || e.keyCode === 190 || e.keyCode === 8) {
                    valKD = true
                    return
                }
                return e.preventDefault()
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                const monto = parseFloat(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
                }
                 
                if (monto > 1000000) {
                    e.preventDefault()
                    e.target.value = 1000000.00
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#deposito_inicial_letra").value = numeroLetras(parseFloat(e.target.value))
                calculaSaldoFinal(e)
            }
             
            const calculaSaldoFinal = (e) => {
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
                return primeraMayuscula(convertir(parteEntera)) + (numero == 1 ? ' peso ' : ' pesos ') + parteDecimal + '/100 M.N.'
            }
        
            const primeraMayuscula = (texto) => {
                return texto.charAt(0).toUpperCase() + texto.slice(1)
            }
             
            const camposLlenos = (e) => {
                const val = () => {
                    let porcentaje = 0
                    for (let i = 1; i <= 3; i++) {
                        if (document.querySelector("#ben" + i).style.opacity === "1") {
                            if (!document.querySelector("#beneficiario_" + i).value) return false
                            if (document.querySelector("#parentesco_" + i).selectedIndex === 0) return false
                            if (!document.querySelector("#porcentaje_" + i).value) return false
                        }
                        porcentaje += parseFloat(document.querySelector("#porcentaje_" + i).value) || 0
                    }
                     
                    if (porcentaje > 100) {
                        e.preventDefault()
                        e.target.value = ""
                        showError("La suma de los porcentajes no puede ser mayor a 100%")
                    }
                     
                    return porcentaje === 100
                }
                 
                if (e.target.tagName === "SELECT") actualizarOpciones(e.target)
                 
                document.querySelector("#btnGeneraContrato").disabled = !val()
            }
             
            const validaPorcentaje = (e) => {
                let porcentaje = 0
                for (let i = 1; i <= 3; i++) {
                    if (i == 1 || document.querySelector("#ben" + i).style.opacity === "1") {
                        const porcentajeBeneficiario = parseFloat(document.querySelector("#porcentaje_" + i).value) || 0
                        porcentaje += porcentajeBeneficiario
                    }
                }
                if (porcentaje > 100) {
                    e.preventDefault()
                    e.target.value = ""
                    return showError("La suma de los porcentajes no puede ser mayor a 100%")
                }
                 
                document.querySelector("#btnGeneraContrato").disabled = porcentaje !== 100
            }
             
            const toggleBeneficiario = (numBeneficiario) => {
                const ben = document.getElementById(`ben` + numBeneficiario)
                ben.style.opacity = ben.style.opacity === "0" ? "1" : "0"
            }
             
            const toggleButtonIcon = (btnId, show) => {
                const btn = document.getElementById("btnBen" + btnId)
                btn.innerHTML = show ? '<i class="fa fa-minus"></i>' : '<i class="fa fa-plus"></i>'
            }
             
            const addBeneficiario = (event) => {
                const btn = event.target === event.currentTarget ? event.target : event.target.parentElement
                 
                if (btn.innerHTML.trim() === '<i class="fa fa-plus"></i>') {
                    const noID = parseInt(btn.id.split("btnBen")[1])
                    habilitaBeneficiario(noID+1, true)
                    toggleBeneficiario(noID+1)
                    toggleButtonIcon(noID, true)
                } else {
                    const noID = parseInt(btn.id.split("btnBen")[1])
                    for (let j = noID; j < 3; j++) {
                        moveData(j+1, j)
                    }
                    for (let i = 3; i > 0; i--) {
                        if (document.getElementById(`ben` + i).style.opacity === "1") {
                            habilitaBeneficiario(i, false)
                            toggleButtonIcon(i-1, false)
                            toggleBeneficiario(i)
                            break
                        }
                    }
                }
            }
             
            const moveData = (from, to) => {
                const beneficiarioFrom = document.getElementById(`beneficiario_` + from)
                const parentescoFrom = document.getElementById(`parentesco_` + from)
                const porcentajeFrom = document.getElementById(`porcentaje_` + from)
                 
                const beneficiarioTo = document.getElementById(`beneficiario_` + to)
                const parentescoTo = document.getElementById(`parentesco_` + to)
                const porcentajeTo = document.getElementById(`porcentaje_` + to)
                 
                beneficiarioTo.value = beneficiarioFrom.value
                parentescoTo.value = parentescoFrom.value
                porcentajeTo.value = porcentajeFrom.value
                 
                beneficiarioFrom.value = ""
                parentescoFrom.value = ""
                porcentajeFrom.value = ""
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
             
            const actualizarOpciones = (select) => {
                const valoresUnicos = [
                    "CÓNYUGE",
                    "PADRE",
                    "MADRE",
                ]
                     
                const valorSeleccionado = select.value
                const selects = document.querySelectorAll("#parentesco_1, #parentesco_2, #parentesco_3")
                const valoresSeleccionados = [
                    document.querySelector("#parentesco_1").value,
                    document.querySelector("#parentesco_2").value,
                    document.querySelector("#parentesco_3").value
                ]     
                 
                selects.forEach(element => {
                    if (element !== select) {
                        element.querySelectorAll("option").forEach(opcion => {
                            if (!valoresUnicos.includes(opcion.text)) return
                            if (valoresUnicos.includes(opcion.text) &&
                            valoresSeleccionados.includes(opcion.value)) return opcion.style.display = "none"
                            opcion.style.display = opcion.value === valorSeleccionado ? "none" : "block"
                        })
                    }
                })
            }
        </script>
        html;

        $parentescos = CajaAhorroDao::GetCatalogoParentescos();

        $opcParentescos = "<option value='' disabled selected>Seleccionar</option>";
        foreach ($parentescos as $parentesco) {
            $opcParentescos .= "<option value='{$parentesco['CODIGO']}'>{$parentesco['DESCRIPCION']}</option>";
        }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        View::set('fecha', date('d/m/Y'));
        view::set('opcParentescos', $opcParentescos);
        View::render("caja_menu_contrato_ahorro");
    }

    public function BuscaCliente()
    {
        $datos = CajaAhorroDao::BuscaClienteNvoContrato($_POST);
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

    public function SolicitudRetiroCuentaCorriente()
    {
        $extraHeader = <<<html
        <title>Solicitud de Retiro Ahorro</title>
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
                    return showError("Ingrese un número de cliente a buscar.")
                }
                
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/BuscaContrato/",
                    data: { cliente: noCliente.value },
                    success: (respuesta) => {
                        //limpiaDatosCliente()
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) {
                            return showError(respuesta.mensaje)
                        }
                       
                         const datosCliente = respuesta.datos
                       
                       
                        var saldo = parseFloat(datosCliente.SALDO).toFixed(2);
                        
                                   
                        document.querySelector("#nombre_cliente").value = datosCliente.NOMBRE
                        document.querySelector("#curp_").value = datosCliente.CURP
                        document.querySelector("#codigo_cl").value = noCliente.value
                        
                        var columnas = document.getElementsByTagName('td');
                        //columnas[1].innerHTML = datosCliente.FAERTURA;
                        columnas[2].innerHTML = datosCliente.CONTRATO;
                        columnas[3].innerHTML = saldo;
                        
                        $("#genera_tabla").show();
                        $("#monto_div").hide();
                        noCliente.value = ""
                        
                        document.querySelector("#codigo_cl").value = noCliente.value
                        
                        if(saldo == 1150)
                         {
                                return showError("El cliente no tiene saldo disponible para solicitar un retiro.")
                         }
                         //if(fregistro >= 30)
                         //{
                         //       return showError("El cliente no puede realizar un retiro hasta el día". suma_fecha )
                         //}
                        
                        
                       
                    },
                    error: (error) => {
                        console.error(error)
                        limpiaDatosCliente()
                        showError("Ocurrió un error al buscar el cliente.")
                    }
                })
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
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    return showError("El monto a depositar debe ser mayor a 0")
                }
                 
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
                document.querySelector("#btnRegistraOperacion").disabled = !(document.querySelector("#saldoFinal").value >= 0 && document.querySelector("#montoOperacion").value > 0)
                
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
                
                datos.forEach((dato) => {
                    if (dato.name === "esDeposito") {
                        dato.value = document.querySelector("#deposito").checked
                    }
                })
                 
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
        View::render("caja_menu_retiro_ahorro");
    }

    public function HistorialSolicitudRetiroCuentaCorriente()
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
        View::render("caja_menu_retiro_ahorro");
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
                    url: "/Ahorro/BuscaContratoPQ/",
                    data: { cliente: noCliente.value },
                    success: (respuesta) => {
                        limpiaDatosCliente()
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) return showError(respuesta.mensaje)
                        const datosCliente = respuesta.datos
                         
                        const contratos = document.createDocumentFragment()
                        const seleccionar = document.createElement("option")
                        seleccionar.value = ""
                        seleccionar.innerText = "Seleccionar"
                        contratos.appendChild(seleccionar)
                         
                        datosCliente.forEach(cliente => {
                            const opcion = document.createElement("option")
                            opcion.value = cliente.CDG_CONTRATO
                            opcion.innerText = cliente.CDG_CONTRATO
                            contratos.appendChild(opcion)
                        })
                         
                        document.querySelector("#contrato").appendChild(contratos)
                        document.querySelector("#contrato").disabled = false
                        document.querySelector("#contrato").addEventListener("change", (e) => {
                            datosCliente.forEach(contrato => {
                                if (contrato.CDG_CONTRATO == e.target.value) {
                                    document.querySelector("#nombre").value = contrato.NOMBRE
                                    document.querySelector("#curp").value = contrato.CURP
                                    document.querySelector("#cliente").value = contrato.CDGCL
                                    document.querySelector("#saldoActual").value = parseFloat(contrato.SALDO).toFixed(2)
                                    if (document.querySelector("#deposito").checked || document.querySelector("#retiro").checked) calculaSaldoFinal()
                                }
                            })
                        })
                        document.querySelector("#monto").disabled=false
                        
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
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#fecha_pago").value = getHoy()
                document.querySelector("#monto").disabled = true
                document.querySelector("#contrato").innerHTML = ""
                document.querySelector("#contrato").disabled = true
            }
             
            const getHoy = () => {
                const hoy = new Date()
                const dd = String(hoy.getDate()).padStart(2, "0")
                const mm = String(hoy.getMonth() + 1).padStart(2, "0")
                const yyyy = hoy.getFullYear()
                return yyyy + "-" + mm + "-" + dd
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
            
             
            let valKD = false
            const soloNumeros = (e) => {
                valKD = false
                if ((e.keyCode > 95 && e.keyCode < 106) || (e.keyCode > 47 && e.keyCode < 58)) {
                    valKD = true
                    return
                }
                if (e.keyCode === 110 || e.keyCode === 190 || e.keyCode === 8) {
                    valKD = true
                    return
                }
                return e.preventDefault()
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                const monto = parseFloat(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
                }
                 
                if (monto > 1000000) {
                    e.preventDefault()
                    e.target.value = 1000000.00
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(e.target.value))
                if (document.querySelector("#deposito").checked || document.querySelector("#retiro").checked) calculaSaldoFinal()
            }
             
            const calculaSaldoFinal = () => {
                const esDeposito = document.querySelector("#deposito").checked
                const saldoActual = parseFloat(document.querySelector("#saldoActual").value)
                const monto = parseFloat(document.querySelector("#monto").value) || 0
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                document.querySelector("#saldoFinal").value = (esDeposito ? saldoActual + monto : saldoActual - monto).toFixed(2)
                compruebaSaldoFinal(document.querySelector("#saldoFinal").value)
            }
             
            const cambioMovimiento = (e) => {
                const esDeposito = document.querySelector("#deposito").checked
                document.querySelector("#simboloOperacion").innerText = esDeposito ? "+" : "-"
                document.querySelector("#descOperacion").innerText = (esDeposito ? "Depósito" : "Retiro") + " a cuenta ahorro corriente"
                calculaSaldoFinal()
            }
             
            const compruebaSaldoFinal = saldoFinal => {
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(document.querySelector("#saldoFinal").value >= 0 && document.querySelector("#montoOperacion").value > 0)
                
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
                return primeraMayuscula(convertir(parteEntera)) + (numero == 1 ? ' peso ' : ' pesos ') + parteDecimal + '/100 M.N.'
            }
             
            const primeraMayuscula = (texto) => {
                return texto.charAt(0).toUpperCase() + texto.slice(1)
            }
             
            const registraOperacion = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                 
                if (!document.querySelector("#deposito").checked && !document.querySelector("#retiro").checked) {
                    return showError("Seleccione el tipo de operación a realizar.")
                }
                
                datos.forEach((dato) => {
                    if (dato.name === "esDeposito") {
                        dato.value = document.querySelector("#deposito").checked
                    }
                })
                 
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
                        imprimeTicket(respuesta.datos.ticket)
                        limpiaDatosCliente()
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
        View::render("caja_menu_peque");
    }

    public function ContratoCuentaPeque()
    {
        $saldoMinimoApertura = 50;

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
                    url: "/Ahorro/BuscaClientePQ/",
                    data: { cliente: noCliente.value },
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) {
                            limpiaDatosCliente()
                            return showError(respuesta.mensaje)
                        }
                        const datosCliente = respuesta.datos
                         
                        document.querySelector("#fechaRegistro").value = datosCliente.FECHA_REGISTRO
                        document.querySelector("#noCliente").value = noCliente.value
                        document.querySelector("#nombre").value = datosCliente.NOMBRE
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
                document.querySelector("#registroInicialAhorro").reset()
                 
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
                         
                        datos.forEach((dato) => {
                            if (dato.name === "sexo") {
                                dato.value = document.querySelector("#sexoH").checked
                            }
                        })
                         
                        let respuesta = await $.ajax({
                            type: "POST",
                            url: "/Ahorro/AgregaContratoAhorroPQ/",
                            data: $.param(datos)
                        })
                        
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) {
                            console.error(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                        
                        const contrato = respuesta.datos
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
                return dd + "/" + mm + "/" + yyyy + " " + hoy.getHours() + ":" + hoy.getMinutes() + ":" + hoy.getSeconds()
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
                        if (!respuesta.success) return showError(respuesta.mensaje)
                    
                        showSuccess(respuesta.mensaje)
                        document.querySelector("#registroInicialAhorro").reset()
                        document.querySelector("#AddPagoApertura").reset()
                        $("#modal_agregar_pago").modal("hide")
                        limpiaDatosCliente()
                        imprimeTicket(respuesta.datos.ticket)
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al registrar el pago de apertura.")
                    }
                })
            }
            
            
            let valKD = false
            const soloNumeros = (e) => {
                valKD = false
                if ((e.keyCode > 95 && e.keyCode < 106) || (e.keyCode > 47 && e.keyCode < 58)) {
                    valKD = true
                    return
                }
                if (e.keyCode === 110 || e.keyCode === 190 || e.keyCode === 8) {
                    valKD = true
                    return
                }
                return e.preventDefault()
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                const monto = parseFloat(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
                }
                 
                if (monto > 1000000) {
                    e.preventDefault()
                    e.target.value = 1000000.00
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#deposito_inicial_letra").value = numeroLetras(parseFloat(e.target.value))
                calculaSaldoFinal(e)
            }
             
            const calculaSaldoFinal = (e) => {
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
             
            const camposLlenos = (e) => {
                const val = () => {
                    const campos = [
                        document.querySelector("#nombre1").value,
                        document.querySelector("#apellido1").value,
                        document.querySelector("#fecha_nac").value,
                        document.querySelector("#ciudad").value,
                        document.querySelector("#curp").value,
                        document.querySelector("#edad").value,
                        document.querySelector("#direccion").value
                    ]
                    return campos.every((campo) => campo)
                }
                document.querySelector("#btnGeneraContrato").disabled = !val()
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
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        View::set('fecha', date('Y-m-d'));
        View::render("caja_menu_contrato_peque");
    }

    public function BuscaClientePQ()
    {
        $datos = CajaAhorroDao::BuscaClienteNvoContratoPQ($_POST);
        echo $datos;
    }

    public function AgregaContratoAhorroPQ()
    {
        $contrato = CajaAhorroDao::AgregaContratoAhorroPQ($_POST);
        echo $contrato;
    }

    public function BuscaContratoPQ()
    {
        $datos = CajaAhorroDao::BuscaClienteContratoPQ($_POST);
        echo $datos;
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
    public function ReimprimeTicketSolicitudes()
    {
        $extraHeader = <<<html
        <title>Solicitudes de reimpresión Tickets</title>
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
        
        </script>
html;

        $Consulta = AhorroDao::ConsultaSolicitudesTickets($this->__usuario);
        $tabla = "";

        foreach ($Consulta as $key => $value) {
            $monto = number_format($value['MONTO'], 2);

            if($value['AUTORIZA'] == 0)
            {
                $autoriza = "PENDIENTE";

                $imprime = <<<html
                    <span class="count_top" style="font-size: 22px"><i class="fa fa-clock-o" style="color: #ac8200"></i></span>
html;
            }
            else if($value['AUTORIZA'] == 1)
            {
                $autoriza = "ACEPTADO";
                $imprime = <<<html
                    <button type="button" class="btn btn-success btn-circle" style="background: #b7b7b7;" onclick="Reimprime_ticket('{$value['CODIGO']}');"><i class="fa fa-print"></i></button>
html;
            }
            else if($value['AUTORIZA'] == 2)
            {
                $imprime = <<<html
                 <span class="count_top" style="font-size: 22px"><i class="fa fa-window-close" style="color: #ac0000"></i></span>
html;
                $autoriza = "RECHAZADO";
            }


            if($value['CDGPE_AUTORIZA'] == '')
            {
                $autoriza_nombre = "-";
            }
            else if($value['CDGPE_AUTORIZA'] != '')
            {
                $autoriza_nombre = $value['CDGPE_AUTORIZA'];
            }




            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                   <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;" width="45" nowrap=""><span class="count_top" style="font-size: 14px"> &nbsp;&nbsp;<i class="fa fa-barcode" style="color: #787b70"></i> </span>{$value['CDG_CONTRATO']} &nbsp;</td>
                    <td style="padding: 0px !important;">{$value['FREGISTRO']} </td>
                    <td style="padding: 0px !important;">{$value['MOTIVO']}</td>
                    <td style="padding: 0px !important;"> {$autoriza}</td>
                    <td style="padding: 0px !important;">{$autoriza_nombre}</td>
                    <td style="padding: 0px !important;" class="center">
                    {$imprime}
                    </td>
                </td>
html;
        }

        $fecha_y_hora = date("Y-m-d H:i:s");



        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha_actual', $fecha_y_hora);
        View::render("caja_menu_reimprime_ticket_historial");
    }

    public function ReimprimeTicket()
    {
        $extraHeader = <<<html
        <title>Reimprime Tickets</title>
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
             const showSuccess = (mensaje) => swal(mensaje, { icon: "success" } )
             
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
                           return showSuccess("Solicitud enviada a tesorería." );
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
                    <td style="padding: 0px !important;">{$value['TIPO_AHORRO']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_CLIENTE']}</td>
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
        $solicitud->_descripcion = MasterDom::getData('descripcion');
        $solicitud->_motivo = MasterDom::getData('motivo');
        $solicitud->_cdgpe = $this->__usuario;


        $id = AhorroDao::insertSolicitudAhorro($solicitud);

        return $id;
    }

    public function Ticket($ticket)
    {
        $datos = CajaAhorroDao::DatosTicket($ticket);
        if (!$datos) {
            echo "No se encontraron datos para el ticket: " . $ticket;
            return;
        }

        $nombreArchivo = "Ticket " . $ticket;

        $mpdf = new \mPDF('UTF-8', array(90, 190));
        $mpdf->SetMargins(0, 0, 10);
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML('<div></div>', 2);

        // CABECERA
        $mpdf->SetFont('Helvetica', '', 19);
        $mpdf->Cell(60, 4, 'Más con Menos', 0, 1, 'C');
        $mpdf->Ln(2);
        $mpdf->SetFont('Helvetica', '', 8);
        $mpdf->Cell(60, 4, 'Dirección de la sucursal, C.P 00000', 0, 1, 'C');
        $mpdf->Cell(60, 4, '000 000 00000', 0, 1, 'C');

        // LEYENDA TIPO COMPROBANTE
        $mpdf->Ln(3);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, 'COMPROBANTE DE ' . ($datos['ES_DEPOSITO'] == 1 ? 'DEPÓSITO' : 'RETIRO'), 0, 1, 'C');
        $mpdf->Ln(3);
        $mpdf->Cell(60, 0, str_repeat('*', 35), 0, 1, 'C');

        // DATOS OPERACION
        $mpdf->Ln(2);
        $mpdf->SetFont('Helvetica', '', 9);
        $mpdf->Cell(60, 4, 'Fecha de la operación: ' . $datos['FECHA'], 0, 1, '');
        $mpdf->Cell(60, 4, 'Método de pago: EFECTIVO', 0, 1, '');
        $mpdf->MultiCell(60, 4, ($datos['ES_DEPOSITO'] == 1 ? 'Recibió' : 'Entrego') . ': ' . $datos['NOM_EJECUTIVO'] . ' (' . $datos['COD_EJECUTIVO'] . ')', 0, 1, '');
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 0, str_repeat('_', 32), 0, 1, 'C');

        // DATOS CLIENTE
        $mpdf->Ln(5);
        $mpdf->SetFont('Helvetica', '', 9);
        $mpdf->MultiCell(60, 4, 'Nombre del cliente: ' . $datos['NOMBRE_CLIENTE'], 0, 1, '');
        $mpdf->Cell(60, 4, 'Código de cliente: ' . $datos['CODIGO'], 0, 1, '');
        $mpdf->Cell(60, 4, 'Código de contrato: ' . $datos['CONTRATO'], 0, 1, '');
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 0, str_repeat('_', 32), 0, 1, 'C');

        // DETALLE DE LA OPERACION
        $mpdf->Ln(7);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, 'CUENTA DE AHORRO CORRIENTE', 0, 1, 'C');
        $mpdf->Ln(3);
        $mpdf->Cell(60, 0, str_repeat('*', 35), 0, 1, 'C');

        // MONTO DE LA OPERACION
        $mpdf->Ln(3);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, ($datos['ES_DEPOSITO'] == 1 ? 'RECIBIMOS ' : 'ENTREGAMOS ') .  "$" . number_format($datos['MONTO'], 2, '.', ','), 0, 1, 'C');
        $mpdf->SetFont('Helvetica', '', 8);
        $mpdf->MultiCell(60, 4, '(UN MIL DOSCIENTOS 00/100 M.N)', 0, 'C');
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 0, str_repeat('_', 32), 0, 1, 'C');

        // DESGLOSE DE LA OPERACION
        $mpdf->Ln(4);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(30, 10, 'SALDO ANTERIOR:', 0);
        $mpdf->Cell(30, 10, "$" . number_format($datos['SALDO_ANTERIOR'], 2, '.', ','), 2, 0, 'R');
        $mpdf->Ln(8);
        $mpdf->Cell(30, 10, ($datos['ES_DEPOSITO'] == 1 ? 'ABONO' : 'RETIRO') . ' A CUENTA :', 0);
        $mpdf->Cell(30, 10,  "$" . number_format($datos['MONTO'], 2, '.', ','), 2, 0, 'R');
        $mpdf->Ln(8);
        if ($datos['COMISION'] > 0) {
            $mpdf->Cell(30, 10, 'COMISIÓN :', 0);
            $mpdf->Cell(30, 10,  "$" . number_format($datos['COMISION'], 2, '.', ','), 2, 0, 'R');
            $mpdf->Ln(8);
        }
        $nvoSaldo = ($datos['ES_DEPOSITO'] == 1 ? $datos['SALDO_ANTERIOR'] + $datos['MONTO'] : $datos['SALDO_ANTERIOR'] - $datos['MONTO']) - $datos['COMISION'];
        $mpdf->Cell(30, 10, 'SALDO NUEVO: ', 0);
        $mpdf->Cell(30, 10, "$" . number_format($nvoSaldo, 2, '.', ','), 2, 0, 'R');

        // FIRMAS
        $mpdf->Ln(15);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(60, 4, 'Firma de conformidad del cliente', 0, 1, 'C');
        $mpdf->Ln(10);
        $mpdf->Cell(60, 0, str_repeat('_', 25), 0, 1, 'C');

        // FOLIO DE LA OPERACION
        $mpdf->Ln(10);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(60, 4, 'FOLIO DE LA OPERACIÓN', 0, 1, 'C');
        $mpdf->WriteHTML('<barcode code="' . $ticket . '-' . $datos['CODIGO'] . '-' . $datos['MONTO'] . '-' . $datos['COD_EJECUTIVO'] . '" type="C128A" size=".63" height="1" class=""/>');

        // PIE DE PAGINA
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:11px;font-family:Helvetica;">Fecha de impresión: ' . date('Y-m-d H:i:s') . '</div>');

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

    public function CalculadoraView()
    {
        View::render("calculadora_view");
    }
}
