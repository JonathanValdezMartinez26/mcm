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
    private $validarYbuscar = 'const validarYbuscar = (e) => {
        if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
        if (e.keyCode === 13) buscaCliente()
    }';
    private $buscaCliente = 'const buscaCliente = () => {
        document.querySelector("#btnBskClnt").disabled = true
        const noCliente = document.querySelector("#clienteBuscado").value
        
        if (!noCliente) {
            limpiaDatosCliente()
            document.querySelector("#btnBskClnt").disabled = false
            return showError("Ingrese un número de cliente a buscar.")
        }
        
        $.ajax({
            type: "POST",
            url: "/Ahorro/BuscaContratoAhorro/",
            data: { cliente: noCliente },
            success: (respuesta) => {
                limpiaDatosCliente()
                respuesta = JSON.parse(respuesta)
                if (!respuesta.success) {
                    if (respuesta.datos && !sinContrato(respuesta.datos)) return
                     
                    limpiaDatosCliente()
                    return showError(respuesta.mensaje)
                }
                 
                llenaDatosCliente(respuesta.datos)
            },
            error: (error) => {
                console.error(error)
                limpiaDatosCliente()
                showError("Ocurrió un error al buscar el cliente.")
            }
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
        if ((e.keyCode > 95 && e.keyCode < 106) || (e.keyCode > 47 && e.keyCode < 58)) {
            valKD = true
            return
        }
        if (e.keyCode === 110 || e.keyCode === 190 || e.keyCode === 8  || e.keyCode === 8 || e.keyCode === 9  || e.keyCode === 37 || e.keyCode === 39 || e.keyCode === 46) {
            valKD = true
            return
        }
        return e.preventDefault()
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
    private $imprimeTicket = <<<script
    const imprimeTicket = (ticket, sucursal = '') => {
        const host = window.location.origin
    
        let plantilla = '<!DOCTYPE html>'
        plantilla += '<html lang="es">'
        plantilla += '<head>'
        plantilla += '<meta charset="UTF-8">'
        plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
        plantilla += '<link rel="shortcut icon" href="" + host + "/img/logo.png">'
        plantilla += '<title>Ticket: ' + ticket + '</title>'
        plantilla += '</head>'
        plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
        plantilla += '<iframe src="'
            + host + '/Ahorro/Ticket/?'
            + 'ticket=' + ticket
            + '&sucursal=' + sucursal
            + '" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
        plantilla += '</body>'
        plantilla += '</html>'
    
        const blob = new Blob([plantilla], { type: 'text/html' })
        const url = URL.createObjectURL(blob)
        window.open(url, '_blank')
    }
    script;
    private $imprimeContrato = <<<script
    const imprimeContrato = (numero_contrato, producto = 1) => {
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
        plantilla += '<iframe src="'
            + host
            + '/Ahorro/Contrato/?'
            + 'contrato=' + numero_contrato
            + '&producto=' + producto
            + '" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
        plantilla += "</body>"
        plantilla += "</html>"
    
        const blob = new Blob([plantilla], { type: "text/html" })
        const url = URL.createObjectURL(blob)
        window.open(url, "_blank")
    }
    script;
    private $sinContrato = <<<script
    const sinContrato = (datosCliente) => {
        if (datosCliente["NO_CONTRATOS"] == 0) {
            swal({
                title: "Cuenta de ahorro corriente",
                text: "El cliente " + datosCliente['CDGCL'] + " no tiene una cuenta de ahorro.\\nDesea aperturar una cuenta de ahorro en este momento?",
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
        if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
            swal({
                title: "Cuenta de ahorro corriente",
                text: "El cliente " + datosCliente['CDGCL'] + " no ha completado el proceso de apertura de la cuenta de ahorro.\\nDesea completar el proceso en este momento?",
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
    private $addParametro = <<<script
    const addParametro = (parametros, newParametro, newValor) => {
        parametros.push({ name: newParametro, value: newValor })
    }
    script;
    private $remParametro = <<<script
    const remParametro = (parametros, parametro) => {
        parametros = parametros.filter((param) => param.name !== parametro)
    }
    script;

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
        $saldoMinimoApertura = 100;

        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
             
            const saldoMinimoApertura = $saldoMinimoApertura
            const montoMaximo = 1000000
            const txtGuardaContrato = "GUARDAR DATOS Y PROCEDER AL COBRO"
            const txtGuardaPago = "REGISTRAR DEPÓSITO DE APERTURA"
            let valKD = false
             
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->imprimeTicket}
            {$this->imprimeContrato}
            {$this->addParametro}
             
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado").value
                limpiaDatosCliente()
                 
                if (!noCliente) {
                    return showError("Ingrese un número de cliente a buscar.")
                }
                
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/BuscaCliente/",
                    data: { cliente: noCliente },
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) {
                            if (!respuesta.datos) {
                                limpiaDatosCliente()
                                return showError(respuesta.mensaje)
                            }
                             
                            const datosCliente = respuesta.datos
                            document.querySelector("#btnGeneraContrato").disabled = true
                            if (datosCliente['NO_CONTRATOS'] >= 0 && datosCliente.CONTRATO_COMPLETO == 0) {
                                showInfo("La apertura del contrato no ha concluido, realice el depósito de apertura.").then(() => {
                                    document.querySelector("#fecha_pago").value = getHoy()
                                    document.querySelector("#contrato").value = datosCliente.CONTRATO
                                    document.querySelector("#codigo_cl").value = datosCliente.CDGCL
                                    document.querySelector("#nombre_cliente").value = datosCliente.NOMBRE
                                    document.querySelector("#mdlCurp").value = datosCliente.CURP
                                    $("#modal_agregar_pago").modal("show")
                                    document.querySelector("#chkCreacionContrato").classList.remove("red")
                                    document.querySelector("#chkCreacionContrato").classList.add("green")
                                    document.querySelector("#chkPagoApertura").classList.remove("green")
                                    document.querySelector("#chkPagoApertura").classList.add("red")
                                    document.querySelector("#btnGuardar").innerText = txtGuardaPago
                                    document.querySelector("#btnGeneraContrato").disabled = false
                                })
                            }
                             
                            if (datosCliente['NO_CONTRATOS'] >= 0 && datosCliente.CONTRATO_COMPLETO == 1) {
                                showInfo(respuesta.mensaje)
                                document.querySelector("#chkCreacionContrato").classList.remove("red")
                                document.querySelector("#chkCreacionContrato").classList.add("green")
                                document.querySelector("#chkPagoApertura").classList.remove("red")
                                document.querySelector("#chkPagoApertura").classList.add("green")
                            }
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
                document.querySelector("#tasa").disabled = false
                document.querySelector("#sucursal").disabled = false
                document.querySelector("#ejecutivo").disabled = false
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#AddPagoApertura").reset()
                document.querySelector("#registroInicialAhorro").reset()
                document.querySelector("#chkCreacionContrato").classList.remove("green")
                document.querySelector("#chkCreacionContrato").classList.add("red")
                document.querySelector("#chkPagoApertura").classList.remove("green")
                document.querySelector("#chkPagoApertura").classList.add("red")
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
                document.querySelector("#btnGuardar").innerText = txtGuardaContrato
                document.querySelector("#marcadores").style.opacity = "0"
                document.querySelector("#tasa").disabled = true
                document.querySelector("#sucursal").disabled = true
                document.querySelector("#ejecutivo").disabled = true
            }
            
            const generaContrato = async (e) => {
                e.preventDefault()
                const btnGuardar = document.querySelector("#btnGuardar")
                if (btnGuardar.innerText === txtGuardaPago) return $("#modal_agregar_pago").modal("show")
                 
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
                        await showSuccess("Se ha generado el contrato: " + contrato.contrato + ".")
                        
                        document.querySelector("#fecha_pago").value = getHoy()
                        document.querySelector("#contrato").value = contrato.contrato
                        document.querySelector("#codigo_cl").value = noCredito
                        document.querySelector("#nombre_cliente").value = document.querySelector("#nombre").value
                        document.querySelector("#mdlCurp").value = document.querySelector("#curp").value
                        imprimeContrato(contrato.contrato)
                        
                        document.querySelector("#chkCreacionContrato").classList.remove("red")
                        document.querySelector("#chkCreacionContrato").classList.add("green")
                         
                        showInfo("Debe registrar el depósito por apertura de cuenta.").then(() => {
                            btnGuardar.innerText = txtGuardaPago
                            $("#modal_agregar_pago").modal("show")
                        })
                    }
                } catch (error) {
                    console.error(error)
                }
                return false
            }
                        
            const pagoApertura = (e) => {
                e.preventDefault()
                if (document.querySelector("#deposito").value < saldoMinimoApertura) return showError("El saldo inicial no puede ser menor a $" + saldoMinimoApertura.toLocalString("es-MX", {style:"currency", currency:"MXN"}) + ".")
                            
                const datos = $("#AddPagoApertura").serializeArray()
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                            
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/PagoApertura/",
                    data: $.param(datos),
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) return showError(respuesta.mensaje)
                    
                        showSuccess(respuesta.mensaje)
                        document.querySelector("#registroInicialAhorro").reset()
                        document.querySelector("#AddPagoApertura").reset()
                        $("#modal_agregar_pago").modal("hide")
                        limpiaDatosCliente()
                        imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco']}")
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al registrar el pago de apertura.")
                    }
                })
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                let monto = parseFloat(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
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
                const monto = parseFloat(e.target.value)
                document.querySelector("#deposito").value = monto.toFixed(2)
                const saldoInicial = (monto - parseFloat(document.querySelector("#inscripcion").value)).toFixed(2)
                document.querySelector("#saldo_inicial").value = saldoInicial > 0 ? saldoInicial : "0.00"
                document.querySelector("#monto_letra").value = primeraMayuscula(numeroLetras(monto))
                    
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
             
            const camposLlenos = (e) => {
                const val = () => {
                    let porcentaje = 0
                    for (let i = 1; i <= 3; i++) {
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
        </script>
        html;

        $parentescos = CajaAhorroDao::GetCatalogoParentescos();

        $opcParentescos = "<option value='' disabled selected>Seleccionar</option>";
        foreach ($parentescos as $parentesco) {
            $opcParentescos .= "<option value='{$parentesco['CODIGO']}'>{$parentesco['DESCRIPCION']}</option>";
        }

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Ahorro Corriente")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        View::set('fecha', date('d/m/Y H:i:s'));
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
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->imprimeTicket}
            {$this->sinContrato}
            {$this->addParametro}
         
            const llenaDatosCliente = (datosCliente) => {
                document.querySelector("#nombre").value = datosCliente.NOMBRE
                document.querySelector("#curp").value = datosCliente.CURP
                document.querySelector("#contrato").value = datosCliente.CONTRATO
                document.querySelector("#cliente").value = datosCliente.CDGCL
                document.querySelector("#saldoActual").value = parseFloat(datosCliente.SALDO).toFixed(2)
                document.querySelector("#monto").disabled = false
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
            }
             
            const validaMonto = () => {
                if (!valKD) return
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseFloat(montoIngresado.value) || 0
                 
                if (!document.querySelector("#deposito").checked && monto > montoMaximoRetiro) {
                    monto = montoMaximoRetiro
                    swal({
                        title: "Cuenta de ahorro corriente",
                        text: "Para retiros mayores a " + montoMaximoRetiro + " es necesario realizar una solicitud de retiro\\nDesea generar una solicitud de retiro ahora?.",
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
                const monto = parseFloat(e.target.value)
                document.querySelector("#mdlDeposito").value = monto.toFixed(2)
                const saldoInicial = (monto - parseFloat(document.querySelector("#mdlInscripcion").value)).toFixed(2)
                document.querySelector("#mdlSaldo_inicial").value = saldoInicial > 0 ? saldoInicial : "0.00"
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
                document.querySelector("#monto").max = esDeposito ? montoMaximoDeposito : montoMaximoRetiro
                valKD = true
                validaMonto()
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
             
            const registraOperacion = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                 
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
                        imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco']}")
                        limpiaDatosCliente()
                    },
                    error: (error) => {
                        console.log(respuesta)
                        console.error(error)
                        showError("Ocurrió un error al registrar la operación.")
                    }
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
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->soloNumeros}
            {$this->primeraMayuscula}
            {$this->numeroLetras}
            {$this->imprimeTicket}
            {$this->addParametro}
            {$this->sinContrato}
            {$this->getHoy}
             
            const llenaDatosCliente = (datosCliente) => {
                if (parseFloat(datosCliente.SALDO) < montoMinimo) {
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
                document.querySelector("#saldoActual").value = parseFloat(datosCliente.SALDO).toFixed(2)
                document.querySelector("#monto").disabled = false
                document.querySelector("#saldoFinal").value = parseFloat(datosCliente.SALDO).toFixed(2)
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
                 
                let monto = parseFloat(montoIngresado.value) || 0
                 
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
                const saldoActual = parseFloat(document.querySelector("#saldoActual").value)
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                const saldoFinal = (saldoActual - monto)
                compruebaSaldoFinal(saldoFinal)
                document.querySelector("#saldoFinal").value = saldoFinal.toFixed(2)
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
             
            const compruebaSaldoFinal = saldoFinal => {
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(document.querySelector("#saldoFinal").value >= 0 && document.querySelector("#montoOperacion").value >= montoMinimo && document.querySelector("#montoOperacion").value < montoMaximoExpress)
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
                
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                addParametro(datos, "retiroExpress", document.querySelector("#express").checked)
                 
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/RegistraSolicitud/",
                    data: $.param(datos),
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) {
                            console.log(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                        showSuccess(respuesta.mensaje)
                        document.querySelector("#registroOperacion").reset()
                        limpiaDatosCliente()
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al registrar la operación.")
                    }
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
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->imprimeTicket}
            {$this->sinContrato}
            {$this->addParametro}
             
            const llenaDatosCliente = (datos) => {
                const saldoActual = parseFloat(datos.SALDO)
                         
                document.querySelector("#nombre").value = datos.NOMBRE
                document.querySelector("#curp").value = datos.CURP
                document.querySelector("#contrato").value = datos.CONTRATO
                document.querySelector("#cliente").value = datos.CDGCL
                document.querySelector("#saldoActual").value = saldoActual.toFixed(2)
                
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
                
                let monto = parseFloat(e.target.value) || 0
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
                    e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                 
                const saldoFinal = parseFloat(document.querySelector("#saldoActual").value) - monto
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                document.querySelector("#saldoFinal").value = (saldoFinal < 0 ? 0 : saldoFinal).toFixed(2)
                document.querySelector("#monto_letra").value = numeroLetras(monto)
                compruebaSaldoFinal(saldoFinal)
                habiltaEspecs(monto)
                compruebaSaldoMinimo()
            }
            
            const compruebaSaldoMinimo = () => {
                const monto = parseFloat(document.querySelector("#monto").value)
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
                    document.querySelector("#rendimiento").value = (parseFloat(document.querySelector("#monto").value) * parseFloat(tasa.TASA) / 100).toFixed(2)
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
                // if (document.querySelector("#plazo").value === "" || document.querySelector("#rendimiento").value === "") {
                //     document.querySelector("#btnRegistraOperacion").disabled = true
                //     return
                // }
                document.querySelector("#btnRegistraOperacion").disabled = !(document.querySelector("#saldoFinal").value >= 0 && document.querySelector("#montoOperacion").value >= saldoMinimoApertura)
            }
             
            const habiltaEspecs = (monto = parseFloat(document.querySelector("#monto").value)) => {
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
                addParametro(datos, "sucursal", "{$_SESSION['cdgco']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                 
                datos.push({ name: "tasa", value: document.querySelector("#plazo").value })
                
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/RegistraInversion/",
                    data: $.param(datos),
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success){
                            console.log(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                        showSuccess(respuesta.mensaje)
                        imprimeTicket(respuesta.datos.ticket, {$_SESSION['cdgco']})
                        limpiaDatosCliente()
                    },
                    error: (error) => {
                        console.log(respuesta)
                        console.error(error)
                        showError("Ocurrió un error al registrar la operación.")
                    }
                })
            }
        </script>
        html;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Inversión")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
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
         
            const llenaDatosCliente = (datosCliente) => {
                const inversiones = getInversiones(datosCliente.CONTRATO)
                if (!inversiones) return
                let inversionesTotal = 0
        
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
            }
                 
            const limpiaDatosCliente = () => {
                document.querySelector("#datosTabla").innerHTML = ""
                document.querySelector("#cliente").value = ""
                document.querySelector("#contrato").value = ""
                document.querySelector("#inversion").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#curp").value = ""
            }
                
            const getInversiones = (contrato) => {
                let inversiones = null
                $.ajax({
                    type: "GET",
                    url: "/Ahorro/GetInversiones/?contrato=" + contrato,
                    async: false,
                    success: (respuesta) => {
                        respuesta = JSON.parse(respuesta)
                        if (!respuesta.success) return showError(respuesta.mensaje)
                        inversiones = respuesta.datos
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al buscar las inversiones.")
                    }
                })
                    
                return inversiones
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
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->imprimeTicket}
            {$this->imprimeContrato}
            {$this->addParametro}
             
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
                                        if (abreCta) {
                                            window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente.value
                                            return
                                        }
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
                                        if (abreCta) {
                                            window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente.value
                                            return
                                        }
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
                    return false
                }
                 
                if (document.querySelector("#edad").value > 17) {
                    showError("El peque a registrar debe tener menos de 18 años.")
                    return false
                }
                 
                if (document.querySelector("#apellido2").value === "") {
                    const respuesta = await swal({
                        title: "Cuenta de ahorro Peques™",
                        text: "No se ha capturado el segundo apellido.\\n¿Desea continuar con el registro?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    })
                    if (!respuesta) return false
                }
                 
                const cliente = document.querySelector("#nombre").value
                try {
                    const continuar = await swal({
                        title:
                            "¿Está seguro de continuar con la apertura de la cuenta Peque™ asociada al cliente " +
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
                            limpiaDatosCliente()
                            return showError(respuesta.mensaje)
                        }
                        
                        const contrato = respuesta.datos
                        limpiaDatosCliente()
                        await showSuccess("Se ha generado el contrato: " + contrato.contrato)
                        imprimeContrato(contrato.contrato)
                    }
                } catch (error) {
                    console.error(error)
                }
                return false
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

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Cuenta Peque")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
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

    // Movimientos sobre cuentas de ahorro Peques
    public function CuentaPeque()
    {
        $extraFooter = <<<html
        <script>
            let valKD = false
         
            {$this->showError}
            {$this->showSuccess}
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->imprimeTicket}
            
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado").value
                
                if (!noCliente) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                 
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/BuscaContratoPQ/",
                    data: { cliente: noCliente },
                    success: (respuesta) => {
                        limpiaDatosCliente()
                        respuesta = JSON.parse(respuesta)
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
                                    if (realizarDeposito) {
                                        window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                                        return
                                    }
                                })
                                return
                            }
                            if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
                                swal({
                                    title: "Cuenta de ahorro Peques™",
                                    text: "La cuenta " + noCliente + " no ha concluido con el proceso de apertua de la cuenta de ahorro.\\nDesea completar el contrato en este momento?",
                                    icon: "info",
                                    buttons: ["No", "Sí"],
                                    dangerMode: true
                                }).then((realizarDeposito) => {
                                    if (realizarDeposito) {
                                        window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                                        return
                                    }
                                })
                            }
                            if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 1) {
                                swal({
                                    title: "Cuenta de ahorro Peques™",
                                    text: "La cuenta " + noCliente + " no tiene asignadas cuentas Peque™.\\nDesea aperturar una cuenta Peque™ en este momento?",
                                    icon: "info",
                                    buttons: ["No", "Sí"],
                                    dangerMode: true
                                }).then((realizarDeposito) => {
                                    if (realizarDeposito) {
                                        window.location.href = "/Ahorro/ContratoCuentaPeque/?cliente=" + noCliente
                                        return
                                    }
                                })
                                return
                            }
                        }
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
                        
                        document.querySelector("#clienteBuscado").value = ""
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
             
            const registraOperacion = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
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
                        imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco']}")
                        limpiaDatosCliente()
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al registrar la operación.")
                    }
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

    public function Log()
    {
        $extraFooter = <<<html
       
        html;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Log Transacciones Ahorro")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_admin_log");
    }

    //********************UTILS********************//
    // Generación de ticket's de operaciones realizadas
    public function Ticket()
    {
        $ticket = $_GET['ticket'];
        $sucursal = $_GET['sucursal'] ?? "1";
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


        $mpdf = new \mPDF('UTF-8', array(90, 190));
        // PIE DE PAGINA
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:Helvetica;">' . $mensajeImpresion . '</div>');

        $mpdf->SetMargins(0, 0, 5);
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML('<div></div>', 2);

        // CABECERA
        $mpdf->SetFont('Helvetica', '', 19);
        $mpdf->Cell(60, 4, 'Más con Menos', 0, 1, 'C');
        $mpdf->Ln(5);

        // LEYENDA TIPO COMPROBANTE
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, 'COMPROBANTE DE ' . $datos['COMPROBANTE'], 0, 1, 'C');
        $mpdf->Ln(3);
        $mpdf->Cell(60, 0, str_repeat('*', 35), 0, 1, 'C');
        $mpdf->Ln(2);

        // DATOS OPERACION
        $mpdf->SetFont('Helvetica', '', 9);
        $mpdf->Cell(60, 4, 'Fecha de la operación: ' . $datos['FECHA'], 0, 1, '');
        $mpdf->Cell(60, 4, 'Método de pago: ' . $datos['METODO'], 0, 1, '');
        if ($datos['COD_EJECUTIVO']) $mpdf->MultiCell(60, 4, $datos['RECIBIO'] . ': ' . $datos['NOM_EJECUTIVO'] . ' (' . $datos['COD_EJECUTIVO'] . ')', 0, 1, '');
        if ($datos['CDG_SUCURSAL']) $mpdf->Cell(60, 4, 'Sucursal: ' . $datos['NOMBRE_SUCURSAL'] . ' (' . $datos['CDG_SUCURSAL'] . ')', 0, 1, '');

        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 0, str_repeat('_', 32), 0, 1, 'C');
        $mpdf->Ln(5);

        // DATOS CLIENTE
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
        $mpdf->Ln(5);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Cell(60, 4, $datos['ENTREGA'] .  " $" . number_format($datos['MONTO'], 2, '.', ','), 0, 1, 'C');
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
        $mpdf->Cell(30, 10, $datos['ES_DEPOSITO'], 0);
        $mpdf->Cell(30, 10,  "$" . number_format($datos['MONTO'], 2, '.', ','), 2, 0, 'R');
        $mpdf->Ln(8);
        if ($datos['COMISION'] > 0) {
            $mpdf->Cell(30, 10, 'COMISIÓN :', 0);
            $mpdf->Cell(30, 10,  "$" . number_format($datos['COMISION'], 2, '.', ','), 2, 0, 'R');
            $mpdf->Ln(8);
        }
        $mpdf->Cell(30, 10, 'SALDO NUEVO: ', 0);
        $mpdf->Cell(30, 10, "$" . number_format($datos['SALDO_NUEVO'], 2, '.', ','), 2, 0, 'R');

        // Linea
        $mpdf->Ln(10);
        $mpdf->SetFont('Helvetica', '', 12);
        $mpdf->Ln(3);
        $mpdf->Cell(60, 0, str_repeat('*', 35), 0, 1, 'C');

        // FIRMAS
        $mpdf->Ln(8);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(60, 4, 'Firma de conformidad del cliente', 0, 1, 'C');
        $mpdf->Ln(10);
        $mpdf->Cell(60, 0, str_repeat('_', 34), 0, 1, 'C');

        // FOLIO DE LA OPERACION
        $mpdf->Ln(12);
        $mpdf->SetFont('Helvetica', '', 10);
        $mpdf->Cell(60, 4, 'FOLIO DE LA OPERACIÓN', 0, 1, 'C');
        $mpdf->WriteHTML('<barcode code="' . $ticket . '-' . $datos['CODIGO'] . '-' . $datos['MONTO'] . '-' . $datos['COD_EJECUTIVO'] . '" type="C128A" size=".60" height="1" class=""/>');

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
        exit;
    }

    public function Contrato()
    {
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
            <h1>Contrato de Cuenta de Ahorro</h1>
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

        $mpdf = new \mPDF('c');
        $mpdf->SetHTMLHeader('<div style="text-align:right; font-size: 10px;">Fecha de impresión  ' . date('d/m/Y H:i:s') . '</div>');
        $mpdf->SetHTMLFooter('<div style="text-align:center; font-size: 11px;">Página {PAGENO} de {nb}</div>');
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML($style, 1);
        $mpdf->WriteHTML($tabla, 2);

        $mpdf->Output($nombreArchivo . '.pdf', 'I');

        exit;
    }

    //********************BORRAR????********************//

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
