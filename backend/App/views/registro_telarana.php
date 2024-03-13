<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Gestión de Clientes en Telaraña</h3>
                <div class="clearfix"></div>
            </div>
            <div class="card col-md-12">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_vincular">
                    <i class="fa fa-plus"></i> Vincular Invitado
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                            <tr>
                                <th>Código Crédito</th>
                                <th>Ciclo Invitación</th>
                                <th>Código Cliente</th>
                                <th>Nombre Cliente</th>
                                <th>Código Invitado</th>
                                <th>Nombre Invitado</th>
                                <th>Fecha Invitación</th>
                                <th>Acciones</th>
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
<!-- <div class="modal fade in" id="modal_vincular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 15px;"> -->
<div class="modal fade" id="modal_vincular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Vincular invitados - Recomienda más paga menos</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="Add_AHC" onsubmit="vincularInvitado(event)">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <span id="availability1">Buscar por:</span>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" name="tipoAnfitrion" id="anfiXcred" checked>
                                    <label for="anfiXcred">Crédito</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" name="tipoAnfitrion" id="anfiXcgdns">
                                    <label for="anfiXcgdns">Cliente</label>
                                </div>

                                <div class="col-md-12">
                                    <span id="availability1">Código:</span>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="Cliente" name="Cliente" value="" placeholder="000000" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" onclick="buscaAnfitrion()">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                                <div class="col-md-12">
                                    <span id="availability1">Nombre de cliente:</span>
                                    <input type="text" class="form-control" id="MuestraCliente" name="MuestraCliente" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <span id="availability1">Buscar por:</span>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" name="tipoInvitado" id="invXcred" checked>
                                    <label for="invXcred">Crédito</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" name="tipoInvitado" id="invXcdgns">
                                    <label for="invXcdgns">Cliente</label>
                                </div>
                                <div class="col-md-12">
                                    <span id="availability1">Código:</span>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="Invitado" name="Invitado" value="" placeholder="000000" disabled required>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" onclick="buscaInvitado()" id="BuscarInvitado" disabled>
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                                <div class="col-md-12">
                                    <span id="availability1">Nombre de invitado:</span>
                                    <input type="text" class="form-control" id="MuestraInvitado" name="MuestraInvitado" value="" readonly>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <span id="availability1">Fecha de Registro:</span>
                                    <input type="date" class="form-control" id="Fecha" name="Fecha" value=<?= $fecha ?> min=<?= $fechaMin ?> max=<?= $fechaMax ?>>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                            <button type="submit" id="btnVincular" name=" agregar" class="btn btn-primary" value="enviar" disabled><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>