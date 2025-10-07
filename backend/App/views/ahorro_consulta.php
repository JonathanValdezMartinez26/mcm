<?php
$fechaI = date('Y-m-d', strtotime('-3 days'));
if (date('N', strtotime($fechaI)) == 1 || date('N', strtotime($fechaI)) == 7) {
    $fechaI = date('Y-m-d', strtotime('-2 days', strtotime($fechaI)));
} elseif (date('N', strtotime($fechaI)) == 6) {
    $fechaI = date('Y-m-d', strtotime('-1 day', strtotime($fechaI)));
}

$fechaF = date('Y-m-d', strtotime('+3 days'));
if (date('N', strtotime($fechaF)) == 1 || date('N', strtotime($fechaF)) == 7) {
    $fechaF = date('Y-m-d', strtotime('+2 days', strtotime($fechaF)));
} elseif (date('N', strtotime($fechaF)) == 6) {
    $fechaF = date('Y-m-d', strtotime('+1 day', strtotime($fechaF)));
}
?>

<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Consulta de Retiros de Ahorro</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaI" value="<?= $fechaI ?>">
                                <span>Fecha inicial</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaF" value="<?= $fechaF ?>">
                                <span>Fecha final</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="btnBuscar" style="margin-top: 0;">
                                    <span class="fa fa-search">&nbsp;</span>Buscar
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" style="text-align: right;">
                                <button type="button" class="btn btn-success" id="btnNuevaSolicitud" style="margin-top: 0;">
                                    <span class="fa fa-plus">&nbsp;</span>Registrar Nueva Solicitud
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="panel-body resultado">
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="tablaRetiros">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Crédito</th>
                            <th>Cantidad Solicitada</th>
                            <th>Cantidad Autorizada</th>
                            <th>Fecha Solicitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nueva Solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" role="dialog" aria-labelledby="modalNuevaSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalNuevaSolicitudLabel">Nueva Solicitud de Retiro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="formNuevaSolicitud">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Crédito (CDGNS) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nueva_cdgns" maxlength="6" placeholder="Ingrese el crédito" required>
                                    <small class="form-text text-muted">6 dígitos</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cantidad Solicitada <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="nueva_cantidad_solicitada" placeholder="0.00" required>
                                    <small class="form-text text-muted">Monto solicitado</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Solicitud <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="nueva_fecha_solicitud" required>
                                    <small class="form-text text-muted">Fecha de solicitud</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Entrega Solicitada <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="nueva_fecha_entrega_solicitada" required>
                                    <small class="form-text text-muted">Fecha deseada de entrega</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Observaciones</label>
                                    <textarea class="form-control" id="nueva_observaciones_administradora" rows="3" placeholder="Ingrese observaciones (opcional)"></textarea>
                                    <small class="form-text text-muted">Comentarios adicionales</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Foto/Comprobante</label>
                                    <input type="file" class="form-control" id="nueva_foto" accept="image/*">
                                    <small class="form-text text-muted">Formatos aceptados: JPG, PNG, PDF (Máx. 5MB)</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarNuevaSolicitud">
                    <span class="glyphicon glyphicon-floppy-disk"></span> Guardar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle del retiro -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalDetalleLabel">Detalle del Retiro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary">
                                <span class="glyphicon glyphicon-file"></span> Información General
                            </h5>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Retiro</label>
                                <input type="text" class="form-control" id="detalle_id_retiro" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Crédito</label>
                                <input type="text" class="form-control" id="detalle_credito" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Creación</label>
                                <input type="text" class="form-control" id="detalle_fecha_creacion" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cantidad Solicitada</label>
                                <input type="text" class="form-control" id="detalle_cantidad_solicitada" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cantidad Autorizada</label>
                                <input type="text" class="form-control" id="detalle_cantidad_autorizada" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha Solicitud</label>
                                <input type="text" class="form-control" id="detalle_fecha_solicitud" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha Entrega Solicitada</label>
                                <input type="text" class="form-control" id="detalle_fecha_entrega_solicitada" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Administradora -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary">
                                <span class="glyphicon glyphicon-user"></span> Administradora
                            </h5>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estatus</label>
                                <input type="text" class="form-control" id="detalle_estatus_administradora" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CDGPE</label>
                                <input type="text" class="form-control" id="detalle_cdgpe_administradora" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CDGPE Soporte</label>
                                <input type="text" class="form-control" id="detalle_cdgpe_soporte" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="detalle_observaciones_administradora" rows="3" disabled></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Tesorería -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary">
                                <span class="glyphicon glyphicon-usd"></span> Tesorería
                            </h5>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estatus</label>
                                <input type="text" class="form-control" id="detalle_estatus_tesoreria" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CDGPE</label>
                                <input type="text" class="form-control" id="detalle_cdgpe_tesoreria" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Proceso</label>
                                <input type="text" class="form-control" id="detalle_fecha_procesa_tesoreria" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="detalle_observaciones_tesoreria" rows="3" disabled></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Call Center -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary">
                                <span class="glyphicon glyphicon-earphone"></span> Call Center
                            </h5>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estatus</label>
                                <input type="text" class="form-control" id="detalle_estatus_call_center" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CDGPE</label>
                                <input type="text" class="form-control" id="detalle_cdgpe_call_center" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Proceso</label>
                                <input type="text" class="form-control" id="detalle_fecha_procesa_call_center" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="detalle_observaciones_call_center" rows="3" disabled></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Comprobante -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary">
                                <span class="glyphicon glyphicon-paperclip"></span> Comprobante
                            </h5>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-block" id="btnVerComprobante">
                                    <span class="glyphicon glyphicon-file"></span> Ver Comprobante
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" role="dialog" aria-labelledby="modalComprobanteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalComprobanteLabel">Comprobante de Retiro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="comprobanteContainer" class="text-center">
                        <img src="/img/wait.gif" alt="Descargando..." id="loadingImg">
                        <img src="" alt="Comprobante" class="img-fluid" id="comprobanteImg" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-primary" id="btnDescargarComprobante">
                    <span class="glyphicon glyphicon-download-alt"></span> Descargar
                </button> -->
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>

<style>
    .form-control-static {
        padding-top: 7px;
        padding-bottom: 7px;
        margin-bottom: 0;
        min-height: 34px;
        font-weight: 600;
        color: #333;
    }

    .modal-body h5 {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .modal-body h5:first-child {
        margin-top: 0;
    }

    .btn-action {
        margin: 2px;
    }

    .botones {
        margin-bottom: 10px;
    }

    #comprobanteContainer {
        min-height: 300px;
        padding: 20px;
    }

    #comprobanteContent {
        margin-top: 20px;
    }

    .badge {
        font-size: 90%;
        padding: 5px 10px;
    }
</style>