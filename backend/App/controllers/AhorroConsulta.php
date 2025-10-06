<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\AhorroConsulta as AhorroConsultaDao;

class AhorroConsulta extends Controller
{
    private $_contenedor;

    public function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function index()
    {
        $extraFooter = <<<JAVASCRIPT
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->formatoMoneda}

                const idTabla = "tablaRetiros"

                const consultaSolicitudes = () => {
                    consultaServidor("/AhorroConsulta/GetRetirosAhorro", getPerametros(), (res) => {
                        if (!res.success) return resultadoError(res.mensaje)
                        resultadoOK(res.datos)
                    })
                }

                const getPerametros = () => {
                    const fechaI = $("#fechaI").val()
                    const fechaF = $("#fechaF").val()
                    const sucursal = $("#sucursal").val()

                    return { fechaI, fechaF, sucursal }
                }

                const resultadoOK = (datos) => {
                    datos = datos.map((item) => {
                        item.MONTO = "$ " + formatoMoneda(item.MONTO)
                        return item
                    })

                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
                }

                const verDetalle = (idRetiro) => {
                    $("#detalle_id_retiro").text(idRetiro);
                    $("#modalDetalle").modal("show");
                }

                const verComprobante = (idRetiro) => {
                    $("#modalComprobante").modal("show");
                }

                $(document).ready(function() {
                    $("#fechaI").change(consultaSolicitudes)
                    $("#fechaF").change(consultaSolicitudes)
                    $("#sucursal").change(consultaSolicitudes)

                    configuraTabla(idTabla)
                    consultaSolicitudes()
                });
            </script>
        JAVASCRIPT;


        $suc = AhorroConsultaDao::GetSucursales();
        $sucursales = '<option value="*">Todas</option>';
        if ($suc['success']) {
            foreach ($suc['datos'] as $sucursal) {
                $sucursales .= '<option value="' . $sucursal['ID_SUCURSAL'] . '">' . $sucursal['SUCURSAL'] . '</option>';
            }
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Ahorro Consulta")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('sucursales', $sucursales);
        View::render("ahorro_consulta");
    }

    public function GetRetirosAhorro()
    {
        echo json_encode(AhorroConsultaDao::getRetirosAhorro());
    }
}
