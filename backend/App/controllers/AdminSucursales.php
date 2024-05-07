<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\AdminSucursales as AdminSucursalesDao;
use \App\models\CajaAhorro as CajaAhorroDao;
use Exception;

class AdminSucursales extends Controller
{
    private $_contenedor;
    private $showError = 'const showError = (mensaje) => swal({ text: mensaje, icon: "error" })';
    private $showSuccess = 'const showSuccess = (mensaje) => swal({ text: mensaje, icon: "success" })';
    private $showInfo = 'const showInfo = (mensaje) => swal({ text: mensaje, icon: "info" })';
    private $noSubmit = 'const noSUBMIT = (e) => e.preventDefault()';
    private $validarYbuscar = 'const validarYbuscar = (e) => {
        if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
        if (e.keyCode === 13) buscar()
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
    private $consultaServidor = 'const consultaServidor = (url, datos, fncOK, metodo = "POST", tipo = "json") => {
        swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
        $.ajax({
            type: metodo,
            url: url,
            data: datos,
            success: (res) => {
                if (tipo === "json") {
                    try {
                        res = JSON.parse(res)
                    } catch (error) {
                        console.error(error)
                        res =  {
                            success: false,
                            mensaje: "Ocurrió un error al procesar la respuesta del servidor."
                        }
                    }
                } else if (tipo === "html") res = res

                swal.close()
                fncOK(res)
            },
            error: (error) => {
                console.error(error)
                showError("Ocurrió un error al procesar la solicitud.")
                swal.close()
            }
        })
    }';
    private $addParametro = 'const addParametro = (parametros, newParametro, newValor) => {
        parametros.push({ name: newParametro, value: newValor })
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
    private $parseaNumero = 'const parseaNumero = (numero) => parseFloat(numero.replace(/-[^0-9.]/g, "")) || 0';
    private $formatoMoneda = 'const formatoMoneda = (numero) => parseFloat(numero).toLocaleString("es-MX", { style: "currency", currency: "MXN" })';
    private $configuraTabla = 'const configuraTabla = (id) => {
        $("#" + id).tablesorter()
        $("#" + id).DataTable({
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

        $("#"  + id + " input[type=search]").keyup(() => {
            $("#example")
                .DataTable()
                .search(jQuery.fn.DataTable.ext.type.search.html(this.value))
                .draw()
        })
    }';

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    //********************Saldos y movimientos de efectivo en sucursal********************//
    // Reporte de saldos diarios por sucursal
    public function SaldosDiarios()
    {
        $extraFooter = <<<html
       
        html;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Saldo Diario")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }

    // Validar Transacciones Día
    public function CierreDia()
    {
        $extraFooter = <<<script
       
script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Arqueo de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_cierre_dia");

    }

    // Ingreso de efectivo a sucursal
    public function FondearSucursal()
    {
        $extraFooter = <<<script
        <script>
            let montoMaximo = 0
            let montoMinimo = 0
            let valKD = false
            let codigoSEA = 0
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->validarYbuscar}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
         
            const buscar = () => {
                const sucursal = document.querySelector("#sucursalBuscada").value
                if (sucursal === "0") return showError("Seleccione una sucursal")
                consultaServidor(
                    "/AdminSucursales/GetDatos/",
                    { sucursal },
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        if (parseFloat(res.datos.SALDO) >= parseFloat(res.datos.MONTO_MAX)) return showError("La sucursal " + sucursal + " cuenta con el saldo máximo permitido (" + parseFloat(res.datos.MONTO_MAX).toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + ") para su operación.")
                        document.querySelector("#sucursalBuscada").value = ""
                        document.querySelector("#codigoSuc").value = res.datos.CODIGO_SUCURSAL
                        document.querySelector("#nombreSuc").value = res.datos.NOMBRE_SUCURSAL
                        document.querySelector("#codigoCajera").value = res.datos.CODIGO_CAJERA
                        document.querySelector("#nombreCajera").value = res.datos.NOMBRE_CAJERA
                        document.querySelector("#fechaCierre").value = res.datos.FECHA_CIERRE
                        document.querySelector("#saldoActual").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#montoOperacion").value = "0.00"
                        document.querySelector("#saldoFinal").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#monto").disabled = false
                        document.querySelector("#monto").focus()
                        montoMinimo = parseFloat(res.datos.MONTO_MIN)
                        montoMaximo = parseFloat(res.datos.MONTO_MAX)
                        codigoSEA = res.datos.CODIGO
                    }
                )
            }
             
            const limpiarCampos = () => {
                document.querySelector("#codigoSuc").value = ""
                document.querySelector("#nombreSuc").value = ""
                document.querySelector("#codigoCajera").value = ""
                document.querySelector("#nombreCajera").value = ""
                document.querySelector("#fechaCierre").value = ""
                document.querySelector("#saldoActual").value = "0.00"
                document.querySelector("#montoOperacion").value = "0.00"
                document.querySelector("#saldoFinal").value = "0.00"
                document.querySelector("#monto").value = ""
                document.querySelector("#monto").disabled = true
            }
             
            const validaMonto = () => {
                const montoIngresado = document.querySelector("#monto")
                if (!parseFloat(montoIngresado.value)) {
                    document.querySelector("#btnFondear").disabled = true
                    document.querySelector("#saldoFinal").value = document.querySelector("#saldoActual").value
                    document.querySelector("#montoOperacion").value = "0.00"
                    return
                }
                 
                let monto = parseFloat(montoIngresado.value) || 0
                let disponible = montoMaximo - parseFloat(document.querySelector("#saldoActual").value)
                 
                if (monto > disponible) {
                    monto = disponible
                    showError("La sucursal no puede tener un saldo mayor a " + montoMaximo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + ", si requiere un monto mayor comuníquese con el administrador.")
                    montoIngresado.value = monto
                }
                 
                const valor = montoIngresado.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    montoIngresado.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                 
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                const nuevoSaldo = (monto + parseFloat(document.querySelector("#saldoActual").value)).toFixed(2)
                document.querySelector("#saldoFinal").value = nuevoSaldo > 0 ? nuevoSaldo : "0.00"
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(montoIngresado.value))
                document.querySelector("#btnFondear").disabled = !(nuevoSaldo <= montoMaximo && nuevoSaldo >= montoMinimo)
                document.querySelector("#tipSaldo").innerText = ""
                if (nuevoSaldo > montoMaximo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser mayor a " + montoMaximo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                if (nuevoSaldo < montoMinimo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser menor a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
            }
             
            const fondear = () => {
                const monto = parseFloat(document.querySelector("#saldoFinal").value)
                if (monto < montoMinimo) return showError("El saldo final debe ser mayor o igual a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }))
                
                let datos = $("#datos").serializeArray()
                addParametro(datos, "codigoSEA", codigoSEA)
                addParametro(datos, "usuario", '{$_SESSION["usuario"]}')
                 
                consultaServidor(
                    "/AdminSucursales/AplicarFondeo/",
                    datos,
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        showSuccess(res.mensaje).then(() => {
                            window.location.reload()
                        })
                    }
                )
            }
        </script>
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Arqueo de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_admin_fondeo");
    }

    public function AplicarFondeo()
    {
        $res = AdminSucursalesDao::AplicarFondeo($_POST);
        echo $res;
    }

    // Egreso de efectivo de sucursal
    public function RetiroSucursal()
    {
        $extraFooter = <<<script
        <script>
            let montoMaximo = 0
            let montoMinimo = 0
            let valKD = false
            let codigoSEA = 0
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->validarYbuscar}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
         
            const buscar = () => {
                const sucursal = document.querySelector("#sucursalBuscada").value
                if (sucursal === "0") return showError("Seleccione una sucursal")
                consultaServidor(
                    "/AdminSucursales/GetDatos/",
                    { sucursal },
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        document.querySelector("#sucursalBuscada").value = ""
                        document.querySelector("#codigoSuc").value = res.datos.CODIGO_SUCURSAL
                        document.querySelector("#nombreSuc").value = res.datos.NOMBRE_SUCURSAL
                        document.querySelector("#codigoCajera").value = res.datos.CODIGO_CAJERA
                        document.querySelector("#nombreCajera").value = res.datos.NOMBRE_CAJERA
                        document.querySelector("#fechaCierre").value = res.datos.FECHA_CIERRE
                        document.querySelector("#saldoActual").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#montoOperacion").value = "0.00"
                        document.querySelector("#saldoFinal").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#monto").disabled = false
                        document.querySelector("#monto").focus()
                        montoMinimo = parseFloat(res.datos.MONTO_MIN)
                        montoMaximo = parseFloat(res.datos.MONTO_MAX)
                        codigoSEA = res.datos.CODIGO
                    }
                )
            }
             
            const limpiarCampos = () => {
                document.querySelector("#codigoSuc").value = ""
                document.querySelector("#nombreSuc").value = ""
                document.querySelector("#codigoCajera").value = ""
                document.querySelector("#nombreCajera").value = ""
                document.querySelector("#fechaCierre").value = ""
                document.querySelector("#saldoActual").value = "0.00"
                document.querySelector("#montoOperacion").value = "0.00"
                document.querySelector("#saldoFinal").value = "0.00"
                document.querySelector("#monto").value = ""
                document.querySelector("#monto").disabled = true
                document.querySelector("#tipSaldo").innerText = ""
            }
             
            const validaMonto = () => {
                const montoIngresado = document.querySelector("#monto")
                if (!parseFloat(montoIngresado.value)) {
                    document.querySelector("#btnFondear").disabled = true
                    document.querySelector("#montoOperacion").value = "0.00"
                    return
                }
                
                let monto = parseFloat(montoIngresado.value) || 0
                const saldoActual = parseFloat(document.querySelector("#saldoActual").value)
                let nuevoSaldo = saldoActual - monto
                 
                if (nuevoSaldo < montoMinimo) {
                    monto = saldoActual - montoMinimo
                    nuevoSaldo = saldoActual - monto
                    showError("La sucursal no puede tener un saldo menor a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + ", si requiere que la sucursal tenga un monto menor comuníquese con el administrador.")
                    montoIngresado.value = monto
                }
                 
                const valor = montoIngresado.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    montoIngresado.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                 
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                document.querySelector("#saldoFinal").value = nuevoSaldo > 0 ? nuevoSaldo.toFixed(2) : "0.00"
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(montoIngresado.value))
                document.querySelector("#btnFondear").disabled = !(nuevoSaldo <= montoMaximo && nuevoSaldo >= montoMinimo)
                document.querySelector("#tipSaldo").innerText = ""
                if (nuevoSaldo > montoMaximo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser mayor a " + montoMaximo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                if (nuevoSaldo < montoMinimo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser menor a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
            }
             
            const retirar = () => {
                const monto = parseFloat(document.querySelector("#saldoFinal").value)
                if (monto < montoMinimo) return showError("El saldo final debe ser mayor o igual a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }))
                
                let datos = $("#datos").serializeArray()
                addParametro(datos, "codigoSEA", codigoSEA)
                addParametro(datos, "usuario", '{$_SESSION["usuario"]}')
                 
                consultaServidor(
                    "/AdminSucursales/AplicarRetiro/",
                    datos,
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        showSuccess(res.mensaje).then(() => {
                            window.location.reload()
                        })
                    }
                )
            }
        </script>
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Retiro de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_admin_retiro");
    }

    public function AplicarRetiro()
    {
        $res = AdminSucursalesDao::AplicarRetiro($_POST);
        echo $res;
    }

    public function GetDatos()
    {
        $datos = AdminSucursalesDao::GetDatosFondeoRetiro($_POST);
        echo $datos;
    }

    //********************Log de transacciones de ahorro********************//
    // Reporte de trnasacciones 
    public function Log()
    {
        $extraFooter = <<<script
        <script>
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
         
            const getLog = () => {
                const datos = {
                    fecha_inicio: $("#fInicio").val(),
                    fecha_fin: $("#fFin").val()
                }
                 
                const op = document.querySelector("#operacion")
                const us = document.querySelector("#usuario")
                
                if (op.value !== "0") datos.operacion = op.options[op.selectedIndex].text
                if (us.value !== "0") datos.usuario = us.options[us.selectedIndex].text
                 
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/GetLogTransacciones/",
                    data: datos,
                    success: (log) => {
                        $("#log").DataTable().destroy()
                         
                        log = JSON.parse(log)
                        let datos = log.datos
                         
                        if (!log.success) {
                            showError(log.mensaje)
                            datos = []
                        }
                        
                        $("#log tbody").html(creaFilas(datos))
                        $("#log").DataTable({
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
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al buscar el log de transacciones.")
                    }
                })
                 
                return false
            }
             
            const creaFilas = (datos) => {
                const filas = document.createDocumentFragment()
                datos.forEach((dato) => {
                    const fila = document.createElement("tr")
                    Object.keys(dato).forEach((key) => {
                        const celda = document.createElement("td")
                        celda.innerText = dato[key]
                        fila.appendChild(celda)
                    })
                    filas.appendChild(fila)
                })
                return filas
            }
             
            $(document).ready(() => {
                getLog()
            })
        </script>
        script;

        $operaciones = CajaAhorroDao::GetOperacionesLog();
        $usuarios = CajaAhorroDao::GetUsuariosLog();
        $sucursales = CajaAhorroDao::GetSucursalesLog();

        $opcOperaciones = "<option value='0'>Todas</option>";
        foreach ($operaciones as $key => $operacion) {
            $i = $key + 1;
            $opcOperaciones .= "<option value='{$i}'>{$operacion['TIPO']}</option>";
        }

        $opcUsuarios = "<option value='0'>Todos</option>";
        foreach ($usuarios as $key => $usuario) {
            $i = $key + 1;
            $opcUsuarios .= "<option value='{$i}'>{$usuario['USUARIO']}</option>";
        }

        $opcSucursales = "<option value='0'>Todas</option>";
        foreach ($sucursales as $key => $sucursal) {
            $i = $key + 1;
            $opcSucursales .= "<option value='{$i}'>{$sucursal['NOMBRE']}</option>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Log Transacciones Ahorro")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcOperaciones', $opcOperaciones);
        View::set('opcUsuarios', $opcUsuarios);
        View::set('opcSucursales', $opcSucursales);
        View::set(('fecha'), date('Y-m-d'));
        View::render("caja_admin_log");
    }

    public function GetLogTransacciones()
    {
        $log = CajaAhorroDao::GetLogTransacciones($_POST);
        echo $log;
    }

    //********************Activación de sucursales y cajeras********************//
    // Permite activar una sucursal y configurar los horarios de cajeras
    public function Configuracion()
    {
        $extraFooter = <<<script
        <script>
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->parseaNumero}
         
            $(document).ready(configuraTabla("sucursalesActivas"))
         
            const cambioSucursal = () => {
                consultaServidor(
                    "/AdminSucursales/GetCajeras/",
                    { sucursal: $("#sucursal").val() },
                    (datos) => {
                        if (!datos.success) return showError(datos.mensaje)
                        if (datos.datos.length === 0) {
                            $("#cajera").html("<option value='0' disabled selected>No hay cajeras en esta sucursal</option>")
                            $("#cajera").prop("disabled", true)
                        } else {
                            let opciones = "<option value='0' disabled selected>Seleccione una cajera</option>"
                            datos.datos.forEach((cajera) => {
                                opciones += "<option value='" + cajera.CODIGO + "'>" + cajera.NOMBRE + "</option>"
                            })
                            $("#cajera").html(opciones)
                            $("#cajera").prop("disabled", false)
                        }
                    }
                )
                     
                consultaServidor(
                    "/AdminSucursales/GetMontoSucursal/",
                    { sucursal: $("#sucursal").val() },
                    (datos) => {
                        if (!datos.success) return
                        if (datos.datos.length === 0) {
                            $("#montoMin").val("")
                            $("#montoMax").val("")
                        } else {
                            $("#montoMin").val(datos.datos[0].MONTO_MIN)
                            $("#montoMax").val(datos.datos[0].MONTO_MAX)
                        }
                    }
                )
            }
             
            const cambioCajera = () => {
                consultaServidor(
                    "/AdminSucursales/GetHorarioCajera/",
                    { cajera: $("#cajera").val() },
                    (datos) => {
                        if (datos.datos && datos.datos.length === 0) {
                            $("#horaA").val(datos.datos[0].HORA_APERTURA)
                            $("#horaC").val(datos.datos[0].HORA_CIERRE)
                            $("#montoMin").val(datos.datos[0].MONTO_MIN)
                            $("#montoMax").val(datos.datos[0].MONTO_MAX)
                        } else {
                            $("#horaA").select(0)
                            $("#horaC").select(0)
                            $("#montoMin").val("")
                            $("#montoMax").val("")
                        }
                    }
                )
                
                $("#horaA").prop("disabled", false)
                $("#horaC").prop("disabled", false)
                $("#montoMin").prop("disabled", false)
                $("#montoMax").prop("disabled", false)
                $("#saldo").prop("disabled", false)
            }
             
            const cambioMonto = () => {
                const min = parseFloat(document.querySelector("#montoMin").value) || 0
                const max = parseFloat(document.querySelector("#montoMax").value) || 0
                const inicial = parseFloat(document.querySelector("#saldo").value) || 0
                document.querySelector("#guardar").disabled = !(min > 0 && max > 0 && max >= min && inicial <= max)
            }
             
            const validaMaxMin = () => {
                const min = parseFloat(document.querySelector("#montoMin").value) || 0
                const max = parseFloat(document.querySelector("#montoMax").value) || 0
                if (min > max) document.querySelector("#montoMax").value = min
            }
             
            const activarSucursal = () => {
                consultaServidor(
                        "/AdminSucursales/ActivarSucursal/",
                        $("#datos").serialize(),
                        (res) => {
                            if (!res.success) return showError(res.mensaje)                            
                            showSuccess(res.mensaje).then(() => {
                                swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                                window.location.reload()
                            })
                        }
                    )
            }
             
            const llenarModal = () => {
                document.querySelector("#configMontos").reset()
                const fila = event.target.parentElement.parentElement
                document.querySelector("#codSucMontos").value = fila.children[1].innerText
                document.querySelector("#nomSucMontos").value = fila.children[2].innerText
                consultaServidor(
                    "/AdminSucursales/GetMontosApertura/",
                    { sucursal: fila.children[1].innerText },
                    (datos) => {
                        if (!datos.success) return
                        document.querySelector("#codigo").value = datos.datos.CODIGO
                        document.querySelector("#minimoApertura").value = datos.datos.MONTO_MINIMO
                        document.querySelector("#maximoApertura").value = datos.datos.MONTO_MAXIMO
                    }
                )
            }
             
            const validaMontoMinMax = (e) => {
                const m = parseFloat(e.target.value)
                if (m < 0) e.target.value = ""
                if (m > 1000000) e.target.value = "1000000.00"
                const valor = e.target.value.split(".")
                valor[1] = valor[1] || "00"
                if (valor[1] && valor[1].length > 2) e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
            }
             
            const guardarMontos = () => {
                consultaServidor(
                    "/AdminSucursales/GuardarParametrosSucursal/",
                    $("#configMontos").serialize(),
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        showSuccess(res.mensaje).then(() => {
                            swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                            window.location.reload()
                        })
                    }
                )
            }
        </script>
        script;

        $sucursales = AdminSucursalesDao::GetSucursales();
        $opcSucursales = "<option value='0' disabled selected>Seleccione una sucursal</option>";
        foreach ($sucursales as $key => $val2) {
            $opcSucursales .= "<option  value='" . $val2['CODIGO'] . "'>(" . $val2['CODIGO'] . ") " . $val2['NOMBRE'] . "</option>";
        }

        $sucActivas = AdminSucursalesDao::GetSucursalesActivas();
        $tabla = "";
        foreach ($sucActivas as $key => $val) {
            $tabla .= "<tr>";
            foreach ($val as $key2 => $val2) {
                if ($key2 === "ACCIONES") {
                    $tabla .= "<td style='vertical-align: middle; text-align: center;'><i class='fa fa-usd' title='Configurar montos' data-toggle='modal' data-target='#modal_configurar_montos' style='cursor: pointer;' onclick=llenarModal(event)></i></td>";
                } else {
                    $tabla .= "<td style='vertical-align: middle;'>{$val2}</td>";
                }
            }
            $tabla .= "</tr>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Configuración de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_admin_configurar");
    }

    public function GetMontoSucursal()
    {
        $monto = AdminSucursalesDao::GetMontoSucursal($_POST['sucursal']);
        echo $monto;
    }

    public function GetCajeras()
    {
        $cajeras = AdminSucursalesDao::GetCajeras($_POST['sucursal']);
        echo $cajeras;
    }

    public function GetHorarioCajera()
    {
        $horario = AdminSucursalesDao::GetHorarioCajera($_POST);
        echo $horario;
    }

    public function ActivarSucursal()
    {
        echo AdminSucursalesDao::ActivarSucursal($_POST);
    }

    public function GetMontosApertura()
    {
        echo AdminSucursalesDao::GetMontosApertura($_POST['sucursal']);
    }

    public function GuardarParametrosSucursal()
    {
        echo AdminSucursalesDao::GuardarParametrosSucursal($_POST);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    public function EstadoCuentaCliente()
    {
        $extraFooter = <<<script
        <script>
            let infoCliente = {}
            let vistaActiva = ""
         
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->validarYbuscar}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
            {$this->parseaNumero}
            {$this->formatoMoneda}
         
            const buscar = () => buscaCliente()
         
            const getVista = (vista) => {
                if (vista === "") return
                consultaServidor("/AdminSucursales/" + vista + "/", infoCliente, (res) => {
                    const cuerpo = document.querySelector("#cuerpoModal")
                    while(cuerpo.firstChild) {
                        cuerpo.firstChild.remove()
                    }
                     
                    const fragmento = document.createElement("template");
                    fragmento.innerHTML = res || ""
                     
                    const contenido = fragmento.content.querySelector(".modal-body")
                    const script = fragmento.content.querySelector("script")
                    
                    document.querySelector("#cuerpoModal").innerHTML = contenido.innerHTML
                    if (script) {
                        const nuevoScript = document.createElement("script")
                        nuevoScript.innerHTML = script.innerHTML
                        document.querySelector("#cuerpoModal").appendChild(nuevoScript)
                    }
                }, "POST", "html")
            }
             
            const llenaDatosCliente = (datos) => {
                infoCliente = datos
                if (vistaActiva) return document.querySelector("#cuerpoModal").innerText = getVista(vistaActiva)
                const opciones = document.querySelector("#opcionesCat").querySelectorAll("li")
                opciones.forEach((opcion) => {
                    if (!opcion.classList.contains("linea")) vistaActiva = opcion.children[0].id
                })
                document.querySelector("#cuerpoModal").innerText = getVista(vistaActiva)
            }
             
            const limpiaDatosCliente = () => {
                infoCliente = {}
                document.querySelector("#cuerpoModal").innerHTML = ""
            }
             
            const actualizaVista = (e) => {
                if (infoCliente.CDGCL === undefined) return showError("No se ha realizado la búsqueda de un cliente.")
                if (vistaActiva === e.target.id) return
                 
                vistaActiva = e.target.id
                reiniciaOpciones()
                e.target.parentElement.classList.remove("linea")
                e.target.style.fontWeight = "bold"
                document.querySelector("#cuerpoModal").innerHTML = ""
                document.querySelector("#cuerpoModal").innerText = getVista(vistaActiva)
            }
             
            const reiniciaOpciones = () => {
                const opciones = document.querySelector("#opcionesCat").querySelectorAll("li")
                opciones.forEach((opcion) => {
                    opcion.classList.add("linea")
                    opcion.children[0].style.fontWeight = "normal"
                })
            }
        </script>
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Catalogo de Clientes")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_admin_clientes");
    }

    public function ResumenCuenta()
    {
        $script = <<<script
        <script>
            configuraTabla = () => {
                $("#tablaResumenCta").tablesorter()
                $("#tablaResumenCta").DataTable({
                    lengthMenu: [
                        [10, 50, -1],
                        [10, 50, "Todos"]
                    ],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: 0
                        }
                    ],
                    order: false
                })
                $("#tablaResumenCta input[type=search]").keyup(() => {
                    $("#example")
                        .DataTable()
                        .search(jQuery.fn.DataTable.ext.type.search.html(this.value))
                        .draw()
                })
            }
             
            $(document).ready(() => configuraTabla())
        </script>
        script;

        $movimientos = self::ListaMovimientos($_POST);

        View::set('script', $script);
        View::set('cliente', $_POST['CDGCL']);
        View::set('nombre', $_POST['NOMBRE']);
        View::set('filas', $movimientos['filas']);
        View::set('conteoAbonos', $movimientos['conteoAbonos']);
        View::set('conteoCargos', $movimientos['conteoCargos']);
        View::set('conteoTotal', $movimientos['conteoTotal']);
        View::set('conteoTransferencias', $movimientos['conteoTransferencias']);
        View::set('montoAbonos', $movimientos['montoAbonos']);
        View::set('montoCargos', $movimientos['montoCargos']);
        View::set('montoTransferencias', $movimientos['montoTransferencias']);
        View::set('saldoFinal', $movimientos['saldoFinal']);
        View::set('filas', $movimientos['filas']);
        echo View::fetch("caja_admin_clientes_resumenCta");
    }

    public function ListaMovimientos($d = null)
    {
        $datos = $d ? $d : $_POST;
        $registros = AdminSucursalesDao::ResumenCuenta($datos);
        $conteoCargos = 0;
        $conteoAbonos = 0;
        $montoCargos = 0;
        $montoAbonos = 0;
        $conteoTotal = 0;
        $conteoTransferencias = 0;
        $montoTransferencias = 0;
        $saldoFinal = null;

        $filas = "";
        foreach ($registros as $key => $registro) {
            $filas .= "<tr>";
            $conteoTotal++;
            $saldoFinal = $registro['SALDO'];
            if ($registro['ABONO'] > 0) {
                $conteoAbonos++;
                $montoAbonos += $registro['ABONO'];
            } else {
                if ($registro['TIPO'] == 5) {
                    $conteoTransferencias++;
                    $montoTransferencias += $registro['CARGO'];
                } else {
                    $conteoCargos++;
                    $montoCargos += $registro['CARGO'];
                }
            }
            foreach ($registro as $key2 => $celda) {
                if ($key2 === "TIPO") continue;
                if ($key2 === "ABONO" || $key2 === "CARGO" || $key2 === "SALDO") {
                    $filas .= "<td style='vertical-align: middle; text-align: right;'>$ " .  number_format($celda, 2, '.', ',') . "</td>";
                } elseif ($key2 === "DESCRIPCION") {
                    $filas .= "<td style='vertical-align: middle; text-align: left;'>{$celda}</td>";
                } else {
                    $filas .= "<td style='vertical-align: middle;'>{$celda}</td>";
                }
            }
            $filas .= "</tr>";
        }

        $respuesta = [
            "conteoAbonos" => $conteoAbonos,
            "conteoCargos" => $conteoCargos,
            "conteoTransferencias" => $conteoTransferencias,
            "conteoTotal" => $conteoTotal,
            "montoAbonos" => $montoAbonos,
            "montoCargos" => $montoCargos,
            "montoTransferencias" => $montoTransferencias,
            "saldoFinal" => $saldoFinal,
            "filas" => $filas
        ];

        if ($d !== null) return $respuesta;
        echo json_encode($respuesta);
    }

    public function HistorialTrns()
    {
        View::set('cliente', $_POST['CDGCL']);
        View::set('nombre', $_POST['NOMBRE']);
        echo View::fetch("caja_admin_clientes_historialTrns");
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    public function Reporteria()
    {
        $extraFooter = <<<script
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
                  "lengthMenu": [
                    [4, 50, -1],
                    [4, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
             
        });
        
          
        </script>
script;


        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro('');
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}'>{$sucursales['NOMBRE']} ({$sucursales['CODIGO']})</option>";
        }


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $Transacciones = CajaAhorroDao::GetAllTransacciones('');
        $tabla = "";
        foreach ($Transacciones as $key => $value) {
            $monto = number_format($value['MONTO'], 2);
            if ($value['CONCEPTO'] == 'TRANSFERENCIA INVERSION') {
                $concepto = '<i class="fa fa-minus" style="color: #0000ac;"></i>';
            } else if ($value['CONCEPTO'] == 'RETIRO') {
                $concepto = '<i class="fa fa-arrow-up" style="color: #ac0000;"></i>';
            } else {
                $concepto = '<i class="fa fa-arrow-down" style="color: #00ac00;"></i>';
            }
            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                
                    <td style="padding: 0px !important;">
                         <div style="margin-bottom: 5px;">CONTRATO: <b>{$value['CDG_CONTRATO']}</b></div>
                         <div>CODIGO CLIENTE SICAFIN: <b>{$value['CDGCL']}</b></div>
                         <div><b>{$value['TITULAR_CUENTA_EJE']}</b></div>
                         <div>SUCURSAL: <b>FALTA CORREGIR</b></div>
                    </td>
                    
                    <td style="padding: 0px !important;">
                         <div style="margin-bottom: 5px;">Producto: {$value['PRODUCTO']}</div>
                         <div style="margin-bottom: 5px; font-size: 15px;">{$concepto} $ {$monto}</div>
                         <div style="margin-bottom: 5px;"> <b>{$value['CONCEPTO']}</b></div>
                          <div style="margin-bottom: 5px;"><span class="fa fa-barcode"></span> <b>{$value['CDG_TICKET']}</b></div>
                    </td>
                    <td style="padding: 0px !important;">{$value['FECHA_MOV']} </td>
                    <td style="padding: 0px !important;">-</td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial de Transacciones")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        view::set('sucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::render("caja_admin_reporteria_transacciones");
    }

    public function ReporteriaTransacciones()
    {
        $extraFooter = <<<script
        <script>
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
        </script>
script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_reporteria_transacciones");
        View::render("caja_admin_reporteria");
    }


    public function SolicitudesReimpresionTicket()
    {
        $extraFooter = <<<script
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
                  "lengthMenu": [
                    [3, 50, -1],
                    [3, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [10, 50, -1],
                    [10, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
        
         
         function ReimpresionEstatus(valor, ticket)
         {
             if(valor == 1)
                 {
                      accion = 'AUTORIZAR';
                 }
             else if(valor == 2)
                 {
                      accion = 'RECHAZAR';
                 }
                 
                 swal({
                         title: "¿Está segur(a) de " + accion +" la solicitud de reimpresión del ticket?",
                         text: 'No podrá deshacer está acción. ',
                         icon: "warning",
                         buttons: ["Cancelar", "Continuar"],
                         dangerMode: false
                         })
                         .then((willDelete) => {
                         if (willDelete) {
                                     
                          $.ajax({
                                type: 'POST',
                                url: '/AdminSucursales/TicketSolicitudUpdate/',
                                data: {"valor" : valor, "ticket" : ticket},
                                success: function(respuesta) {
                                     if(respuesta=='1'){
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
                                });
                         
             
             
         }
        
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
            
            
        </script>
script;


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $Transacciones = CajaAhorroDao::GetSolicitudesPendientesAdminAll();

        foreach ($Transacciones as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 15px !important;"><span class="fa fa-barcode"></span> {$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div style="text-align: left; margin-left: 10px; margin-top: 5px;">
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b>CLIENTE: </b>{$value['NOMBRE_CLIENTE']}
                        </div>
                        
                        <hr style="margin-bottom: 8px; margin-top: 8px;">
                        
                         <div style="text-align: left; margin-left: 10px;">
                            <b>MOTIVO: </b>{$value['MOTIVO']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> CAJERA QUE REALIZA SOLICITUD: </b>{$value['NOMBRE_CAJERA']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> DESCRIPCION CAJERA: </b>{$value['DESCRIPCION_MOTIVO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-calendar-check-o"></span> FECHA DE SOLICITUD: </b>{$value['FREGISTRO']}
                        </div> 
                        
                    </td>
                    <td style="padding: 10px!important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="ReimpresionEstatus('1','{$value['CODIGO_REIMPRIME']}')"><i class="fa fa-check-circle"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="ReimpresionEstatus('2','{$value['CODIGO_REIMPRIME']}');"><i class="fa fa-close"></i></button>
                    </td>
                </tr>
html;
        }

        $TransaccionesHistorial = CajaAhorroDao::GetSolicitudesHistorialAdminAll();

        foreach ($TransaccionesHistorial as $key_ => $valueh) {
            if ($valueh['AUTORIZA'] == '1') {
                $estatus = 'ACEPTADO';
                $color = '#31BD16';
            } else {
                $estatus = 'RECHAZADO';
                $color = '#9C1508';
            }

            $tabla_his .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 15px !important;"><span class="fa fa-barcode"></span> {$valueh['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div style="text-align: left; margin-left: 10px; margin-top: 5px;">
                            <b>CONTRATO:</b> {$valueh['CONTRATO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b>CLIENTE: </b>{$valueh['NOMBRE_CLIENTE']}
                        </div>
                        
                        <hr style="margin-bottom: 8px; margin-top: 8px;">
                        
                         <div style="text-align: left; margin-left: 10px;">
                            <b>MOTIVO: </b>{$valueh['MOTIVO']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> CAJERA QUE REALIZA SOLICITUD: </b>{$valueh['NOMBRE_CAJERA']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> DESCRIPCION CAJERA: </b>{$valueh['DESCRIPCION_MOTIVO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-calendar-check-o"></span> FECHA DE SOLICITUD: </b>{$valueh['FREGISTRO']}
                        </div> 
                        
                    </td>
                    <td style="padding: 15px !important;"> 
                    
                        <div> <b>ESTATUS:</b> <b style="color: {$color};">{$estatus}</b> </div>
                        <div> <b>AUTORIZA:</b> ({$valueh['CDGPE_AUTORIZA']}) {$valueh['TESORERIA']}</div>
                        <br>
                        <div><b><span class="fa fa-calendar-check-o"></span> FECHA DE AUTORIZACIÓN:</b> ({$valueh['FAUTORIZA']})</div>
                        
                    </td>
                  
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::set('tabla', $tabla);
        View::set('tabla_his', $tabla_his);
        View::render("caja_admin_solicitudes");
    }

    public function SolicitudResumenMovimientos()
    {
        $extraFooter = <<<script
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
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
        
        
        
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
            
            
        </script>
script;


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $Transacciones = CajaAhorroDao::GetSolicitudesPendientesAdminAll();

        foreach ($Transacciones as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['NOMBRE_CLIENTE']}
                        </div>
                    </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        view::set('sucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::render("caja_admin_solicitudes_resumen_movimientos");
    }

    public function SolicitudRetiroOrdinario()
    {
        $extraFooter = <<<script
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
                  "lengthMenu": [
                    [2, 50, -1],
                    [2, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [2, 50, -1],
                    [2, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
            
        </script>
script;

        $tabla =  "";
        $SolicitudesOrdinarias = CajaAhorroDao::GetSolicitudesRetiroAhorroOrdinario();

        foreach ($SolicitudesOrdinarias as $key => $value) {

            $cantidad_formateada = number_format($value['CANTIDAD_SOLICITADA'], 2, '.', ',');
            if($value['TIPO_PRODUCTO'] == 'AHORRO CORRIENTE')
            {
                $img =  '<img src="https://cdn-icons-png.flaticon.com/512/5575/5575939.png" style="border-radius: 3px; padding-top: 5px;" width="33" height="35">';
            }
            else
            {
                $img =  '<img src="https://cdn-icons-png.flaticon.com/512/2995/2995467.png" style="border-radius: 3px; padding-top: 5px;" width="33" height="35">';
            }

            $tabla .= <<<html
                <tr style="padding: 15px!important;">
                    <td style="padding: 15px!important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['CLIENTE']}
                        </div>
                         <div>
                            <b>SUCURSAL: </b>{NOS FALTA}
                        </div>
                    </td>
                    <td style="padding: 15px!important;">
                     <div>
                            <b>FECHA PREVISTA ENTREGA:</b> {$value['FECHA_SOLICITUD']}
                        </div>
                        <div>
                            <b>CANTA SOLICITADA: </b>$ {$cantidad_formateada}
                        </div>
                        <div>
                            <b>TIPO DE PRODUCTO: </b>{$value['TIPO_PRODUCTO']} {$img}
                        </div>
                        <hr>
                         <div>
                            <b>ESTATUS DE LA SOLICITUD: </b>{$value['SOLICITUD_VENCIDA']}
                        </div>
                         <div>
                            <b>CAJERA SOLICITA: </b>{NOS FALTA}
                        </div>
                     </td>
                    <td style="padding: 0px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        view::set('sucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::render("caja_admin_solicitudes_retiro_ordinario");
    }

    public function SolicitudRetiroExpress()
    {
        $extraFooter = <<<script
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
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
        
        
        
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
            
            
        </script>
script;


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $Transacciones = CajaAhorroDao::GetSolicitudesPendientesAdminAll();

        foreach ($Transacciones as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['NOMBRE_CLIENTE']}
                        </div>
                    </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        view::set('sucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::render("caja_admin_solicitudes_retiro_express");
    }

    public function SolicitudRetiroEfectivoCaja()
    {
        $extraFooter = <<<script
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
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
        
        
        
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
            
            
        </script>
script;


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $Transacciones = CajaAhorroDao::GetSolicitudesPendientesAdminAll();

        foreach ($Transacciones as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['NOMBRE_CLIENTE']}
                        </div>
                        
                        <div>
                            <b>SUCURSAL: </b> FALTA CORREGIR
                        </div>
                    </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        view::set('sucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::render("caja_admin_solicitudes_retirar_efectivo_sucursal");
    }

    public function TicketSolicitudUpdate()
    {
        $solicitud = new \stdClass();

        $solicitud->_valor = MasterDom::getDataAll('valor');
        $solicitud->_ticket = MasterDom::getData('ticket');

        $id = CajaAhorroDao::AutorizaSolicitudtICKET($solicitud, $this->__usuario);

        echo $id;
    }

    public function ConfiguracionUsuarios()
    {
        $extraFooter = <<<script
        <script>
           $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });        
        });
        </script>
script;

        $empleados = AdminSucursalesDao::GetUsuariosActivos();
        $opcEmpleados = "<option value='0' disabled selected>Seleccione una opción</option>";
        foreach ($empleados as $key => $val2) {
            $opcEmpleados .= "<option  value='" . $val2['CODIGO'] . "'>". $val2['EMPLEADO'] . "</option>";
        }

        $sucActivas = AdminSucursalesDao::GetSucursalesActivas();
        $tabla = "";
        foreach ($sucActivas as $key => $val) {
            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;"></td>
                    <td style="padding: 0px !important;"></td>
                    <td style="padding: 0px !important;"></td>
                    <td style="padding: 0px !important;"></td>
                    
                    <td style="padding: 0px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Configuración de Caja Usuarios")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcEmpleados', $opcEmpleados);
        View::set('tabla', $tabla);
        View::render("caja_admin_configurar_usuarios");
    }

    public function ConfiguracionParametros()
    {
        $extraFooter = <<<script
        <script>
         
        </script>
script;


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Configuración de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_admin_configurar_parametros");
    }

    public function HistorialFondeoSucursal()
    {
        $extraFooter = <<<script
        <script>
         
        </script>
script;


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial Fondeo Sucursal")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_admin_historial_fondeo");
    }

    public function HistorialRetiroSucursal()
    {
        $extraFooter = <<<script
        <script>
         
        </script>
script;


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial Retiro Sucursal")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_admin_historial_retiro_sucursal");
    }

    public function HistorialCierreDia()
    {
        $extraFooter = <<<script
        <script>
         
        </script>
script;


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial Cierre Día")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_admin_historial_cierre_dia");
    }

    public function LogConfiguracion()
    {
        $extraFooter = <<<script
        <script>
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
         
            const getLog = () => {
                const datos = {
                    fecha_inicio: $("#fInicio").val(),
                    fecha_fin: $("#fFin").val()
                }
                 
                const op = document.querySelector("#operacion")
                const us = document.querySelector("#usuario")
                
                if (op.value !== "0") datos.operacion = op.options[op.selectedIndex].text
                if (us.value !== "0") datos.usuario = us.options[us.selectedIndex].text
                 
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/GetLogTransacciones/",
                    data: datos,
                    success: (log) => {
                        $("#log").DataTable().destroy()
                         
                        log = JSON.parse(log)
                        let datos = log.datos
                         
                        if (!log.success) {
                            showError(log.mensaje)
                            datos = []
                        }
                        
                        $("#log tbody").html(creaFilas(datos))
                        $("#log").DataTable({
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
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al buscar el log de transacciones.")
                    }
                })
                 
                return false
            }
             
            const creaFilas = (datos) => {
                const filas = document.createDocumentFragment()
                datos.forEach((dato) => {
                    const fila = document.createElement("tr")
                    Object.keys(dato).forEach((key) => {
                        const celda = document.createElement("td")
                        celda.innerText = dato[key]
                        fila.appendChild(celda)
                    })
                    filas.appendChild(fila)
                })
                return filas
            }
             
            $(document).ready(() => {
                getLog()
            })
        </script>
        script;

        $operaciones = CajaAhorroDao::GetOperacionesLog();
        $usuarios = CajaAhorroDao::GetUsuariosLog();
        $sucursales = CajaAhorroDao::GetSucursalesLog();

        $opcOperaciones = "<option value='0'>Todas</option>";
        foreach ($operaciones as $key => $operacion) {
            $i = $key + 1;
            $opcOperaciones .= "<option value='{$i}'>{$operacion['TIPO']}</option>";
        }

        $opcUsuarios = "<option value='0'>Todos</option>";
        foreach ($usuarios as $key => $usuario) {
            $i = $key + 1;
            $opcUsuarios .= "<option value='{$i}'>{$usuario['USUARIO']}</option>";
        }

        $opcSucursales = "<option value='0'>Todas</option>";
        foreach ($sucursales as $key => $sucursal) {
            $i = $key + 1;
            $opcSucursales .= "<option value='{$i}'>{$sucursal['NOMBRE']}</option>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Log Transacciones Configuración")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcOperaciones', $opcOperaciones);
        View::set('opcUsuarios', $opcUsuarios);
        View::set('opcSucursales', $opcSucursales);
        View::set(('fecha'), date('Y-m-d'));
        View::render("caja_admin_log_configuracion");
    }
}
