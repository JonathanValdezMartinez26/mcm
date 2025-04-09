<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Control de Garantías</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-header" style="margin: 20px 0;">
                    <span class="card-title">Ingrese la fecha de calculo para el cierre</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha">Fecha:</label>
                                <input type="date" id="fecha" class="form-control" style="font-size: 24px;" min="<?= date('Y-m-d', strtotime('-30 days')) ?>" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group" style="min-height: 68px; display: flex; align-items: center; justify-content: space-between;">
                                <button type="button" class="btn btn-primary" id="procesar">Generar Cierre</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado conDatos">
            <div style="width: 100%; text-align: center; margin-bottom: 20px;">
                <label style="font-size: large;">Destinatarios para notificación de resultado.</label>
            </div>
            <div class="botones">
                <button type="button" class="btn btn-primary" id="agregar">
                    <span class="glyphicon glyphicon-plus">&nbsp;</span>Agrega Correo
                </button>
            </div>
            <hr>
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="correos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Area</th>
                            <th>Correo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalAgregaCorreo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <center>
                    <h2 class="modal-title" id="modalCDCLabel">Añadir destinatario</h2>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="agregaCorreo">Agregar</button>
            </div>
        </div>
    </div>
</div>


<?= $footer; ?>