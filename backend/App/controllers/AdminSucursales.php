<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\AdminSucursales as AdminSucursalesDao;
use \App\models\CajaAhorro as CajaAhorroDao;

class AdminSucursales extends Controller
{
    private $_contenedor;
    private $showError = 'const showError = (mensaje) => swal({ text: mensaje, icon: "error" })';
    private $showSuccess = 'const showSuccess = (mensaje) => swal({ text: mensaje, icon: "success" })';
    private $showInfo = 'const showInfo = (mensaje) => swal({ text: mensaje, icon: "info" })';
    private $validarYbuscar = 'const validarYbuscar = (e) => {
        if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
        if (e.keyCode === 13) buscar()
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
    private $consultaServidor = 'const consultaServidor = (url, datos, fncOK, metodo = "POST") => {
        swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false })
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
                swal.close()
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

    // Reporte de saldos diarios por sucursal
    public function ArqueoSucursal()
    {
        $extraFooter = <<<script
       
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Arqueo de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }

    // Ingreso de efectivo a sucursal
    public function FondearSucursal()
    {
        $extraFooter = <<<script
       
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Arqueo de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("en_construccion");
    }

    // Egreso de efectivo de sucursal
    public function RetiroSucursal()
    {
    }

    // Historial de movimientos de efectivo de sucursal
    public function Historial()
    {
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

    //********************BORRAR????********************//
    public function Configuracion()
    {
        $extraFooter = <<<script
        <script>
            {$this->showError}
            {$this->showSuccess}
            {$this->showInfo}
            {$this->soloNumeros}
            {$this->consultaServidor}
            let cjeraRegistrada = false
         
            const noSUBMIT = (e) => e.preventDefault()
         
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
                        // if (!datos.success) return showError(datos.mensaje)
                        if (datos.datos.length === 0) {
                            $("#horaA").val("")
                            $("#horaC").val("")
                            $("#montoMin").val("")
                            $("#montoMax").val("")
                        } else {
                            $("#horaA").val(datos.datos[0].HORA_APERTURA)
                            $("#horaC").val(datos.datos[0].HORA_CIERRE)
                            $("#montoMin").val(datos.datos[0].MONTO_MIN)
                            $("#montoMax").val(datos.datos[0].MONTO_MAX)
                        }
                    }
                )
                
                $("#horaA").prop("disabled", false)
                $("#horaC").prop("disabled", false)
                $("#montoMin").prop("disabled", false)
                $("#montoMax").prop("disabled", false)
            }
             
            const cambioMonto = () => {
                const min = parseFloat(document.querySelector("#montoMin").value) || 0
                const max = parseFloat(document.querySelector("#montoMax").value) || 0
                document.querySelector("#guardar").disabled = !(min > 0 && max > 0 && max >= min)
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
                            showSuccess(res.mensaje)
                            limpiarCampos()
                        }
                    )
            }
             
            const limpiarDatos = () => {
                document.querySelector("#datos").reset()
                document.querySelector("#cajera").innerHTML = "<option value='0' disabled selected>Seleccione una cajera</option>"
                document.querySelector("#cajera").disabled = true
                document.querySelector("#horaA").disabled = true
                document.querySelector("#horaC").disabled = true
                document.querySelector("#montoMin").disabled = true
                document.querySelector("#montoMax").disabled = true
            }
        </script>
        script;

        $opcSucursales = "<option value='0' disabled selected>Seleccione una sucursal</option>";
        $sucursales = AdminSucursalesDao::GetSucursales();


        foreach ($sucursales as $key => $val2) {

            $opcSucursales .= "<option  value='" . $val2['CODIGO'] . "'>(" . $val2['CODIGO'] . ") " . $val2['NOMBRE'] . "</option>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Configuración de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
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
        $res = AdminSucursalesDao::ActivarSucursal($_POST);
        echo $res;
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    public function EstadoCuentaCliente()
    {
        $extraFooter = <<<script
       
script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Estado de Cuenta Mensual")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_clientes");
    }


}
