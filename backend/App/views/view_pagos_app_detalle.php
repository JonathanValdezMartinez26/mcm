<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">


            <div class="card col-md-12">

                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Ejecutivo</span>

                            <div class="count" style="font-size: 14px">NOMBRE DEL EJECUTIVO</div>
                            <span class="count_top badge" style="padding: 1px 1px; background: <?php echo $Administracion[0]['COLOR']; ?>"><h5><b><i class="">SITUACIÓN: <?php echo $Administracion[0]['SITUACION_NOMBRE']; ?></i></b></h5></span>
                        </div>
                        <div class="col-md-2 col-sm-2  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i>#</i> de Pagos Validados</span>
                            <div class="count" style="font-size: 30px; color: #030303"><?php echo $DetalleGlobal[0]['TOTAL_VALIDADOS']; ?> DE <?php echo $DetalleGlobal[0]['TOTAL_PAGOS']; ?></div>
                        </div>
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Monto Validado</span>
                            <div class="count" style="font-size: 35px; color: #368a05">$<?php echo number_format($DetalleGlobal[0]['TOTAL'],2); ?></div>
                        </div>
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Terminar Validación</span>
                            <div class="count" style="font-size: 35px; color: #368a05">
                                <button type="button" class="btn btn-primary" style="border: 1px solid #c4a603; background: #FFFFFF" data-toggle="modal" data-target="#modal_expediente" data-backdrop="static" data-keyboard="false">
                                    <i class="fa fa-eye" style="color: #1c4e63"></i> <label style="color: #1c4e63">Procesar Pagos Validados</label>
                                </button>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="dataTable_wrapper">
                    <hr>
                    <p><b><span class="fa fa-sticky-note"></span> Nota:Si ya valido el pago y es correcto marque la casilla (Validado)</b></p>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                            <th>ID Transacción</th>
                            <th>Cliente</th>
                            <th>Tipo Pago</th>
                            <th>Monto</th>
                            <th>Comentario Cajas</th>
                            <th>Fecha Captura</th>
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

<div class="modal fade" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Pago de Ejecutivo (App)</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_horario(); return false" id="Add_AHC">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fecha_registro">Fecha de Actualización</label>
                                        <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" readonly placeholder=""  value="<?php $fechaActual = date('Y-m-d H:i:s'); echo $fechaActual; ?>">

                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="sucursal">Tipo de Pago *</label>
                                        <select class="form-control" autofocus type="select" id="sucursal" name="sucursal">
                                            <?php echo $opciones_suc; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fecha_registro">Monto Registrado *</label>
                                        <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" readonly placeholder=""  value="<?php $fechaActual = date('Y-m-d H:i:s'); echo $fechaActual; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Fecha de registro para la asignación.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fecha_registro">Nuevo Monto *</label>
                                        <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" readonly placeholder=""  value="<?php $fechaActual = date('Y-m-d H:i:s'); echo $fechaActual; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Fecha de registro para la asignación.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fecha_registro">Comentario (motivo de cambio) *</label>
                                        <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" readonly placeholder=""  value="<?php $fechaActual = date('Y-m-d H:i:s'); echo $fechaActual; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Fecha de registro para la asignación.</small>
                                    </div>
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
