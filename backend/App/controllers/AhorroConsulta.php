<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
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

                    return { fechaI, fechaF }
                }

                const resultadoOK = (datos) => {
                    datos = datos.map((item) => {
                        return [
                            item.ID_RETIRO,
                            item.CDGNS,
                            "$ " + formatoMoneda(item.CANTIDAD_SOLICITADA),
                            "$ " + formatoMoneda(item.CANTIDAD_AUTORIZADA),
                            item.FECHA_SOLICITUD,
                            '<button class="btn btn-info btn-sm" onclick="verDetalle(' + item.ID_RETIRO + ')"><i class="fa fa-eye"></i> Ver detalle</button>'
                        ]
                    })

                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
                }

                const resultadoError = (mensaje) => {
                    $(".resultado").toggleClass("conDatos", false)
                    showError(mensaje).then(() => actualizaDatosTabla(idTabla, []))
                }

                const verDetalle = (idRetiro) => {
                    consultaServidor("/AhorroConsulta/GetRetiroById", { id: idRetiro }, (res) => {
                        if (!res.success) {
                            return Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: res.mensaje
                            });
                        }
                        
                        const datos = res.datos;
                        
                        // Llenar el modal con los datos
                        $("#detalle_id_retiro").val(datos.ID_RETIRO || "");
                        $("#detalle_credito").val(datos.CDGNS || "");
                        $("#detalle_fecha_creacion").val(datos.FECHA_CREACION || "");
                        $("#detalle_cantidad_solicitada").val("$" + formatoMoneda(datos.CANTIDAD_SOLICITADA || 0));
                        $("#detalle_cantidad_autorizada").val("$" + formatoMoneda(datos.CANTIDAD_AUTORIZADA || 0));
                        $("#detalle_fecha_solicitud").val(datos.FECHA_SOLICITUD || "");
                        $("#detalle_fecha_entrega_solicitada").val(datos.FECHA_ENTREGA_SOLICITADA || "");
                        $("#detalle_estatus_administradora").val(datos.ESTATUS_ADMINISTRADORA || "");
                        $("#detalle_cdgpe_administradora").val(datos.CDGPE_ADMINISTRADORA || "");
                        $("#detalle_cdgpe_soporte").val(datos.CDGPE_SOPORTE || "");
                        $("#detalle_observaciones_administradora").val(datos.OBSERVACIONES_ADMINISTRADORA || "");
                        $("#detalle_estatus_tesoreria").val(datos.ESTATUS_TESORERIA || "");
                        $("#detalle_cdgpe_tesoreria").val(datos.CDGPE_TESORERIA || "");
                        $("#detalle_fecha_procesa_tesoreria").val(datos.FECHA_PROCESA_TESORERIA || "");
                        $("#detalle_observaciones_tesoreria").val(datos.OBSERVACIONES_TESORERIA || "");
                        $("#detalle_estatus_call_center").val(datos.ESTATUS_CALL_CENTER || "");
                        $("#detalle_cdgpe_call_center").val(datos.CDGPE_CALL_CENTER || "");
                        $("#detalle_fecha_procesa_call_center").val(datos.FECHA_PROCESA_CALL_CENTER || "");
                        $("#detalle_observaciones_call_center").val(datos.OBSERVACIONES_CALL_CENTER || "");
                        
                        // Configurar botón de ver comprobante
                        $("#btnVerComprobante").off("click").on("click", function() {
                            verComprobante(idRetiro);
                        });
                        
                        $("#modalDetalle").modal("show");
                    });
                }

                const verComprobante = (idRetiro) => {
                    $("#modalDetalle").modal("hide");
                    $("#comprobanteImg").hide();
                    $("#loadingImg").show();
                    $("#comprobanteImg").attr("src", "/AhorroConsulta/GetImgSolicitud/?id=" + idRetiro + "&tipo=comprobante");

                    $("#modalComprobante").modal("show");
                }


                const nuevaSolicitud = () => {
                    const hoy = new Date().toISOString().split("T")[0];
                    const dosDiasDespues = new Date();
                    dosDiasDespues.setDate(dosDiasDespues.getDate() + 2);
                    
                    $("#formNuevaSolicitud")[0].reset();
                    $("#nueva_fecha_solicitud").val(hoy);
                    $("#nueva_fecha_entrega_solicitada").val(dosDiasDespues.toISOString().split("T")[0]);
                    $("#modalNuevaSolicitud").modal("show");
                }

                const guardarNuevaSolicitud = () => {
                    const cdgns = $("#nueva_cdgns").val().trim();
                    const cantidadSolicitada = $("#nueva_cantidad_solicitada").val();
                    const fechaSolicitud = $("#nueva_fecha_solicitud").val();
                    const fechaEntrega = $("#nueva_fecha_entrega_solicitada").val();
                    
                    if (!cdgns || cdgns.length !== 6) return showError("El crédito debe tener 6 dígitos");
                    if (!cantidadSolicitada || parseFloat(cantidadSolicitada) <= 0) return showError("Debe ingresar una cantidad solicitada válida mayor a 0");
                    if (!fechaSolicitud) return showError("Debe seleccionar la fecha de solicitud");
                    if (!fechaEntrega) return showError("Debe seleccionar la fecha de entrega solicitada");    
                    
                    const archivo = $("#nueva_foto")[0].files[0];
                    if (!archivo) return showError("Debe seleccionar un archivo");
                    if (archivo && archivo.size > 5242880) return showError("El archivo no debe superar los 5MB");
                    
                    // Confirmar guardado
                    confirmarMovimiento("Registro de retiro de ahorro", "¿Está seguro de crear esta solicitud de retiro?")
                        .then((continuar) => {
                        if (continuar) {
                            const formData = new FormData();
                            formData.append("cdgns", cdgns);
                            formData.append("cantidad_solicitada", cantidadSolicitada);
                            formData.append("fecha_solicitud", fechaSolicitud);
                            formData.append("fecha_entrega_solicitada", fechaEntrega);
                            formData.append("observaciones_administradora", $("#nueva_observaciones_administradora").val() || "");
                            
                            if (archivo) formData.append("foto", archivo)
                            
                            // Mostrar loading
                            consultaServidor("/AhorroConsulta/InsertRetiro", formData, (res) => {
                                if (!res.success) return showError(res.mensaje)
                                showSuccess(res.mensaje)
                                $("#modalNuevaSolicitud").modal("hide");
                                consultaSolicitudes()
                            }, "POST", "JSON", false, false)
                        }
                    });
                }

                $(document).ready(function() {
                    $("#fechaI").change(consultaSolicitudes)
                    $("#fechaF").change(consultaSolicitudes)
                    
                    $("#btnBuscar").click(consultaSolicitudes)
                    $("#btnNuevaSolicitud").click(nuevaSolicitud)
                    $("#btnGuardarNuevaSolicitud").click(guardarNuevaSolicitud)

                    $("#comprobanteImg").on("load", function() {
                        $("#loadingImg").hide();
                        $(this).show();
                    });

                    configuraTabla(idTabla)
                    consultaSolicitudes()
                });
            </script>
        JAVASCRIPT;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Ahorro Consulta")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("ahorro_consulta");
    }

    public function GetRetirosAhorro()
    {
        echo json_encode(AhorroConsultaDao::GetRetirosAhorro($_POST));
    }

    public function GetRetiroById()
    {
        echo json_encode(AhorroConsultaDao::getRetiroById($_POST['id']));
    }

    public function InsertRetiro()
    {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $_POST['foto'] = fopen($_FILES['foto']['tmp_name'], 'rb');
        }

        $result = AhorroConsultaDao::insertRetiro($_POST);
        echo json_encode($result);
        if ($_POST['foto']) fclose($_POST['foto']);
        return true;
    }

    public function GetImgSolicitud()
    {
        $result = AhorroConsultaDao::getImgSolicitud($_GET);

        if (!$result['success'] || !$result['datos'] || !$result['datos']['FOTO']) {
            http_response_code(404);
            echo "Imagen no encontrada";
            return;
        }
        $archivo = $result['datos']['FOTO'];
        $archivo = is_resource($archivo) ? stream_get_contents($archivo) : $archivo;

        header('Content-Transfer-Encoding: binary');
        echo $archivo;
        if (is_resource($archivo)) {
            fclose($archivo);
        }
        return;
    }
}
