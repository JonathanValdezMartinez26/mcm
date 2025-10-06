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
                                <select class="form-control" id="sucursal">
                                    <?= $sucursales; ?>
                                </select>
                                <span>Sucursal</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="text" id="credito" placeholder="Buscar por crédito">
                                <span>Crédito</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="btnBuscar" style="margin-top: 0;">
                                    <span class="fa fa-search">&nbsp;</span>Buscar
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
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tablaRetiros">
                        <thead>
                            <tr>
                                <th>ID Retiro</th>
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
</div>

<!-- Modal para ver detalle del retiro -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalDetalleLabel">
                    <i class="fa fa-info-circle"></i> Detalle del Retiro
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="text-primary">
                            <i class="fa fa-file-text-o"></i> Información General
                        </h5>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>ID Retiro:</strong></label>
                            <p id="detalle_id_retiro" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Crédito:</strong></label>
                            <p id="detalle_credito" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Fecha Creación:</strong></label>
                            <p id="detalle_fecha_creacion" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Cantidad Solicitada:</strong></label>
                            <p id="detalle_cantidad_solicitada" class="form-control-static text-success">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Cantidad Autorizada:</strong></label>
                            <p id="detalle_cantidad_autorizada" class="form-control-static text-success">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Fecha Solicitud:</strong></label>
                            <p id="detalle_fecha_solicitud" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Fecha Entrega Solicitada:</strong></label>
                            <p id="detalle_fecha_entrega_solicitada" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>

                <!-- Información de Administradora -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="text-primary">
                            <i class="fa fa-user"></i> Administradora
                        </h5>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Estatus:</strong></label>
                            <p id="detalle_estatus_administradora" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>CDGPE:</strong></label>
                            <p id="detalle_cdgpe_administradora" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>CDGPE Soporte:</strong></label>
                            <p id="detalle_cdgpe_soporte" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Observaciones:</strong></label>
                            <p id="detalle_observaciones_administradora" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>

                <!-- Información de Tesorería -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="text-primary">
                            <i class="fa fa-money"></i> Tesorería
                        </h5>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Estatus:</strong></label>
                            <p id="detalle_estatus_tesoreria" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>CDGPE:</strong></label>
                            <p id="detalle_cdgpe_tesoreria" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Fecha Proceso:</strong></label>
                            <p id="detalle_fecha_procesa_tesoreria" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Observaciones:</strong></label>
                            <p id="detalle_observaciones_tesoreria" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>

                <!-- Información de Call Center -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="text-primary">
                            <i class="fa fa-phone"></i> Call Center
                        </h5>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Estatus:</strong></label>
                            <p id="detalle_estatus_call_center" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>CDGPE:</strong></label>
                            <p id="detalle_cdgpe_call_center" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Fecha Proceso:</strong></label>
                            <p id="detalle_fecha_procesa_call_center" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Observaciones:</strong></label>
                            <p id="detalle_observaciones_call_center" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
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
                <h4 class="modal-title" id="modalComprobanteLabel">
                    <i class="fa fa-file-pdf-o"></i> Comprobante de Retiro
                </h4>
            </div>
            <div class="modal-body">
                <div id="comprobanteContainer" class="text-center">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        El comprobante se mostrará aquí
                    </div>
                    <!-- Aquí se cargará el comprobante (PDF, imagen, etc.) -->
                    <div id="comprobanteContent">
                        <!-- Contenido del comprobante -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnDescargarComprobante">
                    <i class="fa fa-download"></i> Descargar
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
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

    .table-responsive {
        overflow-x: auto;
    }

    #tablaRetiros {
        width: 100%;
        margin-bottom: 1rem;
    }

    #tablaRetiros th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }

    #tablaRetiros td {
        vertical-align: middle;
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