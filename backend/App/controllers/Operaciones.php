<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Operaciones as OperacionesDao;

class Operaciones extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    function CierreDiario()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                const tabla = "correos"

                const inicioCierreDiario = () => {
                    confirmarMovimiento(
                        "Cierre diario",
                        "¿Está seguro de querer procesar el cierre del día " + diaMsg() + "?",
                        "Iniciar proceso de cierre diario."
                    ).then((continuar) => {
                        if (!continuar) return
                        procesaCierreDiario()
                    })
                }

                const procesaCierreDiario = () => {
                    const fecha = $("#fecha").val()
                    showInfo("Procesando cierre diario, espere un momento...")
                    return

                    consultaServidor("/operaciones/ProcesaCierreDiario", { fecha }, (respuesta) => {
                        if (!respuesta.success) return showError(respuesta.mensaje)

                        showSuccess(respuesta.mensaje)
                    })
                }

                const diaMsg = () => {
                    let [anio, mes, dia] = $("#fecha").val().split("-")
                    const fecha = new Date(parseInt(anio), parseInt(mes) - 1, parseInt(dia))
                    const diasSemana = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"]
                    const meses = [
                        "enero",
                        "febrero",
                        "marzo",
                        "abril",
                        "mayo",
                        "junio",
                        "julio",
                        "agosto",
                        "septiembre",
                        "octubre",
                        "noviembre",
                        "diciembre"
                    ]

                    const diaSemana = diasSemana[fecha.getDay()]
                    dia = fecha.getDate()
                    mes = meses[fecha.getMonth()]
                    anio = fecha.getFullYear()

                    return diaSemana + " " + dia + " de " + mes + " del " + anio
                }

                $(document).ready(() => {
                    configuraTabla(tabla)

                    $("#procesar").click(() => inicioCierreDiario())

                    $("#agregar").click(() => {
                        $("#modalAgregaCorreo").modal("show")
                    })
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader('Cierre diario')));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('operaciones_cierre_diario');
    }

    function ProcesaCierreDiario()
    {
        $fecha = $_POST['fecha'] ?? null;
        if (!$fecha) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'No se ha indicado la fecha para el cierre diario.'
            ]);
            return;
        }

        // pclose(popen("start /B " . $cmd, "r"));
    }
}
