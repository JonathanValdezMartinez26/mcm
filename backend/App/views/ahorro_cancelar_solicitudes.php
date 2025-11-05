<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Cancelar Solicitudes de Retiro</label>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado">
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="retiros">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Crédito</th>
                            <th>Cantidad Solicitada</th>
                            <th>Fecha de solicitud</th>
                            <th>Fecha de entrega solicitada</th>
                            <th>Administradora</th>
                            <th>Estatus tesorería</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCancelarSolicitud" tabindex="-1" role="dialog" aria-labelledby="modalCancelarSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title">Cancelar Solicitud de Retiro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <input type="hidden" id="idRetiroCancelar" />
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="motivoCancelacion">Comentario de cancelación:</label>
                                <textarea class="form-control" id="motivoCancelacion" rows="4" placeholder="Ingrese el motivo de la cancelación"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove">&nbsp;</span>Volver
                </button>
                <button type="button" class="btn btn-danger" id="btnCancelarSolicitud">
                    <span class="glyphicon glyphicon-floppy-disk">&nbsp;</span>Cancelar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>