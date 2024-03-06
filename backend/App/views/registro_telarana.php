<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Gestión de Clientes en Telaraña</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_horario">
                    <i class="fa fa-plus"></i> Vincular Invitado
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                            <tr>
                                <th>Codigo Credito</th>
                                <th>Ciclo Invitación</th>
                                <th>Codigo Cliente</th>
                                <th>Nombre Cliente</th>
                                <th>Codigo Invitado</th>
                                <th>Nombre Invitado</th>
                                <th>Fecha Invitación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Vinculación de Invitados</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="vincularInvitado(); return false" id="Add_AHC">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="col-md-3">
                                        <span id="availability1">Cliente:</span>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" id="Cliente" name="Cliente" value="">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-primary" onclick="buscaCliente('Cliente')">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        <span id="availability1">Cliente:</span>
                                        <input type="text" class="form-control" id="MuestraCliente" name="MuestraCliente" value="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-3">
                                        <span id="availability1">Invitado:</span>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" id="Invitado" name="Invitado" value="">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-primary" onclick="buscaCliente('Invitado')">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        <span id="availability1">Invitado::</span>
                                        <input type="text" class="form-control" id="MuestraInvitado" name="MuestraInvitado" value="" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <span id="availability1">Fecha:</span>
                                    <input type="date" class="form-control" id="Fecha" name="Fecha" value=<?= $fecha ?>>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>

        </div>
    </div>
</div>

<?php echo $footer; ?>