<?= $header; ?>


<div class="right_col">
    <div class="panel panel-body" style="overflow: auto;">
        <div class="contenedor-card">
            <div class="card-header">
                <div class="x_title">
                    <h3>Incidencias atendidas por usuario</h3>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" style="display: flex; align-items: center; justify-content: space-around; height: 500px;">
                        <div style="width: 1000px;">
                            <canvas id="chrtIncidencias"></canvas>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px; text-align: center;">
                        <h2>Detalle de incidencias</h2>
                        <table id="tblIncidencias" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Año</th>
                                    <th>Mes</th>
                                    <th>Usuario</th>
                                    <th>Nombre</th>
                                    <th>Incidencias</th>
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
    </div>
</div>

<div class="modal fade" id="detalleUsuario" tabindex="-1" role="dialog" aria-labelledby="registroModalLabel" aria-hidden="true" style="width: 100vw; height: 100vh;">
    <div class="modal-dialog" role="document" style=" height: 100%; width: 100%; display: flex; justify-content: center; align-items: center; margin: 0;">
        <div class="modal-content" style="width: 80%; height: 80%;">
            <div class="modal-header" style="text-align:center ;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="ttlNombre"></h4>
            </div>
            <div class="modal-body" style="height: 90%; overflow: auto;">
                <div class="row">
                    <div class="col-md-12" style="display: flex; align-items: center; justify-content: space-around;">
                        <div style="width: 40%;">
                            <canvas id="chrtUConteo"></canvas>
                        </div>
                        <div style="width: 40%;">
                            <canvas id="chrtMonto"></canvas>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblUsuario" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Crédito</th>
                                    <th>Ciclo</th>
                                    <th>Monto</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>