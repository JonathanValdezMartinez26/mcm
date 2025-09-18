<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\RadarCobranza as RadarCobranzaDao;

class RadarCobranza extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function DashboardDia()
    {
        $extraFooter = <<<HTML
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}

                const validarToken = () => {
                    const authData = localStorage.getItem("radar_auth")
                    if (!authData) {
                        mostrarModalLogin()
                        return false
                    }

                    try {
                        const data = JSON.parse(authData)
                        if (!data.access_token) {
                            mostrarModalLogin()
                            return false
                        }
                        return data.access_token
                    } catch (e) {
                        mostrarModalLogin()
                        return false
                    }
                }

                const mostrarModalLogin = () => {
                    $("#loginModal").modal("show")
                }

                // Proceso de login
                const realizarLogin = () => {
                    const usuario = $("#usuario").val().trim()
                    const password = $("#password").val().trim()

                    if (!usuario || !password) {
                        showError("Por favor ingrese usuario y contraseña")
                        return
                    }

                    consultaServidor("/RadarCobranza/Login", { usuario, password }, (res) => {
                        if (!res.success) {
                            showError(res.mensaje)
                            return
                        }

                        // Guardar datos en localStorage
                        localStorage.setItem("radar_auth", JSON.stringify(res.datos))
                        $("#loginModal").modal("hide")
                        $("#btnCerrarSesion").show() // Mostrar botón de cerrar sesión
                        cargarDashboard()
                    })
                }

                // Cargar dashboard principal
                const cargarDashboard = () => {
                    const token = validarToken()
                    if (!token) return

                    consultaServidor("/RadarCobranza/GetResumenCobranza", { token }, (res) => {
                        if (!res.success) {
                            if (res.codigo === "TOKEN_EXPIRED") {
                                localStorage.removeItem("radar_auth")
                                mostrarModalLogin()
                            } else {
                                showError(res.mensaje)
                            }
                            return
                        }

                        renderizarDashboard(res.datos)
                    })
                }

                // Renderizar dashboard
                const renderizarDashboard = (data) => {
                    // Limpiar charts anteriores al renderizar nuevo dashboard
                    Object.keys(chartInstances).forEach(chartId => {
                        if (chartInstances[chartId]) {
                            chartInstances[chartId].destroy()
                            delete chartInstances[chartId]
                        }
                    })
                    
                    if (!data.por_dia) {
                        showError("No hay datos disponibles")
                        return
                    }

                    const diasSemana = ["LUNES", "MARTES", "MIERCOLES", "JUEVES", "VIERNES"]
                    const fechaActual = new Date()
                    let diaActual = ""

                    // Obtener el día actual en español
                    switch (fechaActual.getDay()) {
                        case 1:
                            diaActual = "LUNES"
                            break
                        case 2:
                            diaActual = "MARTES"
                            break
                        case 3:
                            diaActual = "MIERCOLES"
                            break
                        case 4:
                            diaActual = "JUEVES"
                            break
                        case 5:
                            diaActual = "VIERNES"
                            break
                        default:
                            diaActual = "" // Fin de semana
                    }

                    let accordionHTML = ""

                    diasSemana.forEach((dia, index) => {
                        const datosDia = data.por_dia[dia] || {}
                        const tieneDatos = Object.keys(datosDia).length > 0

                        let totales = 0,
                            cobrados = 0,
                            pendientes = 0

                        if (tieneDatos) {
                            Object.values(datosDia).forEach((sucursal) => {
                                if (sucursal.global) {
                                    // Calcular totales correctamente
                                    const pagosCobrados = Math.abs(sucursal.global.PAGOS_COBRADOS)
                                    const pagosPendientes = sucursal.global.PAGOS_PENDIENTES
                                    const totalDelDia = pagosCobrados + pagosPendientes

                                    totales += totalDelDia
                                    cobrados += pagosCobrados
                                    pendientes += pagosPendientes
                                }
                            })
                        }

                        // Determinar clase del badge
                        let badgeClass = "badge-secondary"
                        if (dia === diaActual) {
                            badgeClass = "badge-success"
                        } else {
                            const indiceDiaActual = diasSemana.indexOf(diaActual)
                            if (indiceDiaActual !== -1) {
                                if (index < indiceDiaActual) {
                                    badgeClass = "badge-danger"
                                } else if (index > indiceDiaActual) {
                                    badgeClass = "badge-warning"
                                }
                            }
                        }

                        const collapseId = "collapse" + dia
                        const headingId = "heading" + dia

                        accordionHTML +=
                            "<div class='card'>" +
                            "<div class='card-header' id='" +
                            headingId +
                            "'>" +
                            "<h2 class='mb-0'>" +
                            "<button class='btn btn-link btn-block text-left collapsed d-flex justify-content-between align-items-center' " +
                            "type='button' data-toggle='collapse' data-target='#" +
                            collapseId +
                            "' " +
                            "aria-expanded='false' aria-controls='" +
                            collapseId +
                            "' " +
                            "onclick=\"toggleDia('" +
                            dia +
                            "', " +
                            JSON.stringify(datosDia).replace(/"/g, "&quot;") +
                            ')">' +
                            "<div>" +
                            "<span class='badge " +
                            badgeClass +
                            " mr-2'>" +
                            dia +
                            "</span>" +
                            "<span>Totales: " +
                            totales +
                            " | Cobrados: " +
                            cobrados +
                            " | Pendientes: " +
                            pendientes +
                            "</span>" +
                            "</div>" +
                            "<i class='fa fa-chevron-down'></i>" +
                            "</button>" +
                            "</h2>" +
                            "</div>" +
                            "<div id='" +
                            collapseId +
                            "' class='collapse' aria-labelledby='" +
                            headingId +
                            "' data-parent='#accordionDias'>" +
                            "<div class='card-body' id='content" +
                            dia +
                            "'>" +
                            "</div>" +
                            "</div>" +
                            "</div>"
                    })

                    $("#accordionDias").html(accordionHTML)
                }

                // Toggle día y cargar contenido
                const toggleDia = (dia, datos) => {
                    const contentDiv = document.getElementById("content" + dia)

                    if (contentDiv.innerHTML.trim() === "") {
                        cargarContenidoDia(dia, datos)
                    }
                }

                // Variable para almacenar instancias de charts
                const chartInstances = {}

                // Cargar contenido del día
                const cargarContenidoDia = (dia, datos) => {
                    if (!datos || Object.keys(datos).length === 0) {
                        document.getElementById("content" + dia).innerHTML =
                            "<p class='text-center'>No hay datos para este día</p>"
                        return
                    }

                    // Destruir chart anterior si existe
                    const chartId = "chart" + dia
                    if (chartInstances[chartId]) {
                        chartInstances[chartId].destroy()
                        delete chartInstances[chartId]
                    }

                    let totalCobrados = 0,
                        totalPendientes = 0
                    let ejecutivosHTML = ""

                    Object.values(datos).forEach((sucursal) => {
                        if (sucursal.global) {
                            totalCobrados += Math.abs(sucursal.global.PAGOS_COBRADOS)
                            totalPendientes += sucursal.global.PAGOS_PENDIENTES
                        }

                        if (sucursal.detalle) {
                            sucursal.detalle.forEach((ejecutivo) => {
                                const pagosCobrados = Math.abs(ejecutivo.PAGOS_COBRADOS)
                                const efectivo = ejecutivo.POR_RECOLECTAR_EFECTIVO || 0

                                ejecutivosHTML +=
                                    "<div class='col-md-4 mb-3'>" +
                                    "<div class='card'>" +
                                    "<div class='card-body'>" +
                                    "<h6 class='card-title'>" +
                                    ejecutivo.NOMBRE_ASESOR +
                                    "</h6>" +
                                    "<p class='card-text small'>" +
                                    "Del día: " +
                                    ejecutivo.PAGOS_DEL_DIA +
                                    "<br>" +
                                    "Cobrados: " +
                                    pagosCobrados +
                                    "<br>" +
                                    "Pendientes: " +
                                    ejecutivo.PAGOS_PENDIENTES +
                                    "<br>" +
                                    "Efectivo: $" +
                                    efectivo.toLocaleString() +
                                    "</p>" +
                                    "<button class='btn btn-sm btn-primary' onclick=\"mostrarDetalleEjecutivo('" +
                                    ejecutivo.NOMBRE_ASESOR +
                                    "', '" +
                                    dia +
                                    "', '" +
                                    ejecutivo.SUCURSAL +
                                    "')\">" +
                                    "Ver Detalle" +
                                    "</button>" +
                                    "</div>" +
                                    "</div>" +
                                    "</div>"
                            })
                        }
                    })

                    const totalGeneral = totalCobrados + totalPendientes

                    let chartHTML = ""
                    if (totalGeneral > 0) {
                        const porcentajeCobrados = ((totalCobrados / totalGeneral) * 100).toFixed(1)
                        const porcentajePendientes = ((totalPendientes / totalGeneral) * 100).toFixed(1)

                        chartHTML =
                            "<div class='text-center mb-3' style='flex: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%;'>" +
                            "<h5>Resumen del día</h5>" +
                            "<div style='height: 85%; display: flex; justify-content: center; align-items: center;'>" +
                            "<canvas id='" +
                            chartId +
                            "'></canvas>" +
                            "</div>" +
                            "<div class='mt-2'>" +
                            "<small>" +
                            "<span class='text-success'>■ Cobrados: " +
                            totalCobrados +
                            " (" +
                            porcentajeCobrados +
                            "%)</span><br>" +
                            "<span class='text-danger'>■ Pendientes: " +
                            totalPendientes +
                            " (" +
                            porcentajePendientes +
                            "%)</span>" +
                            "</small>" +
                            "</div>" +
                            "</div>"
                    } else {
                        chartHTML = "<div class='text-center'><p>No hay datos para mostrar</p></div>"
                    }

                    const contenidoHTML =
                        "<div class='row' style='display: flex;'>" +
                        "<div class='col-md-6' style='flex: 1; display: flex; justify-content: center; align-items: center;'>" +
                        chartHTML +
                        "</div>" +
                        "<div class='col-md-6'>" +
                        "<h5>Ejecutivos</h5>" +
                        "<div class='row'>" +
                        ejecutivosHTML +
                        "</div>" +
                        "</div>" +
                        "</div>"

                    document.getElementById("content" + dia).innerHTML = contenidoHTML

                    // Configurar gráfico si hay datos
                    if (totalGeneral > 0) {
                        setTimeout(() => {
                            const ctx = document.getElementById(chartId)
                            if (ctx && ctx.getContext) {
                                chartInstances[chartId] = new Chart(ctx.getContext("2d"), {
                                    type: "pie",
                                    data: {
                                        labels: ["Cobrados", "Pendientes"],
                                        datasets: [
                                            {
                                                data: [totalCobrados, totalPendientes],
                                                backgroundColor: ["#28a745", "#dc3545"],
                                                borderWidth: 2,
                                                borderColor: "#fff"
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: true,
                                        aspectRatio: 1,
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
                                        },
                                        animation: {
                                            duration: 500
                                        }
                                    }
                                })
                            }
                        }, 100)
                    }
                }

                // Mostrar detalle del ejecutivo
                const mostrarDetalleEjecutivo = (nombreEjecutivo, dia, sucursal) => {
                    // Obtener fecha actual o calcular fecha del día seleccionado
                    const fechaActual = new Date()

                    // Mapear días a números (0=Domingo, 1=Lunes, etc.)
                    const mapaDias = {
                        LUNES: 1,
                        MARTES: 2,
                        MIERCOLES: 3,
                        JUEVES: 4,
                        VIERNES: 5
                    }

                    // Calcular la fecha del día seleccionado
                    const diaNumerico = mapaDias[dia]
                    const diaActual = fechaActual.getDay()
                    let fechaDelDia = new Date(fechaActual)

                    if (diaNumerico) {
                        const diferencia = diaNumerico - diaActual
                        fechaDelDia.setDate(fechaActual.getDate() + diferencia)
                    }

                    const fechaFormateada = fechaDelDia.toLocaleDateString("es-MX", {
                        weekday: "long",
                        year: "numeric",
                        month: "long",
                        day: "numeric"
                    })

                    $("#modalDetalleTitle").text(nombreEjecutivo)
                    $("#modalDetalleSubtitle").html(
                        "<div class='row'>" +
                            "<div class='col-md-4'><strong>Día:</strong> " +
                            dia +
                            "</div>" +
                            "<div class='col-md-4'><strong>Fecha:</strong> " +
                            fechaFormateada +
                            "</div>" +
                            "<div class='col-md-4'><strong>Sucursal:</strong> " +
                            sucursal +
                            "</div>" +
                            "</div>"
                    )
                    $("#modalDetalle").modal("show")
                }

                // Cerrar sesión
                const cerrarSesion = () => {
                    // Limpiar todos los charts antes de cerrar sesión
                    Object.keys(chartInstances).forEach(chartId => {
                        if (chartInstances[chartId]) {
                            chartInstances[chartId].destroy()
                            delete chartInstances[chartId]
                        }
                    })
                    
                    localStorage.removeItem("radar_auth")
                    $("#btnCerrarSesion").hide()
                    $("#accordionDias").html("")
                    mostrarModalLogin()
                }

                // Inicialización
                $(document).ready(() => {
                    $("#loginBtn").click(realizarLogin)
                    $("#usuario, #password").keypress((e) => {
                        if (e.which === 13) realizarLogin()
                    })

                    // Validar token al cargar
                    const token = validarToken()
                    if (token) {
                        $("#btnCerrarSesion").show()
                        cargarDashboard()
                    }
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Radar de Cobranza - Dashboard Día")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('rc_dashboard_dia');
    }

    public function Login()
    {
        echo json_encode(RadarCobranzaDao::Login($_POST));
    }

    public function GetResumenCobranza()
    {
        echo json_encode(RadarCobranzaDao::GetResumenCobranza($_POST['token']));
    }
}
