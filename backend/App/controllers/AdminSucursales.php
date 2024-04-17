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
        View::render("en_construccion");
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

    public function Configuracion()
    {
        $extraFooter = <<<html
       
html;

        $opciones_suc = '';

        $ComboSucursales = AdminSucursalesDao::getComboSucursalesHorario();


        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Saldo Diario")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opciones_suc', $opciones_suc);
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_configurar");
    }
}
