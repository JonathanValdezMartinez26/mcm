<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\CorreccionAjustes as CorreccionAjustesDao;



class CorreccionAjustes extends Controller
{

    private $_contenedor;


    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function index()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->configuraTabla}
                {$this->actualizaDatosTabla}
                {$this->consultaServidor}
                {$this->mensajes}
                {$this->confirmarMovimiento}
                {$this->formatoMoneda}

                const idTabla = "refinanciamientos"

                const buscarEnter = (e) => {
                    if (e.key === "Enter") buscarAjustes()
                }

                const inputError = (id, mensaje) => {
                    $("#" + id).toggleClass("incorrecto", true)
                    $("#" + id).focus()
                    resultadoError(mensaje)
                }

                const resultadoError = (mensaje) => {
                    $(".resultado").toggleClass("conDatos", false)
                    showError(mensaje).then(() => actualizaDatosTabla(idTabla, []))
                }

                const resultadoOK = (datos) => {
                    const datosEdit = {
                        credito: datos[0].CREDITO,
                        ciclo: datos[0].CICLO,
                        periodos: [],
                        secuencias: [],
                        razon: datos[0].RAZON,
                        descripcion: datos[0].RAZON_DESC,
                        fecha: datos[0].FECHA,
                        monto: 0
                    }

                    datos = datos.map((dato, fila) => {
                        datosEdit.monto += parseFloat(dato.CANTIDAD)
                        datosEdit.periodos.push(dato.PERIODO)
                        datosEdit.secuencias.push(dato.SECUENCIA)

                        const nuevo = [
                            dato.CREDITO,
                            dato.CICLO,
                            dato.RAZON_DESC,
                            dato.OBSERVACIONES,
                            dato.FECHA,
                            "$ " + formatoMoneda(dato.CANTIDAD),
                            dato.REFERENCIA
                        ]
                        
                        return nuevo
                    })

                    $("#datosEdit").val(JSON.stringify(datosEdit))
                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
                }

                const buscarAjustes = () => {
                    $("#muestraDatos").css("display", "none")

                    $("#creditoBuscar").toggleClass("incorrecto", false)
                    $("#cicloBuscar").toggleClass("incorrecto", false)
                    const credito = $("#creditoBuscar").val()
                    const ciclo = $("#cicloBuscar").val()

                    if (credito === "") return inputError("creditoBuscar", "Debe ingresar un número de crédito.")
                    if (isNaN(credito)) return inputError("creditoBuscar", "El número de crédito solo debe contener números.")
                    if (credito.length !== 6) return inputError("creditoBuscar", "El número de crédito debe tener 6 dígitos.")

                    if (ciclo === "") return inputError("cicloBuscar", "Debe ingresar un ciclo.")
                    if (isNaN(ciclo)) return inputError("cicloBuscar", "El ciclo debe ser numérico.")
                    if (ciclo.length !== 2) return inputError("cicloBuscar", "El ciclo debe tener 2 dígitos.")

                    consultaServidor("/CorreccionAjustes/GetAjustes", { credito, ciclo }, (resultado) => {
                        if (!resultado.success) return resultadoError(resultado.mensaje)
                        
                        const { datos } = resultado
                        if (datos.length === 0) return resultadoError("No se encontraron ajustes para el crédito " + credito + ".")

                        resultadoOK(datos)
                        $("#muestraDatos").css("display", "block")
                    })
                }

                const muestraModal = () => {
                    const datos = JSON.parse($("#datosEdit").val())

                    $("#credito").val(datos.credito)
                    $("#ciclo").val(datos.ciclo)
                    $("#monto").val("$ " + formatoMoneda(datos.monto))
                    $("#fecha").val(datos.fecha)
                    $("#razonActual").val(datos.descripcion)
                    $("#razon").val(datos.razon)

                    $("#modalRazones").modal("show")
                }

                const modificaRazon = () => {
                    const datos = JSON.parse($("#datosEdit").val())

                    if (datos.razon === $("#razon").val()) return showInfo("Se debe seleccionar una razón diferente.")

                    confirmarMovimiento("Corrección de ajuste", "¿Seguro desea cambiar la razón de este ajuste?")
                    .then((continuar) => {
                        if (!continuar) return
                        datos.razon = $("#razon").val()

                        consultaServidor("/CorreccionAjustes/ActualizaRazon", datos, (resultado) => {
                            if (!resultado.success) return showError(resultado.mensaje)
                            $("#modalRazones").modal("hide")

                            showSuccess(resultado.mensaje).then(() => {
                                buscarAjustes(credito, ciclo)
                            })
                        })
                    })
                }

                $(document).ready(() => {
                    configuraTabla("refinanciamientos")

                    $("#creditoBuscar").on("keypress", buscarEnter)
                    $("#cicloBuscar").on("keypress", buscarEnter)
                    $("#buscar").on("click", buscarAjustes)
                    $("#muestraDatos").on("click", muestraModal)
                    $("#modificar").on("click", modificaRazon)
                })
            </script>
        HTML;


        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Cancelación de Refinanciamientos')));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set("razones", $this->GetRazones());
        View::render("correccion_ajustes");
    }

    public function GetRazones()
    {
        $razones = CorreccionAjustesDao::GetRazones();
        if (!$razones['success']) return "";

        $opciones = "";
        foreach ($razones['datos'] as $razon) {
            $opciones .= "<option value='{$razon['CODIGO']}'>{$razon['DESCRIPCION']}</option>";
        }

        return $opciones;
    }

    public function GetAjustes()
    {
        echo json_encode(CorreccionAjustesDao::GetAjustes($_POST));
    }

    public function ActualizaRazon()
    {
        echo json_encode(CorreccionAjustesDao::ActualizaRazon($_POST));
    }
}
