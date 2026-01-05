<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Retiros de ahorro</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">

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

<div class="modal fade" id="modalRechazarSolicitud" tabindex="-1" role="dialog" aria-labelledby="modalRechazarSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title">Rechazar Solicitud de Retiro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <input type="hidden" id="idRetiroRechazar" />
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="motivoRechazo">Comentario de rechazo:</label>
                                <textarea class="form-control" id="motivoRechazo" rows="4" placeholder="Ingrese el motivo del rechazo"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove">&nbsp;</span>Volver
                </button>
                <button type="button" class="btn btn-danger" id="btnRechazarSolicitud">
                    <span class="glyphicon glyphicon-floppy-disk">&nbsp;</span>Rechazar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>