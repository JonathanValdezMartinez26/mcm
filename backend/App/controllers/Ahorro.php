<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\CajaAhorro as CajaAhorroDao;
use \App\models\Ahorro as AhorroDao;
use DateTime;

class Ahorro extends Controller
{
    private $_contenedor;
    private $operacionesNulas = [2, 5]; // [Comisión, Transferencia]
    private $showError = 'const showError = (mensaje) => swal({ text: mensaje, icon: "error" })';
    private $showSuccess = 'const showSuccess = (mensaje) => swal({ text: mensaje, icon: "success" })';
    private $showInfo = 'const showInfo = (mensaje) => swal({ text: mensaje, icon: "info" })';
    private $confirmarMovimiento = 'const confirmarMovimiento = async (titulo, mensaje) => {
        return await swal({ title: titulo, text: mensaje, icon: "warning", buttons: ["No", "Si, continuar"], dangerMode: true })
    }';
    private $validarYbuscar = 'const validarYbuscar = (e, t) => {
        if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
        if (e.keyCode === 13) buscaCliente(t)
    }';
    private $buscaCliente = 'const buscaCliente = (t) => {
        document.querySelector("#btnBskClnt").disabled = true
        const noCliente = document.querySelector("#clienteBuscado").value
         
        if (!noCliente) {
            limpiaDatosCliente()
            document.querySelector("#btnBskClnt").disabled = false
            return showError("Ingrese un número de cliente a buscar.")
        }
        
        consultaServidor("/Ahorro/BuscaContratoAhorro/", { cliente: noCliente }, (respuesta) => {
                limpiaDatosCliente()
                if (!respuesta.success) {
                    if (respuesta.datos && !sinContrato(respuesta.datos)) return
                     
                    limpiaDatosCliente()
                    return showError(respuesta.mensaje)
                }
                 
                llenaDatosCliente(respuesta.datos)
            })
        
        document.querySelector("#btnBskClnt").disabled = false
    }';
    private $getHoy = 'const getHoy = (completo = true) => {
        const hoy = new Date()
        const dd = String(hoy.getDate()).padStart(2, "0")
        const mm = String(hoy.getMonth() + 1).padStart(2, "0")
        const yyyy = hoy.getFullYear()
        const r = dd + "/" + mm + "/" + yyyy
        return completo ? r  + " " + hoy.getHours().toString().padStart(2, "0") + ":" + hoy.getMinutes().toString().padStart(2, "0") + ":" + hoy.getSeconds().toString().padStart(2, "0") : r
    }';
    private $soloNumeros = 'const soloNumeros = (e) => {
        valKD = false
        if (
            !(e.key >= "0" && e.key <= "9") &&
            e.key !== "." &&
            e.key !== "Backspace" &&
            e.key !== "Delete" &&
            e.key !== "ArrowLeft" &&
            e.key !== "ArrowRight" &&
            e.key !== "Tab"
        ) e.preventDefault()
        if (e.key === "." && e.target.value.includes(".")) e.preventDefault()
        valKD = true
    }';
    private $numeroLetras = 'const numeroLetras = (numero) => {
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
        return primeraMayuscula(convertir(parteEntera)) + (numero == 1 ? " peso " : " pesos ") + parteDecimal + "/100 M.N."
    }';
    private $primeraMayuscula = 'const primeraMayuscula = (texto) => texto.charAt(0).toUpperCase() + texto.slice(1)';
    private $muestraPDF = <<<script
    const muestraPDF = (titulo, ruta) => {
        let plantilla = '<!DOCTYPE html>'
            plantilla += '<html lang="es">'
            plantilla += '<head>'
            plantilla += '<meta charset="UTF-8">'
            plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            plantilla += '<link rel="shortcut icon" href="" + host + "/img/logo.png">'
            plantilla += '<title>' + titulo + '</title>'
            plantilla += '</head>'
            plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
            plantilla += '<iframe src="' + ruta + '" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
            plantilla += '</body>'
            plantilla += '</html>'
        
            const blob = new Blob([plantilla], { type: 'text/html' })
            const url = URL.createObjectURL(blob)
            window.open(url, '_blank')
    }
    script;
    private $imprimeTicket = <<<script
    const imprimeTicket = (ticket, sucursal = '') => {
        const host = window.location.origin
        const titulo = 'Ticket: ' + ticket
        const ruta = host + '/Ahorro/Ticket/?'
        + 'ticket=' + ticket
        + '&sucursal=' + sucursal
        + '&copiaCliente=true'
        
        muestraPDF(titulo, ruta)
    }
    script;
    private $imprimeContrato = <<<script
    const imprimeContrato = (numero_contrato, producto = 1) => {
        if (!numero_contrato) return
        const host = window.location.origin
        const titulo = 'Contrato ' + numero_contrato
        const ruta = host
            + '/Ahorro/Contrato/?'
            + 'contrato=' + numero_contrato
            + '&producto=' + producto
         
        muestraPDF(titulo, ruta)
    }
    script;
    private $sinContrato = <<<script
    const sinContrato = (datosCliente) => {
        if (datosCliente["NO_CONTRATOS"] == 0) {
            swal({
                title: "Cuenta de ahorro corriente",
                text: "El cliente " + datosCliente['CDGCL'] + " no tiene una cuenta de ahorro.\\n¿Desea aperturar una cuenta de ahorro en este momento?",
                icon: "info",
                buttons: ["No", "Sí"],
                dangerMode: true
            }).then((abreCta) => {
                if (abreCta) {
                    window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + datosCliente['CDGCL']
                    return
                }
            })
            return false
        }
        const msj2 = (typeof mEdoCta !== 'undefined') ? "No podemos generar un estado de cuenta para el cliente  " + datosCliente['CDGCL'] + ", porque este no ha concluido con su proceso de apertura de la cuenta de ahorro corriente.\\n¿Desea completar el proceso en este momento?" 
        : "El cliente " + datosCliente['CDGCL'] + " no ha completado el proceso de apertura de la cuenta de ahorro.\\n¿Desea completar el proceso en este momento?"
        if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
            swal({
                title: "Cuenta de ahorro corriente",
                text: msj2,
                icon: "info",
                buttons: ["No", "Sí"],
                dangerMode: true
            }).then((abreCta) => {
                if (abreCta) {
                    window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + datosCliente['CDGCL']
                    return
                }
            })
            return false
        }
        return true
    }
    script;
    private $addParametro = 'const addParametro = (parametros, newParametro, newValor) => {
        parametros.push({ name: newParametro, value: newValor })
    }';
    private $consultaServidor = 'const consultaServidor = (url, datos, fncOK, metodo = "POST") => {
        const espera = swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
        $.ajax({
            type: metodo,
            url: url,
            data: datos,
            success: (res) => {
                try {
                    res = JSON.parse(res)
                } catch (error) {
                    console.error(error)
                    res =  {
                        success: false,
                        mensaje: "Ocurrió un error al procesar la respuesta del servidor."
                    }
                }
                swal.close()
                fncOK(res)
            },
            error: (error) => {
                console.error(error)
                showError("Ocurrió un error al procesar la solicitud.")
                msjW.close()
            }
        })
    }';
    private $parseaNumero = 'const parseaNumero = (numero) => parseFloat(numero.replace(/[^0-9.-]/g, "")) || 0';
    private $formatoMoneda = 'const formatoMoneda = (numero) => parseFloat(numero).toLocaleString("es-MX", { minimumFractionDigits: 2 })';
    private $limpiaMontos = 'const limpiaMontos = (datos, campos = []) => {
        datos.forEach(dato => {
            if (campos.includes(dato.name)) {
                dato.value = parseaNumero(dato.value)
            }
        })
    }';

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    private function GetExtraHeader($titulo)
    {
        return <<<html
        <title>$titulo</title>
        <link rel="shortcut icon" href="/img/logo.png">
        html;
    }

    //********************AHORRO CORRIENTE********************//
    // Apertura de contratos para cuentas de ahorro corriente
    public function ContratoCuentaCorriente()
    {

        $saldosMM = CajaAhorroDao::GetSaldoMinimoApertura($_SESSION['cdgco']);
        $saldoMinimoApertura = $saldosMM['MONTO_MINIMO'];
        $costoInscripcion = 100;

        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
             
            const saldoMinimoApertura = $saldoMinimoApertura
            const costoInscripcion = $costoInscripcion
            const montoMaximo = 1000000
            const txtGuardaContrato = "GUARDAR DATOS Y PROCEDER AL COBRO"
            const txtGuardaPago = "REGISTRAR DEPÓSITO DE APERTURA"
            let valKD = false
             
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->imprimeContrato}
            {$this->addParametro}
            {$this->consultaServidor}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
             
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado").value
                limpiaDatosCliente()
                 
                if (!noCliente) return showError("Ingrese un número de cliente a buscar.")
                 
                consultaServidor("/Ahorro/BuscaCliente/", { cliente: noCliente }, async (respuesta) => {
                    if (!respuesta.success) {
                        if (!respuesta.datos) {
                            limpiaDatosCliente()
                            return showError(respuesta.mensaje)
                        }
                         
                        const datosCliente = respuesta.datos
                        document.querySelector("#btnGeneraContrato").style.display = "none"
                        document.querySelector("#contratoOK").value = datosCliente.CONTRATO
                        if (datosCliente['NO_CONTRATOS'] >= 0 && datosCliente.CONTRATO_COMPLETO == 0) {
                                await showInfo("La apertura del contrato no ha concluido, realice el depósito de apertura.")
                                document.querySelector("#fecha_pago").value = getHoy()
                                document.querySelector("#contrato").value = datosCliente.CONTRATO
                                document.querySelector("#codigo_cl").value = datosCliente.CDGCL
                                document.querySelector("#nombre_cliente").value = datosCliente.NOMBRE
                                document.querySelector("#mdlCurp").value = datosCliente.CURP
                                $("#modal_agregar_pago").modal("show")
                                document.querySelector("#chkCreacionContrato").classList.add("green")
                                document.querySelector("#chkCreacionContrato").classList.add("fa-check")
                                document.querySelector("#chkCreacionContrato").classList.remove("red")
                                document.querySelector("#chkCreacionContrato").classList.remove("fa-times")
                                document.querySelector("#lnkContrato").style.cursor = "pointer"
                                document.querySelector("#chkPagoApertura").classList.remove("green")
                                document.querySelector("#chkPagoApertura").classList.remove("fa-check")
                                document.querySelector("#chkPagoApertura").classList.add("fa-times")
                                document.querySelector("#chkPagoApertura").classList.add("red")
                                document.querySelector("#btnGuardar").innerText = txtGuardaPago
                                document.querySelector("#btnGeneraContrato").style.display = "block"
                        }
                         
                        if (datosCliente['NO_CONTRATOS'] >= 0 && datosCliente.CONTRATO_COMPLETO == 1) {
                            await showInfo(respuesta.mensaje)
                            document.querySelector("#chkCreacionContrato").classList.remove("red")
                            document.querySelector("#chkCreacionContrato").classList.remove("fa-times")
                            document.querySelector("#chkCreacionContrato").classList.add("green")
                            document.querySelector("#chkCreacionContrato").classList.add("fa-check")
                            document.querySelector("#lnkContrato").style.cursor = "pointer"
                            document.querySelector("#chkPagoApertura").classList.remove("red")
                            document.querySelector("#chkPagoApertura").classList.remove("fa-times")
                            document.querySelector("#chkPagoApertura").classList.add("green")
                            document.querySelector("#chkPagoApertura").classList.add("fa-check")
                        }
                         
                        consultaServidor("/Ahorro/GetBeneficiarios/", { contrato: datosCliente.CONTRATO }, (respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)
                             
                            const beneficiarios = respuesta.datos
                            for (let i = 0; i < beneficiarios.length; i++) {
                                document.querySelector("#beneficiario_" + (i + 1)).value = beneficiarios[i].NOMBRE
                                document.querySelector("#parentesco_" + (i + 1)).value = beneficiarios[i].CDGCT_PARENTESCO
                                document.querySelector("#porcentaje_" + (i + 1)).value = beneficiarios[i].PORCENTAJE
                                document.querySelector("#btnBen" + (i + 1)).disabled = true
                                document.querySelector("#parentesco_" + (i + 1)).disabled = true
                                document.querySelector("#porcentaje_" + (i + 1)).disabled = true
                                document.querySelector("#ben" + (i + 1)).style.opacity = "1"
                            }
                        })
                    }
                     
                    const datosCL = respuesta.datos
                     
                    document.querySelector("#fechaRegistro").value = datosCL.FECHA_REGISTRO
                    document.querySelector("#noCliente").value = noCliente
                    document.querySelector("#nombre").value = datosCL.NOMBRE
                    document.querySelector("#curp").value = datosCL.CURP
                    document.querySelector("#edad").value = datosCL.EDAD
                    document.querySelector("#direccion").value = datosCL.DIRECCION
                    document.querySelector("#marcadores").style.opacity = "1"
                    noCliente.value = ""
                    if (respuesta.success) habilitaBeneficiario(1, true)
                })
            }
             
            const habilitaBeneficiario = (numBeneficiario, habilitar) => {
                document.querySelector("#beneficiario_" + numBeneficiario).disabled = !habilitar
                document.querySelector("#tasa").disabled = false
                document.querySelector("#sucursal").disabled = false
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#AddPagoApertura").reset()
                document.querySelector("#registroInicialAhorro").reset()
                document.querySelector("#chkCreacionContrato").classList.remove("green")
                document.querySelector("#chkCreacionContrato").classList.remove("fa-check")
                document.querySelector("#chkCreacionContrato").classList.add("red")
                document.querySelector("#chkCreacionContrato").classList.add("fa-times")
                document.querySelector("#lnkContrato").style.cursor = "default"
                document.querySelector("#chkPagoApertura").classList.remove("green")
                document.querySelector("#chkPagoApertura").classList.remove("fa-check")
                document.querySelector("#chkPagoApertura").classList.add("red")
                document.querySelector("#chkPagoApertura").classList.add("fa-times")
                document.querySelector("#fechaRegistro").value = ""
                document.querySelector("#noCliente").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#curp").value = ""
                document.querySelector("#edad").value = ""
                document.querySelector("#direccion").value = ""
                habilitaBeneficiario(1, false)
                document.querySelector("#ben2").style.opacity = "0"
                document.querySelector("#ben3").style.opacity = "0"
                document.querySelector("#btnGeneraContrato").style.display = "none"
                document.querySelector("#btnGuardar").innerText = txtGuardaContrato
                document.querySelector("#marcadores").style.opacity = "0"
                document.querySelector("#tasa").disabled = true
                document.querySelector("#sucursal").disabled = true
                document.querySelector("#contratoOK").value = ""
            }
            
            const generaContrato = async (e) => {
                e.preventDefault()
                const btnGuardar = document.querySelector("#btnGuardar")
                if (btnGuardar.innerText === txtGuardaPago) return $("#modal_agregar_pago").modal("show")
                 
                document.querySelector("#fecha_pago").value = getHoy()
                document.querySelector("#contrato").value = ""
                document.querySelector("#codigo_cl").value = document.querySelector("#noCliente").value
                document.querySelector("#nombre_cliente").value = document.querySelector("#nombre").value
                document.querySelector("#mdlCurp").value = document.querySelector("#curp").value
                    
                await showInfo("Debe registrar el depósito por apertura de cuenta.")
                btnGuardar.innerText = txtGuardaPago
                $("#modal_agregar_pago").modal("show")
            }
                        
            const pagoApertura = (e) => {
                e.preventDefault()
                if (parseaNumero(document.querySelector("#deposito").value) < saldoMinimoApertura) return showError("El saldo inicial no puede ser menor a " + saldoMinimoApertura.toLocaleString("es-MX", {style:"currency", currency:"MXN"}) + ".")
                 
                confirmarMovimiento(
                    "Cuenta de ahorro corriente",
                    "¿Está segura de continuar con la apertura de la cuenta de ahorro del cliente: " +
                        document.querySelector("#nombre").value +
                        "?"
                ).then((continuar) => {
                    if (!continuar) return
                
                    const noCredito = document.querySelector("#noCliente").value
                    const datosContrato = $("#registroInicialAhorro").serializeArray()
                    addParametro(datosContrato, "credito", noCredito)
                    addParametro(datosContrato, "ejecutivo", "{$_SESSION['usuario']}")
                     
                    if (document.querySelector("#contrato").value !== "") return regPago(document.querySelector("#contrato").value)
                    
                    consultaServidor("/Ahorro/AgregaContratoAhorro/", $.param(datosContrato), (respuesta) => {
                        if (!respuesta.success) {
                            console.error(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                         
                        regPago(respuesta.datos.contrato)
                    })
                })
            }
             
            const regPago = (contrato) => {
                const datos = $("#AddPagoApertura").serializeArray()
                limpiaMontos(datos, ["deposito", "inscripcion", "saldo_inicial"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                addParametro(datos, "contrato", contrato)
                 
                consultaServidor("/Ahorro/PagoApertura/", $.param(datos), (respuesta) => {
                    if (!respuesta.success) return showError(respuesta.mensaje)
                
                    showSuccess(respuesta.mensaje)
                    .then(() => {
                        document.querySelector("#registroInicialAhorro").reset()
                        document.querySelector("#AddPagoApertura").reset()
                        $("#modal_agregar_pago").modal("hide")
                        limpiaDatosCliente()
                        imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco']}")
                    
                        showSuccess("Se ha generado el contrato: " + contrato + ".")
                        .then(() => {
                            imprimeContrato(contrato, 1)
                        })
                    })
                })
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                let monto = parseaNumero(e.target.value)
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0.")
                }
                 
                if (monto > montoMaximo) {
                    e.preventDefault()
                    monto = montoMaximo
                    e.target.value = monto
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(e.target.value))
                calculaSaldoFinal(e)
            }
             
            const calculaSaldoFinal = (e) => {
                const monto = parseaNumero(e.target.value)
                document.querySelector("#deposito").value = formatoMoneda(monto)
                const saldoInicial = (monto - parseaNumero(document.querySelector("#inscripcion").value))
                document.querySelector("#saldo_inicial").value = formatoMoneda(saldoInicial > 0 ? saldoInicial : 0)
                document.querySelector("#monto_letra").value = primeraMayuscula(numeroLetras(monto))
                    
                if (saldoInicial < (saldoMinimoApertura - costoInscripcion)) {
                    document.querySelector("#saldo_inicial").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#registraDepositoInicial").disabled = true
                } else {
                    document.querySelector("#saldo_inicial").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                    document.querySelector("#registraDepositoInicial").disabled = false
                }
            }
             
            const camposLlenos = (e) => {
                const val = () => {
                    let porcentaje = 0
                    for (let i = 1; i <= 3; i++) {
                        document.querySelector("#beneficiario_" + i).value = document.querySelector("#beneficiario_" + i).value.toUpperCase()
                        porcentaje += parseFloat(document.querySelector("#porcentaje_" + i).value) || 0
                        if (document.querySelector("#ben" + i).style.opacity === "1") {
                            if (!document.querySelector("#beneficiario_" + i).value) {
                                document.querySelector("#parentesco_" + i).disabled = true
                                document.querySelector("#porcentaje_" + i).disabled = true
                                document.querySelector("#btnBen" + i).disabled = true
                                return false
                            }
                            document.querySelector("#parentesco_" + i).disabled = false
                             
                            if (document.querySelector("#parentesco_" + i).selectedIndex === 0) {
                                document.querySelector("#porcentaje_" + i).disabled = true
                                document.querySelector("#btnBen" + i).disabled = true
                                return false
                            }
                            document.querySelector("#porcentaje_" + i).disabled = false
                             
                            if (!document.querySelector("#porcentaje_" + i).value) {
                                document.querySelector("#btnBen" + i).disabled = true
                                return false
                            }
                            document.querySelector("#btnBen" + i).disabled = porcentaje >= 100 && document.querySelector("#btnBen1").querySelector("i").classList.contains("fa-plus")
                        }
                    }
                    
                    if (porcentaje > 100) {
                        e.preventDefault()
                        e.target.value = ""
                        showError("La suma de los porcentajes no puede ser mayor a 100%.")
                    }
                     
                    return porcentaje === 100
                }
                 
                if (e.target.tagName === "SELECT") actualizarOpciones(e.target)
                 
                document.querySelector("#btnGeneraContrato").style.display = !val() ? "none" : "block"
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
                 
                document.querySelector("#btnGeneraContrato").style.display = porcentaje !== 100 ? "none" : "block"
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
                camposLlenos(event)
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
             
            const reImprimeContrato = (e) => {
                const c = document.querySelector('#contratoOK').value
                if (!c) {
                    e.preventDefault()
                    return
                }
                 
                imprimeContrato(c)
            }
        </script>
        html;


        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro($this->__usuario);
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}'>{$sucursales['NOMBRE']}</option>";
            $suc_eje = $sucursales['CODIGO'];
        }

        $ejecutivos = CajaAhorroDao::GetEjecutivosSucursal($suc_eje);
        $opcEjecutivos = "";
        foreach ($ejecutivos as $ejecutivos) {
            $opcEjecutivos .= "<option value='{$ejecutivos['ID_EJECUTIVO']}'>{$ejecutivos['EJECUTIVO']}</option>";
        }
        $opcEjecutivos .= "<option value='{$this->__usuario}'>{$this->__nombre} - CAJER(A)</option>";

        $parentescos = CajaAhorroDao::GetCatalogoParentescos();
        $opcParentescos = "<option value='' disabled selected>Seleccionar</option>";
        foreach ($parentescos as $parentesco) {
            $opcParentescos .= "<option value='{$parentesco['CODIGO']}'>{$parentesco['DESCRIPCION']}</option>";
        }


        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Ahorro Corriente")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        view::set('costoInscripcion', $costoInscripcion);
        View::set('fecha', date('d/m/Y H:i:s'));
        view::set('opcParentescos', $opcParentescos);
        view::set('sucursales', $opcSucursales);
        view::set('ejecutivos', $opcEjecutivos);
        View::render("caja_menu_contrato_ahorro");
    }

    public function BuscaCliente()
    {
        $datos = CajaAhorroDao::BuscaClienteNvoContrato($_POST);
        echo $datos;
    }

    public function GetBeneficiarios()
    {
        $beneficiarios = CajaAhorroDao::GetBeneficiarios($_POST['contrato']);
        echo $beneficiarios;
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

    // Movimientos sobre cuentas de ahorro corriente //
    public function CuentaCorriente()
    {
        $saldoMinimoApertura = 100;
        $montoMaximoRetiro = 9999.99;

        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
             
            const saldoMinimoApertura = $saldoMinimoApertura
            const montoMaximoRetiro = $montoMaximoRetiro
            const montoMaximoDeposito = 100000
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->sinContrato}
            {$this->addParametro}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->consultaServidor}
            {$this->limpiaMontos}
         
            const llenaDatosCliente = (datosCliente) => {
                document.querySelector("#nombre").value = datosCliente.NOMBRE
                document.querySelector("#curp").value = datosCliente.CURP
                document.querySelector("#contrato").value = datosCliente.CONTRATO
                document.querySelector("#cliente").value = datosCliente.CDGCL
                document.querySelector("#saldoActual").value = formatoMoneda(datosCliente.SALDO)
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
            }
             
            const validaMonto = () => {
                if (!valKD) return
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseaNumero(montoIngresado.value)
                 
                if (!document.querySelector("#deposito").checked && monto > montoMaximoRetiro) {
                    monto = montoMaximoRetiro
                    swal({
                        title: "Cuenta de ahorro corriente",
                        text: "Para retiros mayores a " + montoMaximoRetiro.toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + " es necesario realizar una solicitud de retiro\\nDesea generar una solicitud de retiro ahora?.",
                        icon: "info",
                        buttons: ["No", "Sí"],
                        dangerMode: true
                    }).then((regRetiro) => {
                        if (regRetiro) {
                            window.location.href = "/Ahorro/SolicitudRetiroCuentaCorriente/?cliente=" + document.querySelector("#cliente").value
                            return
                        }
                    })
                    montoIngresado.value = monto
                }
                 
                if (document.querySelector("#deposito").checked && monto > montoMaximoDeposito) {
                    monto = montoMaximoDeposito
                    montoIngresado.value = monto
                }
                 
                const valor = montoIngresado.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    montoIngresado.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                if (montoIngresado.id === "mdlDeposito_inicial") return calculaSaldoInicial(e)
                 
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(montoIngresado.value))
                if (document.querySelector("#deposito").checked || document.querySelector("#retiro").checked) calculaSaldoFinal()
            }
             
            const calculaSaldoInicial = (e) => {
                const monto = parseaNumero(e.target.value)
                document.querySelector("#mdlDeposito").value = formatoMoneda(monto)
                const saldoInicial = (monto - parseaNumero(document.querySelector("#mdlInscripcion").value)).toFixed(2)
                document.querySelector("#mdlSaldo_inicial").value = formatoMoneda(saldoInicial > 0 ? saldoInicial : 0)
                document.querySelector("#mdlDeposito_inicial_letra").value = primeraMayuscula(numeroLetras(monto))
                    
                if (saldoInicial < saldoMinimoApertura) {
                    document.querySelector("#mdlSaldo_inicial").setAttribute("style", "color: red")
                    document.querySelector("#mdlTipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#mdlRegistraDepositoInicial").disabled = true
                } else {
                    document.querySelector("#mdlSaldo_inicial").removeAttribute("style")
                    document.querySelector("#mdlTipSaldo").setAttribute("style", "opacity: 0%;")
                    document.querySelector("#mdlRegistraDepositoInicial").disabled = false
                }
            }
             
            const calculaSaldoFinal = () => {
                const esDeposito = document.querySelector("#deposito").checked
                const saldoActual = parseaNumero(document.querySelector("#saldoActual").value)
                const monto = parseaNumero(document.querySelector("#monto").value)
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                document.querySelector("#saldoFinal").value = formatoMoneda(esDeposito ? saldoActual + monto : saldoActual - monto)
                compruebaSaldoFinal()
            }
             
            const cambioMovimiento = (e) => {
                document.querySelector("#monto").disabled = false
                const esDeposito = document.querySelector("#deposito").checked
                document.querySelector("#simboloOperacion").innerText = esDeposito ? "+" : "-"
                document.querySelector("#descOperacion").innerText = (esDeposito ? "Depósito" : "Retiro") + " a cuenta ahorro corriente"
                document.querySelector("#monto").max = esDeposito ? montoMaximoDeposito : montoMaximoRetiro
                valKD = true
                validaMonto()
                calculaSaldoFinal()
            }
             
            const compruebaSaldoFinal = () => {
                const saldoFinal = parseaNumero(document.querySelector("#saldoFinal").value)
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(saldoFinal >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) > 0)
                
            }
             
            const registraOperacion = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                 
                if (!document.querySelector("#deposito").checked && !document.querySelector("#retiro").checked) return showError("Seleccione el tipo de operación a realizar.")
                
                datos.forEach((dato) => {
                    if (dato.name === "esDeposito") dato.value = document.querySelector("#deposito").checked
                })
                 
                confirmarMovimiento(
                    "Confirmación de movimiento de ahorro corriente",
                    "¿Está segur(a) de continuar con el registro de un "
                    + (document.querySelector("#deposito").checked ? "depósito" : "retiro")
                    + " de cuanta ahorro corriente por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")?"
                ).then((continuar) => {
                    if (!continuar) return
                    consultaServidor("/Ahorro/RegistraOperacion/", $.param(datos), (respuesta) => {
                            if (!respuesta.success){
                                console.log(respuesta.error)
                                return showError(respuesta.mensaje)
                            }
                            showSuccess(respuesta.mensaje).then(() => {
                                imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco']}")
                                limpiaDatosCliente()
                            })
                        })
                })
            }
        </script>
        html;

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Ahorro Corriente")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        view::set('montoMaximoRetiro', $montoMaximoRetiro);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_menu_ahorro");
    }

    public function BuscaContratoAhorro()
    {
        $datos = CajaAhorroDao::BuscaContratoAhorro($_POST);
        echo $datos;
    }

    public function RegistraOperacion()
    {
        $resutado =  CajaAhorroDao::RegistraOperacion($_POST);
        echo $resutado;
    }

    // Registro de solicitudes de retiros mayores de cuentas de ahorro //
    public function SolicitudRetiroCuentaCorriente()
    {
        $montoMinimoRetiro = 10000;
        $montoMaximoExpress = 49999.99;
        $montoMaximoRetiro = 1000000;

        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
         
            const montoMinimo = $montoMinimoRetiro
            const montoMaximoExpress = $montoMaximoExpress
            const montoMaximoRetiro = $montoMaximoRetiro
            let valKD = false
         
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->soloNumeros}
            {$this->primeraMayuscula}
            {$this->numeroLetras}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->addParametro}
            {$this->sinContrato}
            {$this->getHoy}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
             
            const llenaDatosCliente = (datosCliente) => {
                if (parseaNumero(datosCliente.SALDO) < montoMinimo) {
                    swal({
                        title: "Retiro de cuenta corriente",
                        text: "El saldo de la cuenta es menor al monto mínimo para retiros express (" + montoMinimo.toLocaleString("es-MX", {style:"currency", currency:"MXN"}) + ").\\n¿Desea realizar un retiro simple?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    }).then((retSimple) => {
                        if (retSimple) {
                            window.location.href = "/Ahorro/CuentaCorriente/?cliente=" + datosCliente.CDGCL
                            return
                        }
                    })
                    return
                }
                 
                document.querySelector("#nombre").value = datosCliente.NOMBRE
                document.querySelector("#curp").value = datosCliente.CURP
                document.querySelector("#contrato").value = datosCliente.CONTRATO
                document.querySelector("#cliente").value = datosCliente.CDGCL
                document.querySelector("#saldoActual").value = formatoMoneda(datosCliente.SALDO)
                document.querySelector("#monto").disabled = false
                document.querySelector("#saldoFinal").value = formatoMoneda(datosCliente.SALDO)
                document.querySelector("#express").disabled = false
                document.querySelector("#programado").disabled = false
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
                document.querySelector("#express").disabled = true
                document.querySelector("#programado").disabled = true
                document.querySelector("#fecha_retiro_hide").setAttribute("style", "display: none;")
                document.querySelector("#fecha_retiro").removeAttribute("style")
            }
             
            const validaMonto = () => {
                document.querySelector("#express").disabled = false
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseaNumero(montoIngresado.value) || 0
                 
                if (monto > montoMaximoExpress) {
                    document.querySelector("#programado").checked = true
                    document.querySelector("#express").disabled = true
                    cambioMovimiento()
                }
                 
                if (monto > montoMaximoRetiro) {
                    monto = montoMaximoRetiro
                    montoIngresado.value = monto
                }
                                  
                document.querySelector("#monto_letra").value = primeraMayuscula(numeroLetras(monto))
                const saldoActual = parseaNumero(document.querySelector("#saldoActual").value)
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                const saldoFinal = (saldoActual - monto)
                compruebaSaldoFinal(saldoFinal)
                document.querySelector("#saldoFinal").value = formatoMoneda(saldoFinal)
            }
             
            const valSalMin = () => {
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseFloat(montoIngresado.value) || 0
                 
                if (monto < montoMinimo) {
                    monto = montoMinimo
                    swal({
                        title: "Retiro de cuenta corriente",
                        text: "El monto mínimo para retiros express es de " + montoMinimo.toLocaleString("es-MX", {
                            style: "currency",
                            currency: "MXN"
                        }) + ", para un monto menor debe realizar el retiro de manera simple.\\n¿Desea realizar el retiro de manera simple?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    }).then((retSimple) => {
                        if (retSimple) {
                            window.location.href = "/Ahorro/CuentaCorriente/?cliente=" + document.querySelector("#cliente").value
                            return
                        }
                    })
                }
            }
             
            const cambioMovimiento = (e) => {
                const express = document.querySelector("#express").checked
                
                if (express) {
                    document.querySelector("#fecha_retiro").removeAttribute("style")
                    document.querySelector("#fecha_retiro_hide").setAttribute("style", "display: none;")
                    document.querySelector("#fecha_retiro").value = getHoy()
                    return
                }
                
                document.querySelector("#fecha_retiro_hide").removeAttribute("style")
                document.querySelector("#fecha_retiro").setAttribute("style", "display: none;")
            }
             
            const compruebaSaldoFinal = () => {
                const saldoFinal = parseaNumero(document.querySelector("#saldoFinal").value)
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(saldoFinal >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) >= montoMinimo && parseaNumero(document.querySelector("#montoOperacion").value) < montoMaximoRetiro)
            }
             
            const pasaFecha = (e) => {
                const fechaSeleccionada = new Date(e.target.value)
                if (fechaSeleccionada.getDay() === 5 || fechaSeleccionada.getDay() === 6) {
                    showError("No se pueden realizar retiros los fines de semana.")
                    const f = getHoy(false).split("/")
                    e.target.value = f[2] + "-" + f[1] + "-" + f[0]
                    return
                }
                const f = document.querySelector("#fecha_retiro_hide").value.split("-")
                document.querySelector("#fecha_retiro").value = f[2] + "/" + f[1] + "/" + f[0]
            }
             
            const registraSolicitud = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                addParametro(datos, "retiroExpress", document.querySelector("#express").checked)
                 
                confirmarMovimiento(
                    "Confirmación de movimiento ahorro corriente",
                    "¿Está segur(a) de continuar con el registro de un retiro "
                    + (document.querySelector("#express").checked ? "express" : "programado")
                    + ", por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")?"
                ).then((continuar) => {
                    if (!continuar) return
                    
                    consultaServidor("/Ahorro/RegistraSolicitud/", $.param(datos), (respuesta) => {
                            if (!respuesta.success) {
                                console.log(respuesta.error)
                                return showError(respuesta.mensaje)
                            }
                            showSuccess(respuesta.mensaje).then(() => {
                                document.querySelector("#registroOperacion").reset()
                                limpiaDatosCliente()
                            })
                        })
                })
            }
        </script>
        html;

        $fechaMax = new DateTime();
        for ($i = 0; $i < 7; $i++) {
            $fechaMax->modify('+1 day');
            if ($fechaMax->format('N') >= 6 || $fechaMax->format('N') === 0) $fechaMax->modify('+1 day');
        }

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Solicitud de Retiro")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('montoMinimoRetiro', $montoMinimoRetiro);
        View::set('montoMaximoExpress', $montoMaximoExpress);
        View::set('montoMaximoRetiro', $montoMaximoRetiro);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::set('fechaInput', date('Y-m-d'));
        View::set('fechaInputMax', $fechaMax->format('Y-m-d'));
        View::render("caja_menu_retiro_ahorro");
    }

    public function RegistraSolicitud()
    {
        $datos = CajaAhorroDao::RegistraSolicitud($_POST);
        echo $datos;
    }

    // Historial de solicitudes de retiros de cuentas de ahorro //
    public function HistorialSolicitudRetiroCuentaCorriente()
    {
        $extraFooter = <<<html
        <script>
            $(document).ready(() => {
                $("#muestra-cupones").tablesorter()
                $("#muestra-cupones").DataTable({
                    lengthMenu: [
                        [10, 40, -1],
                        [10, 40, "Todos"]
                    ],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: 0
                        }
                    ],
                    order: false
                })
            
                $("#muestra-cupones input[type=search]").keyup(() => {
                    $("#example")
                        .DataTable()
                        .search(jQuery.fn.DataTable.ext.type.search.html(this.value))
                        .draw()
                })
            })
             
            const imprimeExcel = () => {
                alert("Exportando a Excel")
                // window.location.href = "/Ahorro/ExportaExcel/"
            }
        </script>
        html;

        $detalles = CajaAhorroDao::HistoricoSolicitudRetiro();

        $tabla = "";

        foreach ($detalles as $key1 => $detalle) {
            $tabla .= "<tr>";
            foreach ($detalle as $key2 => $valor) {
                $v = $valor;
                if ($key2 === "MONTO") $v = "$ " . number_format($valor, 2);

                $tabla .= "<td style='vertical-align: middle;'>$v</td>";
            }

            $tabla .= "</tr>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial de solicitudes de retiro")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("caja_menu_solicitud_retiro_historial");
    }

    //********************INVERSIONES********************//
    // Apertura de contratos para cuentas de inversión
    public function ContratoInversion()
    {
        $saldoMinimoApertura = CajaAhorroDao::GetSaldoMinimoInversion();
        $tasas = CajaAhorroDao::GetTasas();
        $tasas = $tasas ? json_encode($tasas) : "[]";

        $extraFooter = <<<html
        <script>
            const saldoMinimoApertura = $saldoMinimoApertura
            const montoMaximo = 1000000
            let tasasDisponibles
            try {
                tasasDisponibles = JSON.parse('$tasas')
            } catch (error) {
                console.error(error)
                tasasDisponibles = []
            }
            let valKD = false
         
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->imprimeContrato}
            {$this->sinContrato}
            {$this->addParametro}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
             
            const llenaDatosCliente = (datos) => {
                const saldoActual = parseaNumero(datos.SALDO)
                         
                document.querySelector("#nombre").value = datos.NOMBRE
                document.querySelector("#curp").value = datos.CURP
                document.querySelector("#contrato").value = datos.CONTRATO
                document.querySelector("#cliente").value = datos.CDGCL
                document.querySelector("#saldoActual").value = formatoMoneda(saldoActual)
                document.querySelector("#saldoFinal").value = formatoMoneda(saldoActual)
                if (saldoActual >= saldoMinimoApertura) return document.querySelector("#monto").disabled = false
                
                showError("No es posible hacer la apertura de inversión.\\nEl saldo mínimo de apertura es de " + saldoMinimoApertura.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }) + 
                    "\\nEl saldo actual del cliente es de " + saldoActual.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }))
            }
            
            const limpiaDatosCliente = () => {
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
                document.querySelector("#plazo").innerHTML = ""
                document.querySelector("#plazo").disabled = true
                habiltaEspecs()
            }
            
            const validaDeposito = (e) => {
                if (!valKD) return
                
                let monto = parseaNumero(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                }
                
                if (monto > montoMaximo) {
                    e.preventDefault()
                    monto = montoMaximo
                    e.target.value = monto
                }
                
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseaNumero(valor[0] + "." + valor[1].substring(0, 2))
                }
                 
                const saldoFinal = parseaNumero(document.querySelector("#saldoActual").value) - monto
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                document.querySelector("#saldoFinal").value = formatoMoneda(saldoFinal < 0 ? 0 : saldoFinal)
                document.querySelector("#monto_letra").value = numeroLetras(monto)
                compruebaSaldoFinal(saldoFinal)
                habiltaEspecs(monto)
                compruebaSaldoMinimo()
            }
            
            const compruebaSaldoMinimo = () => {
                const monto = parseaNumero(document.querySelector("#monto").value)
                let mMax = 0
                
                const tasas =  tasasDisponibles
                .filter(tasa => {
                    const r = monto >= saldoMinimoApertura && tasa.MONTO_MINIMO <= monto 
                    mMax = r ? tasa.MONTO_MINIMO : mMax
                    return r
                })
                .filter(tasa => tasa.MONTO_MINIMO == mMax)
                 
                if (tasas.length > 0) {
                    document.querySelector("#plazo").innerHTML = tasas.map(tasa => "<option value='" + tasa.CODIGO + "'>" + tasa.PLAZO + "</option>").join("")
                    document.querySelector("#plazo").disabled = false
                    cambioPlazo()
                    return 
                }
                 
                document.querySelector("#plazo").innerHTML = ""
                document.querySelector("#plazo").disabled = true
                document.querySelector("#rendimiento").value = ""
            }
             
            const cambioPlazo = () => {
                const plazo = document.querySelector("#plazo").value
                const tasa = tasasDisponibles.find(tasa => tasa.CODIGO == plazo)
                if (tasa) {
                    document.querySelector("#rendimiento").value = formatoMoneda(parseaNumero(document.querySelector("#monto").value) * parseaNumero(tasa.TASA) / 100)
                    document.querySelector("#leyendaRendimiento").innerText = "* Rendimiento calculado con una tasa anual fija del " + tasa.TASA + "%"
                    return
                }
                 
                document.querySelector("#rendimiento").value = ""
                document.querySelector("#leyendaRendimiento").innerText = ""
            }
             
            const compruebaSaldoFinal = saldoFinal => {
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                habilitaBoton()
            }
             
            const habilitaBoton = (e) => {
                if (e && e.target.id === "plazo") cambioPlazo()
                document.querySelector("#btnRegistraOperacion").disabled = !(parseaNumero(document.querySelector("#saldoFinal").value) >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) >= saldoMinimoApertura)
            }
             
            const habiltaEspecs = (monto = parseaNumero(document.querySelector("#monto").value)) => {
                document.querySelector("#plazo").disabled = !(monto >= saldoMinimoApertura)
                document.querySelector("#renovacion").disabled = !(monto >= saldoMinimoApertura)
                 
                if (monto < saldoMinimoApertura) {
                    document.querySelector("#plazo").innerHTML = ""
                    document.querySelector("#rendimiento").value = ""
                    document.querySelector("#renovacion").selectedIndex = 0
                }
            }
            
            const registraOperacion = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                 
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                 
                datos.push({ name: "tasa", value: document.querySelector("#plazo").value })
                 
                const plazo = document.querySelector("#plazo")
                confirmarMovimiento(
                    "Apertura de cuenta de inversión",
                    "¿Está segur(a) de continuar con la apertura de la cuenta de inversión por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")" 
                    + " a un plazo de " + plazo.options[plazo.selectedIndex].text + "?"
                ).then((continuar) => {
                    if (!continuar) return
                 
                    consultaServidor("/Ahorro/RegistraInversion/", $.param(datos), (respuesta) => {
                            if (!respuesta.success){
                                console.log(respuesta.error)
                                return showError(respuesta.mensaje)
                            }
                            showSuccess(respuesta.mensaje).then(() => {
                                imprimeContrato(document.querySelector("#contrato").value, 2)
                                imprimeTicket(respuesta.datos.ticket, {$_SESSION['cdgco']})
                                limpiaDatosCliente()
                            })
                        })
                })
            }
             
            const validaBlur = (e) => {
                const monto = parseaNumero(e.target.value)
                 
                if (monto < saldoMinimoApertura) {
                    e.target.value = ""
                    return showError("El monto mínimo de apertura es de " + saldoMinimoApertura.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }))
                }
            }
        </script>
        html;

        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro($this->__usuario);
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}'>{$sucursales['NOMBRE']}</option>";
            $suc_eje = $sucursales['CODIGO'];
        }

        $ejecutivos = CajaAhorroDao::GetEjecutivosSucursal($suc_eje);
        $opcEjecutivos = "";
        foreach ($ejecutivos as $ejecutivos) {
            $opcEjecutivos .= "<option value='{$ejecutivos['ID_EJECUTIVO']}'>{$ejecutivos['EJECUTIVO']}</option>";
        }
        $opcEjecutivos .= "<option value='{$this->__usuario}'>{$this->__nombre} - CAJER(A)</option>";

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Inversión")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        view::set('ejecutivos', $opcEjecutivos);
        View::render("caja_menu_contrato_inversion");
    }

    public function RegistraInversion()
    {
        $contrato = CajaAhorroDao::RegistraInversion($_POST);
        echo $contrato;
    }

    // Visualización de cuentas de inversión
    public function ConsultaInversion()
    {
        $extraFooter = <<<html
        <script>
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->sinContrato}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->soloNumeros}
            {$this->primeraMayuscula}
            {$this->consultaServidor}
         
            const configuraTabla = (idTabla = "muestra-cupones") => {
                $("#" + idTabla).tablesorter()
                $("#" + idTabla).DataTable({
                    lengthMenu: [
                        [10, 40, -1],
                        [10, 40, "Todos"]
                    ],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: 0
                        }
                    ],
                    order: false
                })
            
                $("#"  + idTabla + " input[type=search]").keyup(() => {
                    $("#example")
                        .DataTable()
                        .search(jQuery.fn.DataTable.ext.type.search.html(this.value))
                        .draw()
                })
            }
             
            $(document).ready(configuraTabla())
             
            const llenaDatosCliente = (datosCliente) => {
                consultaServidor("/Ahorro/GetInversiones/", { contrato: datosCliente.CONTRATO }, (respuesta) => {
                    if (!respuesta.success) return showError(respuesta.mensaje)
                    const inversiones = respuesta.datos
                    if (!inversiones) return
                    let inversionesTotal = 0
                    
                    const tTMP = $("#muestra-cupones").DataTable()
                    if (tTMP) tTMP.destroy()
                    
                    const filas = document.createDocumentFragment()
                    inversiones.forEach((inversion) => {
                        const fila = document.createElement("tr")
                        Object.keys(inversion).forEach((key) => {
                            let dato = inversion[key]
                            if (["RENDIMIENTO", "MONTO"].includes(key))
                                dato = parseFloat(dato).toLocaleString("es-MX", {
                                    style: "currency",
                                    currency: "MXN"
                                })
            
                            inversionesTotal += key === "MONTO" ? parseFloat(inversion[key]) : 0
                            const celda = document.createElement("td")
                            celda.innerText = dato
                            fila.appendChild(celda)
                        })
                        filas.appendChild(fila)
                    })
                    
                    document.querySelector("#datosTabla").appendChild(filas)
                    document.querySelector("#inversion").value = inversionesTotal.toLocaleString("es-MX", {
                        style: "currency",
                        currency: "MXN"
                    })
                    document.querySelector("#cliente").value = datosCliente.CDGCL
                    document.querySelector("#contrato").value = datosCliente.CONTRATO
                    document.querySelector("#nombre").value = datosCliente.NOMBRE
                    document.querySelector("#curp").value = datosCliente.CURP
                    configuraTabla()
                }, "GET")
            }
                 
            const limpiaDatosCliente = () => {
                document.querySelector("#datosTabla").innerHTML = ""
                document.querySelector("#cliente").value = ""
                document.querySelector("#contrato").value = ""
                document.querySelector("#inversion").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#curp").value = ""
            }
        </script>
        html;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Consulta Inversiones")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_estatus_inversion");
    }

    public function GetInversiones()
    {
        $inversiones = CajaAhorroDao::GetInversiones($_GET);
        echo $inversiones;
    }

    //********************CUENTA PEQUES********************//
    // Apertura de contratos para cuentas de ahorro Peques
    public function ContratoCuentaPeque()
    {
        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
        
            let valKD = false
             
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->imprimeContrato}
            {$this->addParametro}
            {$this->consultaServidor}
             
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado")
                 
                if (!noCliente.value) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                
                consultaServidor("/Ahorro/BuscaClientePQ/", { cliente: noCliente.value }, (respuesta) => {
                        if (!respuesta.success) {
                            if (respuesta.datos) {
                                const datosCliente = respuesta.datos
                                if (datosCliente["NO_CONTRATOS"] == 0) {
                                    swal({
                                        title: "Cuenta de ahorro Peques™",
                                        text: "El cliente " + noCliente.value + " no tiene una cuenta de ahorro.\\nDesea aperturar una cuenta de ahorro en este momento?",
                                        icon: "info",
                                        buttons: ["No", "Sí"],
                                        dangerMode: true
                                    }).then((abreCta) => {
                                        if (abreCta) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente.value
                                    })
                                    return
                                }
                                if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
                                    swal({
                                        title: "Cuenta de ahorro Peques™",
                                        text: "El cliente " + noCliente.value + " no ha completado el proceso de apertura de la cuenta de ahorro.\\nDesea completar el proceso en este momento?",
                                        icon: "info",
                                        buttons: ["No", "Sí"],
                                        dangerMode: true
                                    }).then((abreCta) => {
                                        if (abreCta) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente.value
                                    })
                                    return
                                }
                            }
                             
                            limpiaDatosCliente()
                            return showError(respuesta.mensaje)
                        }
                         
                        const datosCliente = respuesta.datos
                         
                        document.querySelector("#nombre1").disabled = false
                        document.querySelector("#nombre2").disabled = false
                        document.querySelector("#apellido1").disabled = false
                        document.querySelector("#apellido2").disabled = false
                        document.querySelector("#fecha_nac").disabled = false
                        document.querySelector("#ciudad").disabled = false
                        document.querySelector("#curp").disabled = false
                         
                        document.querySelector("#fechaRegistro").value = datosCliente.FECHA_REGISTRO
                        document.querySelector("#noCliente").value = noCliente.value
                        document.querySelector("#nombre").value = datosCliente.NOMBRE
                        document.querySelector("#direccion").value = datosCliente.DIRECCION
                        noCliente.value = ""
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
                 
                document.querySelector("#nombre1").disabled = true
                document.querySelector("#nombre2").disabled = true
                document.querySelector("#apellido1").disabled = true
                document.querySelector("#apellido2").disabled = true
                document.querySelector("#fecha_nac").disabled = true
                document.querySelector("#ciudad").disabled = true
                document.querySelector("#curp").disabled = true
                document.querySelector("#btnGeneraContrato").disabled = true
            }
            
            const generaContrato = async (e) => {
                e.preventDefault()
                 
                if (document.querySelector("#curp").value.length !== 18) {
                    showError("La CURP debe tener 18 caracteres.")
                    return
                }
                 
                if (document.querySelector("#edad").value > 17) {
                    showError("El peque a registrar debe tener menos de 18 años.")
                    return 
                }
                 
                if (document.querySelector("#apellido2").value === "") {
                    const respuesta = await swal({
                        title: "Cuenta de ahorro Peques™",
                        text: "No se ha capturado el segundo apellido.\\n¿Desea continuar con el registro?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    })
                    if (!respuesta) return
                }
                 
                const cliente = document.querySelector("#nombre").value
                
                confirmarMovimiento("Cuenta de ahorro Peques™",
                    "¿Está segura de continuar con la apertura de la cuenta Peques™ asociada al cliente "
                    + cliente
                    + "?"
                ).then((continuar) => {
                    if (!continuar) return
                    const noCredito = document.querySelector("#noCliente").value
                    const datos = $("#registroInicialAhorro").serializeArray()
                    addParametro(datos, "credito", noCredito)
                    
                    datos.forEach((dato) => {
                        if (dato.name === "sexo") {
                            dato.value = document.querySelector("#sexoH").checked
                        }
                    })
                    
                    consultaServidor("/Ahorro/AgregaContratoAhorroPQ/", $.param(datos), (respuesta) => {
                        if (!respuesta.success) {
                            console.error(respuesta.error)
                            limpiaDatosCliente()
                            return showError(respuesta.mensaje)
                        }
                    
                        const contrato = respuesta.datos
                        limpiaDatosCliente()
                        showSuccess("Se ha generado el contrato: " + contrato.contrato).then(() => {
                            imprimeContrato(contrato.contrato, 3)
                        })
                    })
                })
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
             
            const iniveCambio = (e) => e.preventDefault()
             
            const camposLlenos = (e) => {
                document.querySelector("#nombre1").value = document.querySelector("#nombre1").value.toUpperCase()
                document.querySelector("#nombre2").value = document.querySelector("#nombre2").value.toUpperCase()
                document.querySelector("#apellido1").value = document.querySelector("#apellido1").value.toUpperCase()
                document.querySelector("#apellido2").value = document.querySelector("#apellido2").value.toUpperCase()
                 
                const val = () => {
                    const campos = [
                        document.querySelector("#nombre1").value,
                        document.querySelector("#apellido1").value,
                        document.querySelector("#fecha_nac").value,
                        document.querySelector("#ciudad").value,
                        document.querySelector("#curp").value,
                        document.querySelector("#edad").value,
                        document.querySelector("#direccion").value,
                        document.querySelector("#confirmaDir").checked
                    ]
                    
                    return campos.every((campo) => campo)
                }
                if (e.target.id === "fecha_nac") calculaEdad(e)
                document.querySelector("#btnGeneraContrato").disabled = !val()
            }
             
            const calculaEdad = (e) => {
                const fecha = new Date(e.target.value)
                const hoy = new Date()
                let edad = hoy.getFullYear() - fecha.getFullYear()
                 
                const mesActual = hoy.getMonth()
                const diaActual = hoy.getDate()
                const mesNacimiento = fecha.getMonth()
                const diaNacimiento = fecha.getDate()
                if (mesActual < mesNacimiento || (mesActual === mesNacimiento && diaActual < diaNacimiento)) edad--
                 
                document.querySelector("#edad").value = edad
            }
        </script>
        html;


        $ComboEntidades = CajaAhorroDao::GetEFed();

        $opciones_ent = "";
        foreach ($ComboEntidades as $key => $val2) {
            $opciones_ent .= <<<html
                <option  value="{$val2['NOMBRE']}"> {$val2['NOMBRE']}</option>
            html;
        }

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Cuenta Peque")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::set('opciones_ent', $opciones_ent);
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

    // Movimientos sobre cuentas de ahorro Peques
    public function CuentaPeque()
    {
        $extraFooter = <<<html
        <script>
            let valKD = false
         
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->addParametro}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
            
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado").value
                
                if (!noCliente) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                 
                consultaServidor("/Ahorro/BuscaContratoPQ/", { cliente: noCliente }, (respuesta) => {
                        limpiaDatosCliente()
                        if (!respuesta.success) {
                            if (!respuesta.datos) return showError(respuesta.mensaje)
                            const datosCliente = respuesta.datos
                             
                            if (datosCliente["NO_CONTRATOS"] == 0) {
                                swal({
                                    title: "Cuenta de ahorro Peques™",
                                    text: "La cuenta " + noCliente + " no tiene una cuenta de ahorro.\\nDesea realizar la apertura en este momento?",
                                    icon: "info",
                                    buttons: ["No", "Sí"],
                                    dangerMode: true
                                }).then((realizarDeposito) => {
                                    if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                                })
                                return
                            }
                            if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
                                swal({
                                    title: "Cuenta de ahorro Peques™",
                                    text: "La cuenta " + noCliente + " no ha concluido con el proceso de apertura de la cuenta de ahorro.\\nDesea completar el contrato en este momento?",
                                    icon: "info",
                                    buttons: ["No", "Sí"],
                                    dangerMode: true
                                }).then((realizarDeposito) => {
                                    if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                                })
                            }
                            if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 1) {
                                swal({
                                    title: "Cuenta de ahorro Peques™",
                                    text: "La cuenta " + noCliente + " no tiene asignadas cuentas Peques™.\\nDesea aperturar una cuenta Peques™ en este momento?",
                                    icon: "info",
                                    buttons: ["No", "Sí"],
                                    dangerMode: true
                                }).then((realizarDeposito) => {
                                    if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaPeque/?cliente=" + noCliente
                                })
                                return
                            }
                        }
                        const datosCliente = respuesta.datos
                         
                        const contratos = document.createDocumentFragment()
                        const seleccionar = document.createElement("option")
                        seleccionar.value = ""
                        seleccionar.disabled = true
                        seleccionar.innerText = "Seleccionar"
                        contratos.appendChild(seleccionar)
                         
                        datosCliente.forEach(cliente => {
                            const opcion = document.createElement("option")
                            opcion.value = cliente.CDG_CONTRATO
                            opcion.innerText = cliente.NOMBRE
                            contratos.appendChild(opcion)
                        })
                         
                        document.querySelector("#contrato").appendChild(contratos)
                        document.querySelector("#contrato").selectedIndex = 0
                        document.querySelector("#contrato").disabled = false
                        document.querySelector("#contrato").addEventListener("change", (e) => {
                            datosCliente.forEach(contrato => {
                                if (contrato.CDG_CONTRATO == e.target.value) {
                                    document.querySelector("#nombre").value = contrato.CDG_CONTRATO
                                    document.querySelector("#curp").value = contrato.CURP
                                    document.querySelector("#cliente").value = contrato.CDGCL
                                    document.querySelector("#saldoActual").value = formatoMoneda(contrato.SALDO)
                                    document.querySelector("#deposito").disabled = false
                                    document.querySelector("#retiro").disabled = false
                                }
                            })
                        })
                        
                        document.querySelector("#clienteBuscado").value = ""
                    })
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#fecha_pago").value = getHoy()
                document.querySelector("#monto").disabled = true
                document.querySelector("#deposito").disabled = true
                document.querySelector("#retiro").disabled = true
                document.querySelector("#contrato").innerHTML = ""
                document.querySelector("#contrato").disabled = true
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
                if (!valKD) return
                 
                let monto = parseaNumero(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
                }
                 
                if (monto > 1000000) {
                    monto = 1000000
                    e.preventDefault()
                    e.target.value = 1000000.00
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseaNumero(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#monto_letra").value = numeroLetras(parseaNumero(e.target.value))
                if (document.querySelector("#deposito").checked || document.querySelector("#retiro").checked) calculaSaldoFinal()
            }
             
            const calculaSaldoFinal = () => {
                const esDeposito = document.querySelector("#deposito").checked
                const saldoActual = parseaNumero(document.querySelector("#saldoActual").value)
                const monto = parseaNumero(document.querySelector("#monto").value)
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                document.querySelector("#saldoFinal").value = formatoMoneda(esDeposito ? saldoActual + monto : saldoActual - monto)
                compruebaSaldoFinal(document.querySelector("#saldoFinal").value)
            }
             
            const cambioMovimiento = (e) => {
                document.querySelector("#monto").disabled = false
                const esDeposito = document.querySelector("#deposito").checked
                document.querySelector("#simboloOperacion").innerText = esDeposito ? "+" : "-"
                document.querySelector("#descOperacion").innerText = (esDeposito ? "Depósito" : "Retiro") + " a cuenta ahorro corriente"
                calculaSaldoFinal()
            }
             
            const compruebaSaldoFinal = () => {
                const saldoFinal = parseaNumero(document.querySelector("#saldoFinal").value)
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(saldoFinal >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) > 0)
            }
             
            const registraOperacion = async (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                 
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                 
                if (!document.querySelector("#deposito").checked && !document.querySelector("#retiro").checked) {
                    return showError("Seleccione el tipo de operación a realizar.")
                }
                
                datos.forEach((dato) => {
                    if (dato.name === "esDeposito") {
                        dato.value = document.querySelector("#deposito").checked
                    }
                })
                 
                confirmarMovimiento(
                    "Confirmación de movimiento de cuenta ahorro Peques™",
                    "¿Está segur(a) de continuar con el registro de un "
                    + (document.querySelector("#deposito").checked ? "depósito" : "retiro")
                    + " de cuenta ahorro peque, por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")?"
                ).then((continuar) => {
                    if (!continuar) return
                    
                    consultaServidor("/Ahorro/registraOperacion/", $.param(datos), (respuesta) => {
                            if (!respuesta.success){
                                console.log(respuesta.error)
                                return showError(respuesta.mensaje)
                            }
                            
                            showSuccess(respuesta.mensaje).then(() => {
                                imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco']}")
                                limpiaDatosCliente()
                            })
                        })
                })
            }
        </script>
        html;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Cuenta Peque")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_menu_peque");
    }

    //******************REPORTE DE SALDO EN CAJA******************//
    // Muestra un reporte para el segimiento de los saldos en caja
    public function SaldosDia()
    {
        $saldoInicial = 65493.52;
        $entradas = 0;
        $salidas = 0;

        $extraFooter = <<<html
        <script>
            $(document).ready(() => {
                $("#muestra-cupones").tablesorter()
                $("#muestra-cupones").DataTable({
                    lengthMenu: [
                        [10, 40, -1],
                        [10, 40, "Todos"]
                    ],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: 0
                        }
                    ],
                    order: false
                })
            
                $("#muestra-cupones input[type=search]").keyup(() => {
                    $("#example")
                        .DataTable()
                        .search(jQuery.fn.DataTable.ext.type.search.html(this.value))
                        .draw()
                })
            })
             
            const imprimeExcel = () => {
                window.location.href = "/Ahorro/ExportaExcel/"
            }
        </script>
        html;

        $detalles = CajaAhorroDao::DetalleMovimientosXdia();

        $tabla = "";

        foreach ($detalles as $key => $detalle) {
            $tabla .= "<tr>";
            foreach ($detalle as $key => $valor) {
                if ($key === 'CODOP') continue;

                $v = $valor;
                if ($key === 'MOVIMIENTO') {
                    $v = self::IconoOperacion($valor, $detalle['CODOP']);
                    [$e, $s] = self::SeparaMontos($valor, $detalle['CODOP'], $detalle['MONTO']);
                    $entradas += $e;
                    $salidas += $s;
                }

                if ($key == 'MONTO') $v = "$ " . number_format($valor, 2);

                $tabla .= "<td style='vertical-align: middle;'>$v</td>";
            }

            $tabla .= "</tr>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Saldos del día")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y'));
        View::set('saldoInicial', number_format($saldoInicial, 2));
        View::set('entradas', number_format($entradas, 2));
        View::set('salidas', number_format($salidas, 2));
        View::set('saldoFinal', number_format($saldoInicial + $entradas - $salidas, 2));
        View::render("caja_menu_saldos_dia");
    }

    public function IconoOperacion($movimiento, $operacion)
    {
        if (in_array($operacion, $this->operacionesNulas)) return '<i class="fa fa-minus" style="color: #0000ac;"></i>';
        if ($movimiento == 1) return '<i class="fa fa-arrow-down" style="color: #00ac00;"></i>';
        if ($movimiento == 0) return '<i class="fa fa-arrow-up" style="color: #ac0000;"></i>';
    }

    public function SeparaMontos($movimiento, $operacion, $monto)
    {
        if (in_array($operacion, $this->operacionesNulas)) return [0, 0];
        if ($movimiento == 0) return [0, $monto];
        if ($movimiento == 1) return [$monto, 0];
    }

    public function ExportaExcel()
    {
        $detalles = CajaAhorroDao::DetalleMovimientosXdia();

        $tabla = "<table border='1'>";
        $tabla .= "<tr>";
        foreach ($detalles[0] as $key => $valor) {
            if ($key === 'CODOP') continue;
            $tabla .= "<th>$key</th>";
        }
        $tabla .= "</tr>";

        foreach ($detalles as $key => $detalle) {
            $tabla .= "<tr>";
            foreach ($detalle as $key => $valor) {
                if ($key === 'CODOP') continue;
                $tabla .= "<td>$valor</td>";
            }
            $tabla .= "</tr>";
        }

        $tabla .= "</table>";

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=DetallesMovimientos.xlsx");
        echo $tabla;
    }

    public function GetLogTransacciones()
    {
        $log = CajaAhorroDao::GetLogTransacciones($_POST);
        echo $log;
    }

    public function EstadoCuenta()
    {
        $fecha = date('Y-m-d');
        $fechaInicio =  date('Y-m-d', strtotime('-1 month'));

        $extraFooter = <<<script
        <script>
            const mEdoCta = true
            let datosCliente = {}
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->sinContrato}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->consultaServidor}
         
            const limpiaDatosCliente = () => {
                datosCliente = {}
                document.querySelector("#cliente").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#contrato").value = ""
                document.querySelector("#fechaInicio").value = "{$fechaInicio}"
                document.querySelector("#fechaFin").value = "{$fecha}"
                document.querySelector("#cliente").disabled = true
                document.querySelector("#nombre").disabled = true
                document.querySelector("#contrato").disabled = true
                document.querySelector("#fechaInicio").disabled = true
                document.querySelector("#fechaFin").disabled = true
                document.querySelector("#generarEdoCta").disabled = true
            }
             
            const llenaDatosCliente = (datos) => {
                if (!datos) return
                datosCliente = datos
                document.querySelector("#clienteBuscado").value = ""
                document.querySelector("#nombre").value = datos.NOMBRE
                document.querySelector("#cliente").value = datos.CDGCL
                document.querySelector("#contrato").value = datos.CONTRATO
                document.querySelector("#fechaInicio").disabled = false
                document.querySelector("#fechaFin").disabled = false
                document.querySelector("#generarEdoCta").disabled = false
            }
             
            const imprimeEdoCta = () => {
                const cliente = document.querySelector("#cliente").value
                if (!cliente) return showError("Ingrese un código de cliente.")
                mostrar(cliente)
            }
             
            const mostrar = (cliente) => {
                const host = window.location.origin
                fInicio = getFecha(document.querySelector("#fechaInicio").value)
                fFin = getFecha(document.querySelector("#fechaFin").value)
            
                let plantilla = '<!DOCTYPE html>'
                plantilla += '<html lang="es">'
                plantilla += '<head>'
                plantilla += '<meta charset="UTF-8">'
                plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
                plantilla += '<link rel="shortcut icon" href="" + host + "/img/logo.png">'
                plantilla += '<title>Estado de Cuenta: ' + cliente + '</title>'
                plantilla += '</head>'
                plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
                plantilla += '<iframe src="'
                    + host + '/Ahorro/EdoCta/?'
                    + 'cliente=' + cliente
                    + '&fInicio=' + fInicio
                    + '&fFin=' + fFin
                    + '" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
                plantilla += '</body>'
                plantilla += '</html>'
            
                const blob = new Blob([plantilla], { type: 'text/html' })
                const url = URL.createObjectURL(blob)
                window.open(url, '_blank')
            }
             
            const getFecha = (fecha) => {
                const f = new Date(fecha + 'T06:00:00Z')
                return f.toLocaleString("es-MX", { year: "numeric", month:"2-digit", day:"2-digit" })
            }
        </script>
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Estado de Cuenta")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', $fecha);
        View::set('fechaInicio', date('Y-m-d', strtotime('-1 month')));
        View::render("caja_menu_estado_cuenta");
    }

    //********************UTILS********************//
    // Generación de ticket's de operaciones realizadas

    public function Contrato()
    {
        $productos = [
            1 => 'Cuenta de Ahorro Corriente',
            2 => 'Cuenta de Inversión',
            3 => 'Cuenta de Ahorro Peque',
        ];
        $contrato = $_GET['contrato'];
        $datos = CajaAhorroDao::DatosContrato($contrato);
        if (!$datos) {
            echo "No se encontró información para el contrato: " . $contrato;
            return;
        }

        $style = <<<html
        <style>
            body {
                font-family: Helvetica, sans-serif;
                margin: 0;
                padding: 0;
            }
            .contenedor {
                max-width: 800px;
                margin: 0 auto;
                position: relative;
            }
            .logo {
                position: absolute;
                top: 0;
                right: 0;
                max-width: 100px;
                height: auto;
            }
            h1, h2 {
                text-align: center;
            }
            .seccion {
                margin-bottom: 20px;
            }
            .seccion-title {
                font-size: 1.2em;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .seccion-content {
                border: 1px solid #ccc;
                padding: 10px;
                width: 100%;
            }
            .generales {
                width: 100%;
            }
            .firma {
                text-align: center;
            }
        </style>  
        html;

        $dep_ini = number_format($datos['DEP_INICIAL'], 2, '.', ',');
        $comision = number_format($datos['COMISION'], 2, '.', ',');
        $saldo_ini = number_format($datos['SALDO_INICIAL'], 2, '.', ',');

        $tabla = <<<html
        <div class="contenedor">
            <h1>Contrato de {$productos[$_GET['producto']]}</h1>
            <div class="seccion">
                <h2 class="seccion-title">Datos Generales</h2>
                <div class="seccion-content">
                    <table class="generales">
                        <tr>
                            <td><b>Contrato:</b></td>
                            <td>{$datos['CONTRATO']}</td>
                            <td></td>
                            <td></td>
                            <td><b>Fecha de apertura:</b></td>
                            <td>{$datos['FECHA_APERTURA']}</td>
                        </tr>
                        <tr>
                            <td><b>Cliente:</b></td>
                            <td colspan="5">{$datos['NOMBRE_CLIENTE']}</td>
                        </tr>
                        <tr>
                            <td><b>Deposito inicial:</b></td>
                            <td>$ $dep_ini</td>
                            <td><b>Cargos y comisiones:</b></td>
                            <td>$ $comision</td>
                            <td><b>Saldo inicial:</b></td>
                            <td>$ $saldo_ini</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="seccion">
                <h2 class="seccion-title">Partes del Contrato</h2>
                <div class="seccion-content">
                    <p>En este contrato, se establece en común acuerdo entre Más con Menos S.A. de C.V. (en adelante "La Empresa") y el cliente {$datos['NOMBRE_CLIENTE']} (en adelante "El Cliente").</p>
                </div>
            </div>
            <div class="seccion">
                <h2 class="seccion-title">Servicios Ofrecidos</h2>
                <div class="seccion-content">
                    <p>La Empresa se compromete a proporcionar los siguientes servicios financieros:</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ac enim at justo luctus condimentum et eu eros. Duis molestie, mi in suscipit tristique, enim mauris lobortis dui, sit amet aliquam lorem odio eu nisl. Sed mi mi, pulvinar placerat tincidunt ut, pulvinar a nibh. Aliquam sapien nunc, auctor aliquet lacinia sit amet, convallis ut urna. Aenean lacinia molestie dui ut bibendum. Nullam iaculis pretium ante. In hac habitasse platea dictumst. Phasellus posuere in augue sed tincidunt.
                        Aenean nec laoreet eros, egestas egestas nulla. Donec nec hendrerit tortor. Curabitur eu quam in nibh interdum convallis eget id massa. Vivamus eget eros at tellus mattis dapibus quis in est. Ut quis placerat ex. Nulla tempor vestibulum condimentum. Quisque vestibulum, urna eu sollicitudin finibus, arcu eros pretium lorem, id tempus eros odio sit amet justo. Suspendisse potenti. Sed accumsan nibh est, id tristique diam semper sed. Interdum et malesuada fames ac ante ipsum primis in faucibus. Etiam eleifend urna dui. Nunc ac sapien molestie, semper neque nec, aliquet metus. Etiam eget pulvinar ligula. Proin volutpat ultricies sem, fermentum sagittis nisi ultrices id.
                        Phasellus feugiat rutrum est, eu ullamcorper libero dictum sit amet. Praesent sit amet odio rutrum, lacinia diam vel, faucibus quam. Etiam rhoncus erat id convallis iaculis. Mauris euismod mollis quam viverra faucibus. Cras eget diam augue. Sed orci quam, placerat vitae nisl sed, mollis vehicula enim. Sed ullamcorper pretium metus, commodo tincidunt ipsum gravida nec. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur non.</p>
                </div>
            </div>
            <div class="seccion">
                <h2 class="seccion-title">Condiciones</h2>
                <div class="seccion-content">
                    <p>El Cliente se compromete a respetar y cumplir las siguientes condiciones:</p>
                    <ul>
                        <li>Condición 1</li>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi.</p>
                        <li>Condición 2</li>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi.</p>
                        <li>Condición 3</li>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi.</p>
                    </ul>
                </div>
            </div>
            <div class="seccion">
                <h2 class="seccion-title">Firma del Cliente</h2>
                <div class="seccion-content firma">
                    <p>___________________________</p>
                    <p>Firma del Cliente</p>
                </div>
            </div>
        </div>
        html;

        $nombreArchivo = "Contrato " . $contrato;

        $mpdf = new \mPDF('utf-8', 'Letter', 10, 'Arial');
        $fi = date('d/m/Y H:i:s');
        $pie = <<< html
        <table style="width: 100%; font-size: 10px">
            <tr>
            <td style="text-align: left; width: 50%;">
                Fecha de impresión  {$fi}
            </td>
            <td style="text-align: right; width: 50%;">
                Página {PAGENO} de {nb}
            </td>
            </tr>
        </table>
        html;
        $mpdf->SetHTMLFooter($pie);
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML($style, 1);
        $mpdf->WriteHTML($tabla, 2);

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
    }

    public function Ticket()
    {
        $ticket = $_GET['ticket'];
        $sucursal = $_GET['sucursal'] ?? "";
        $datos = CajaAhorroDao::DatosTicket($ticket);
        if (!$datos) {
            echo "No se encontró información para el ticket: " . $ticket;
            return;
        }

        $nombreArchivo = "Ticket " . $ticket;
        $mensajeImpresion = 'Fecha de impresión:<br>' . date('d/m/Y H:i:s');
        if ($sucursal) {
            $datosImpresion = CajaAhorroDao::getSucursal($sucursal);
            $mensajeImpresion = 'Fecha y sucursal de impresión:<br>' . date('d/m/Y H:i:s') . ' - ' . $datosImpresion['NOMBRE'] . ' (' . $datosImpresion['CODIGO'] . ')';
        }


        $mpdf = new \mPDF('UTF-8', array(90, 190), 10, 'Arial', 10, 10, 0, 0, 0, 5);
        // PIE DE PAGINA
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:Arial;">' . $mensajeImpresion . '</div>');
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->SetMargins(0, 0, 5);

        $tktEjecutivo = $datos['COD_EJECUTIVO'] ? "<label>" . $datos['RECIBIO'] . ": " . $datos['NOM_EJECUTIVO'] . " (" . $datos['COD_EJECUTIVO'] . ")</label><br>" : "";
        $tktSucursal = $datos['CDG_SUCURSAL'] ? '<label>Sucursal: ' . $datos['NOMBRE_SUCURSAL'] . ' (' . $datos['CDG_SUCURSAL'] . ')</label>' : "";
        $tktMontoLetra = self::NumeroLetras($datos['MONTO']);
        $tktSaldoA = number_format($datos['SALDO_ANTERIOR'], 2, '.', ',');
        $tktMontoOP = number_format($datos['MONTO'], 2, '.', ',');
        $tktSaldoN = number_format($datos['SALDO_NUEVO'], 2, '.', ',');
        $tktComision =  $datos['COMISION'] > 0 ?  '<tr><td style="text-align: left; width: 60%;">COMISION:</td><td style="text-align: right; width: 40%;">$ ' . number_format($datos['COMISION'], 2, '.', ',') . '</td></tr>' : "";

        $detalleMovimientos = "";
        if ($datos['COMPROBANTE'] == 'DEPÓSITO') {
            $detalleMovimientos = <<<html
            <tr>
                <td style="text-align: left; width: 60%;">
                    {$datos['ES_DEPOSITO']}:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktMontoOP}
                </td>
            </tr>
            html;
        } else {
            $detalleMovimientos = <<<html
            <tr>
                <td style="text-align: center; font-weight: bold; font-size: 12px;" colspan="2">
                    SALDOS EN CUENTA DE AHORRO
                </td>
            <tr>
                <td style="text-align: left; width: 60%;">
                    SALDO ANTERIOR:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktSaldoA}
                </td>
            </tr>
            <tr>
                <td style="text-align: left; width: 60%;">
                    {$datos['ES_DEPOSITO']}:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktMontoOP}
                </td>
            </tr>
            $tktComision
            <tr>
                <td style="text-align: left; width: 60%;">
                    SALDO FINAL:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktSaldoN}
                </td>
            </tr>
            html;
        }

        $ticketHTML = <<<html
        <body style="font-family:Helvetica; padding: 0; margin: 0">
            <div>
                <div style="text-align:center; font-size: 20px; font-weight: bold;">
                    <label>Más con Menos</label>
                </div>
                <div style="text-align:center; font-size: 15px;">
                    <label>COMPROBANTE DE {$datos['COMPROBANTE']}</label>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                    *****************************************
                </div>
                <div style="font-size: 11px;">
                    <label>Fecha de la operación: {$datos['FECHA']}</label>
                    <br>
                    <label>Método de pago: {$datos['METODO']}</label>
                    <br>
                    $tktEjecutivo
                    $tktSucursal
                </div>
                <div style="text-align:center; font-size: 10px;margin-top:5px; margin-bottom: 5px; font-weight: bold;">
                    __________________________________________________________
                </div>
                <div style="font-size: 11px;">
                    <label>Nombre del cliente: {$datos['NOMBRE_CLIENTE']}</label>
                    <br>
                    <label>Código de cliente: {$datos['CODIGO']}</label>
                    <br>
                    <label>Código de contrato: {$datos['CONTRATO']}</label>
                </div>
                <div style="text-align:center; font-size: 10px;margin-top:5px; margin-bottom: 5px; font-weight: bold;">
                    __________________________________________________________
                </div>
                <div style="text-align:center; font-size: 13px; font-weight: bold;">
                    <label>{$datos['PRODUCTO']}</label>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                    *****************************************
                </div>
                <div style="text-align:center; font-size: 15px; font-weight: bold;">
                    <label>{$datos['ENTREGA']} $ {$tktMontoOP}</label>
                </div>
                <div style="text-align:center; font-size: 11px;">
                    <label>($tktMontoLetra)</label>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                    *****************************************
                </div>
                <div style="text-align:center; font-size: 13px;">
                    <table style="width: 100%; font-size: 11spx">
                        $detalleMovimientos
                    </table>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                    *****************************************
                </div>
                <div style="text-align:center; font-size: 15px; margin-top:25px; font-weight: bold;">
                    <label>Firma de conformidad del cliente</label>
                    <div style="text-align:center; font-size: 15px; margin-top:25px; margin-bottom: 5px">
                        ______________________
                    </div>
                </div>
                <div style="text-align:center; font-size: 12px; font-weight: bold;">
                    <label>FOLIO DE LA OPERACIÓN</label>
                    <barcode code="$ticket-{$datos['CODIGO']}-{$datos['MONTO']}-{$datos['COD_EJECUTIVO']}" type="C128A" size=".60" height="1" />
                </div>
            </div>
        </body>
        html;

        // Agregar contenido al PDF
        $mpdf->WriteHTML($ticketHTML);

        if ($_GET['copiaCliente']) {
            $mpdf->WriteHTML('<div style="text-align:center; font-size: 15px;"><label><b>COPIA SUCURSAL</b></label></div>');
            $mpdf->AddPage();
            $mpdf->WriteHTML($ticketHTML);
            $mpdf->WriteHTML('<div style="text-align:center; font-size: 15px;"><label><b>COPIA CLIENTE</b></label></div>');
        }

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
        exit;
    }

    public function EdoCta()
    {
        $msjError = "";
        $msjError .= !isset($_GET['cliente']) ? "No se proporcionó un número de cliente.<br>" : "";
        $msjError .= !isset($_GET['fInicio']) ? "No se proporcionó una fecha de inicio.<br>" : "";
        $msjError .= !isset($_GET['fFin']) ? "No se proporcionó una fecha de fin.<br>" : "";
        $fi = DateTime::createFromFormat('d/m/Y', $_GET['fInicio']);
        $ff = DateTime::createFromFormat('d/m/Y', $_GET['fFin']);
        $msjError .= !$fi ? "La fecha de inicio no es válida.<br>" : "";
        $msjError .= !$ff ? "La fecha de final no es válida.<br>" : "";
        $msjError .= ($fi > $ff) ? "La fecha de inicio no puede ser mayor a la fecha de final.<br>" : "";
        if ($msjError) {
            echo $msjError;
            return;
        }

        $dtsGrls = CajaAhorroDao::GetDatosEdoCta($_GET['cliente']);
        if (!$dtsGrls) {
            echo "No se encontró información para el cliente: " . $_GET['cliente'];
            return;
        }

        $fInicio = $_GET['fInicio'] ?? date('d/m/Y', strtotime('-1 month'));
        $fFin = $_GET['fFin'] ?? date('d/m/Y');

        $estilo = <<<css
        <style>
            body {
                margin: 0;
                padding: 0;
            }
            .datosGenerales {
                margin-bottom: 20px;
            }
            .tablaTotales {
                margin: 5px 0;
            }
            .tituloTablas {
                font-size: 20px;
                font-weight: bold;
            }
            .datosCliente {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
            }
            .datosCliente td {
                text-align: center;
                margin: 15px 0;
            }
            .contenedorTotales {
                margin: 10px 0;
            }
            .tablaTotales {
                width: 100%;
                border-collapse: collapse;
            }
            .contenedorDetalle {
                margin: 5px 0;
            }
            .tablaDetalle {
                border-collapse: collapse;
                width: 100%;
                margin: 0 0 20px 0;
            }
            .tablaDetalle th {
                background-color: #f2f2f2;
            }
            .tablaDetalle th, .tablaDetalle td {
                border: 1px solid #ddd;
            }
        </style>
        css;

        $cuerpo = <<<html
        <body>
            <div class="datosGenerales" style="text-align:center;">
                <h1>Estado de Cuenta</h1>
                <table class="datosCliente">
                    <tr>
                        <td colspan="6">
                            <b>Nombre del Cliente: </b>{$dtsGrls['NOMBRE']}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="width: 50%;">
                            <b>Número de Contrato: </b>{$dtsGrls['CONTRATO']}
                        </td>
                        <td colspan="3" style="width: 50%;">
                            <b>Número de Cliente: </b>{$dtsGrls['CLIENTE']}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="width: 50%;">
                            <b>Inicio del Período: </b>{$fInicio}
                        </td>
                        <td colspan="3" style="width: 50%;">
                            <b>Fin del Período: </b>{$fFin}
                        </td>
                    </tr>
                </table>
            </div>
        html;

        $cuerpo .= self::TablaMovimientosAhorro($dtsGrls['CONTRATO'], $_GET['fInicio'], $_GET['fFin']);
        $cuerpo .= self::TablaMovimientosInversion($dtsGrls['CONTRATO']);
        $cuerpo .= self::TablaMovimientosPeque($_GET['cliente'], $_GET['fInicio'], $_GET['fFin']);

        $cuerpo .= <<<html
            <div class="notices">
                <h2>Avisos y Leyendas</h2>
                <p>[Avisos y Leyendas Legales]</p>
            </div>
        </body>
        html;

        $nombreArchivo = "Estado de Cuenta: " . $_GET['cliente'];

        $mpdf = new \mPDF('utf-8', 'Letter', 10, 'Arial');
        $fi = date('d/m/Y H:i:s');
        $pie = <<< html
        <table style="width: 100%; font-size: 10px">
            <tr>
            <td style="text-align: left; width: 50%;">
                Fecha de impresión  {$fi}
            </td>
            <td style="text-align: right; width: 50%;">
                Página {PAGENO} de {nb}
            </td>
            </tr>
        </table>
        html;

        $mpdf->SetHTMLFooter($pie);
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML($estilo, 1);
        $mpdf->WriteHTML($cuerpo, 2);

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
    }

    public function TablaMovimientosAhorro($contrato, $fIni, $fFin)
    {
        $datos = CajaAhorroDao::GetMovimientosAhorro($contrato, $fIni, $fFin);
        $cargos = 0;
        $abonos = 0;
        $filas = "<tr><td colspan='5' style='text-align: center;'>Sin movimientos en el periodo.</td></tr>";
        $salto = false;
        if ($datos || count($datos) > 0) {
            $filas = "";
            foreach ($datos as $dato) {
                $cargo = number_format($dato['CARGO'], 2, '.', ',');
                $abono = number_format($dato['ABONO'], 2, '.', ',');
                $saldo = number_format($dato['SALDO'], 2, '.', ',');
                $cargos += $dato['CARGO'];
                $abonos += $dato['ABONO'];

                $filas .= <<<html
                <tr>
                    <td style="text-align: center;">{$dato['FECHA']}</td>
                    <td>{$dato['DESCRIPCION']}</td>
                    <td style="text-align: right;">$ $cargo</td>
                    <td style="text-align: right;">$ $abono</td>
                    <td style="text-align: right;">$ $saldo</td>
                </tr>
                html;
            }
            $salto = true;
        }

        $si = number_format($datos[0]['SALDO'] + $datos[0]['CARGO'] - $datos[0]['ABONO'], 2, '.', ',');
        $sf = number_format($datos[count($datos) - 1]['SALDO'], 2, '.', ',');
        $c = number_format($cargos, 2, '.', ',');
        $a = number_format($abonos, 2, '.', ',');
        $tabla = <<<html
        <span class="tituloTablas">Cuenta Ahorro Corriente</span>
        <div class="contenedorTotales">
            <table class="tablaTotales">
                <thead>
                    <tr>
                        <th>Saldo Inicial</th>
                        <th>Abonos</th>
                        <th>Cargos</th>
                        <th>Saldo Final</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; width: 25%;">
                            $ $si
                        </td>
                        <td style="text-align: center; width: 25%;">
                            $ $a
                        </td>
                        <td style="text-align: center; width: 25%;">
                            $ $c
                        </td>
                        <td style="text-align: center; width: 25%;">
                            $ $sf
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="contenedorDetalle">
            <table class="tablaDetalle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Fecha</th>
                        <th>Descripción</th>
                        <th style="width: 100px;">Cargo</th>
                        <th style="width: 100px;">Abono</th>
                        <th style="width: 100px;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    $filas
                </tbody>
            </table>
        </div>
        html;

        return $tabla . ($salto ? "<div style='page-break-after: always;'></div>" : "");
    }

    public function TablaMovimientosInversion($contrato)
    {
        $datos = CajaAhorroDao::GetMovimientosInversion($contrato);
        if ($datos || count($datos) > 0) {
            $inversionTotal = 0;
            $rendimientoTotal = 0;
            $salto = false;
            // $filas = "<tr><td colspan='8' style='text-align: center;'>Sin movimientos en el periodo.</td></tr>";
            $filas = "";
            foreach ($datos as $dato) {
                $inversion = number_format($dato['MONTO'], 2, '.', ',');
                $rendimiento = number_format($dato['RENDIMIENTO'], 2, '.', ',');
                $inversionTotal += $dato['MONTO'];
                $rendimientoTotal += $dato['RENDIMIENTO'];

                $filas .= <<<html
                <tr>
                    <td style="text-align: center;">{$dato['FECHA_APERTURA']}</td>
                    <td style="text-align: center;">{$dato['FECHA_VENCIMIENTO']}</td>
                    <td style="text-align: right;">$ {$inversion}</td>
                    <td style="text-align: center;">{$dato['PLAZO']}</td>
                    <td style="text-align: center;">{$dato['TASA']} %</td>
                    <td style="text-align: center;">{$dato['ESTATUS']}</td>
                    <td style="text-align: center;">{$dato['FECHA_LIQUIDACION']}</td>
                    <td style="text-align: right;">$ {$rendimiento}</td>
                    <td style="text-align: center;">{$dato['ACCION']}</td>
                </tr>
                html;
            }
            $salto = true;

            $it = number_format($inversionTotal, 2, '.', ',');
            $rt = number_format($rendimientoTotal, 2, '.', ',');

            $tabla = <<<html
            <span class="tituloTablas">Cuenta Inversión</span>
            <div class="contenedorTotales">
                <table class="tablaTotales">
                    <thead>
                        <tr>
                            <th>Monto Total Invertido</th>
                            <th>Rendimientos Recibidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center; width: 50%;">
                                $ $it
                            </td>
                            <td style="text-align: center; width: 50%;">
                                $ $rt
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="contenedorDetalle">
                <table class="tablaDetalle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Fecha Apertura</th>
                            <th style="width: 80px;">Fecha Cierre</th>
                            <th style="width: 100px;">Monto</th>
                            <th>Plazo</th>
                            <th style="width: 60px;">Tasa Anual</th>
                            <th>Estatus</th>
                            <th style="width: 100px;">Fecha Liquidación</th>
                            <th>Rendimiento</th>
                            <th>Destino</th>
                        </tr>
                    </thead>
                    <tbody>
                        $filas
                    </tbody>
                </table>
            </div>
            html;

            return $tabla . ($salto ? "<div style='page-break-after: always;'></div>" : "");
        }
    }

    public function TablaMovimientosPeque($clPadre, $fIni, $fFin)
    {
        $cuentas = CajaAhorroDao::GetCuentasPeque($clPadre);
        if ($cuentas || count($cuentas) > 0) {
            $tabla = "<span class='tituloTablas'>Cuenta Ahorro Peque</span>";
            $salto = false;
            foreach ($cuentas as $cuenta) {
                $cargos = 0;
                $abonos = 0;
                $filas = "";
                $datos = CajaAhorroDao::GetMovimientosPeque($cuenta['CONTRATO'], $fIni, $fFin);
                if ($datos || count($datos) > 0) {
                    foreach ($datos as $dato) {
                        $cargo = number_format($dato['CARGO'], 2, '.', ',');
                        $abono = number_format($dato['ABONO'], 2, '.', ',');
                        $saldo = number_format($dato['SALDO'], 2, '.', ',');
                        $cargos += $dato['CARGO'];
                        $abonos += $dato['ABONO'];

                        $filas .= <<<html
                        <tr>
                            <td style="text-align: center;">{$dato['FECHA']}</td>
                            <td>{$dato['DESCRIPCION']}</td>
                            <td style="text-align: right;">$ $cargo</td>
                            <td style="text-align: right;">$ $abono</td>
                            <td style="text-align: right;">$ $saldo</td>
                        </tr>
                        html;
                    }
                    $salto = true;
                }
                $filas = $filas ? $filas : "<tr><td colspan='5' style='text-align: center;'>Sin movimientos en el periodo.</td></tr>";

                $si = number_format($datos[0]['SALDO'] + $datos[0]['CARGO'] - $datos[0]['ABONO'], 2, '.', ',');
                $sf = number_format($datos[count($datos) - 1]['SALDO'], 2, '.', ',');
                $c = number_format($cargos, 2, '.', ',');
                $a = number_format($abonos, 2, '.', ',');
                $tabla .= <<<html
                <div class="contenedorTotales">
                    <table class="tablaTotales">
                        <tr>
                            <td colspan="2" style="text-align: center; width: 50%;">
                                <b>Nombre: </b>{$cuenta['NOMBRE']}
                            </td>
                            <td colspan="2" style="text-align: center; width: 50%;">
                                <b>No. Cuenta: </b>{$cuenta['CONTRATO']}
                            </td>
                        </tr>
                        <tr>
                            <th>Saldo Inicial</th>
                            <th>Abonos</th>
                            <th>Cargos</th>
                            <th>Saldo Final</th>
                        </tr>
                        <tr>
                            <td style="text-align: center; width: 25%;">
                                $ $si
                            </td>
                            <td style="text-align: center; width: 25%;">
                                $ $a
                            </td>
                            <td style="text-align: center; width: 25%;">
                                $ $c
                            </td>
                            <td style="text-align: center; width: 25%;">
                                $ $sf
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="contenedorDetalle">
                    <table class="tablaDetalle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Fecha</th>
                                <th>Descripción</th>
                                <th style="width: 100px;">Cargo</th>
                                <th style="width: 100px;">Abono</th>
                                <th style="width: 100px;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            $filas
                        </tbody>
                    </table>
                </div>
                html;
            }

            return $tabla . ($salto ? "<div style='page-break-after: always;'></div>" : "");
        }
    }

    public function toLetras($numero)
    {
        $cifras = array(
            0 => 'cero',
            1 => 'uno',
            2 => 'dos',
            3 => 'tres',
            4 => 'cuatro',
            5 => 'cinco',
            6 => 'seis',
            7 => 'siete',
            8 => 'ocho',
            9 => 'nueve',
            11 => 'once',
            12 => 'doce',
            13 => 'trece',
            14 => 'catorce',
            15 => 'quince',
            16 => 'dieciséis',
            17 => 'diecisiete',
            18 => 'dieciocho',
            19 => 'diecinueve',
            21 => 'veintiuno',
            22 => 'veintidós',
            23 => 'veintitrés',
            24 => 'veinticuatro',
            25 => 'veinticinco',
            26 => 'veintiséis',
            27 => 'veintisiete',
            28 => 'veintiocho',
            29 => 'veintinueve',
            10 => 'diez',
            20 => 'veinte',
            30 => 'treinta',
            40 => 'cuarenta',
            50 => 'cincuenta',
            60 => 'sesenta',
            70 => 'setenta',
            80 => 'ochenta',
            90 => 'noventa',
            100 => 'cien',
            200 => 'doscientos',
            300 => 'trescientos',
            400 => 'cuatrocientos',
            500 => 'quinientos',
            600 => 'seiscientos',
            700 => 'setecientos',
            800 => 'ochocientos',
            900 => 'novecientos'
        );

        $letra = '';

        if ($numero >= 1000000) {
            $letra .= floor($numero / 1000000) == 1 ? 'un' : $cifras[floor($numero / 1000000)];
            $numero %= 1000000;
            $letra .= (floor($numero / 1000000) > 1 ? ' millones' : ' millón') . ($numero > 0 ? ' ' : '');
            $letra .= $letra == 'un millón' ? ' de' : '';
        }

        if ($numero >= 1000) {
            $letra .= floor($numero / 1000) == 1 ? ' un' : $cifras[floor($numero / 1000)];
            $numero %= 1000;
            $letra .= ' mil' . ($numero > 0 ? ' ' : '');
        }

        if ($numero >= 100) {
            $letra .= $cifras[floor($numero / 100) * 100];
            $letra .= floor($numero / 100) == 1 ? 'to' : '';
            $numero %= 100;
            $letra .= $numero > 0 ? ' ' : '';
        }

        if ($numero >= 30) {
            $letra .= $cifras[floor($numero / 10) * 10];
            $numero %= 10;
            $letra .= $numero > 0 ? ' y' : '';
        }


        if ($numero == 1) $letra .= ' un';
        else if ($numero == 21) $letra .= ' veintiún';
        else if ($numero > 0) $letra .= ' ' . $cifras[$numero];

        return trim($letra);
    }

    public function NumeroLetras($numero)
    {
        $letra = '';
        $letra = ($numero == 0) ? 'cero' : self::toLetras(floor($numero));

        $tmp = [
            ucfirst($letra),
            (floor($numero) == 1 ? "peso" : "pesos"),
            str_pad(round(($numero - floor($numero)) * 100), 2, "0", STR_PAD_LEFT) . "/100 M.N."
        ];

        return implode(" ", $tmp);
    }

    //********************BORRAR????********************//
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
        $extraFooter = <<<html
        <script>
            {$this->muestraPDF}
            {$this->imprimeTicket}
         
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
        </script>
        html;

        $Consulta = AhorroDao::ConsultaSolicitudesTickets($this->__usuario);
        $tabla = "";

        foreach ($Consulta as $key => $value) {
            $monto = number_format($value['MONTO'], 2);

            if ($value['AUTORIZA'] == 0) {
                $autoriza = "PENDIENTE";

                $imprime = <<<html
                    <span class="count_top" style="font-size: 22px"><i class="fa fa-clock-o" style="color: #ac8200"></i></span>
html;
            } else if ($value['AUTORIZA'] == 1) {
                $autoriza = "ACEPTADO";

                $imprime = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="imprimeTicket('{$value['CODIGO']}');"><i class="fa fa-print"></i></button>
html;
            } else if ($value['AUTORIZA'] == 2) {
                $imprime = <<<html
                <span class="count_top" style="font-size: 22px"><i class="fa fa-close" style="color: #ac1d00"></i></span>
html;
                $autoriza = "RECHAZADO";
            }


            if ($value['CDGPE_AUTORIZA'] == '') {
                $autoriza_nombre = "-";
            } else if ($value['CDGPE_AUTORIZA'] != '') {
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

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Solicitudes de reimpresión Tickets")));
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
           
            $(document).ready(function(){
            $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
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
            $('#muestra-cupones1 input[type=search]').keyup( function () {
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
