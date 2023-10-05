<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">


            <div class="card col-md-12">

                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Ejecutivo</span>

                            <div class="count" style="font-size: 14px"><?php echo $Administracion[0]['CLIENTE']; ?></div>
                            <span class="count_top badge" style="padding: 1px 1px; background: <?php echo $Administracion[0]['COLOR']; ?>"><h5><b><i class="">SITUACIÓN: <?php echo $Administracion[0]['SITUACION_NOMBRE']; ?></i></b></h5></span>
                        </div>
                        <div class="col-md-2 col-sm-2  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i>#</i> de Pagos Validados</span>
                            <div class="count" style="font-size: 35px; color: #030303">13 de 15</div>
                        </div>
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Monto Validado</span>
                            <div class="count" style="font-size: 35px; color: #368a05">$40,000.00</div>
                        </div>
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Ejecutivo</span>
                            <div class="count" style="font-size: 35px; color: #368a05">$40,000.00</div>
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
                <center><h4 class="modal-title" id="myModalLabel">Asignar Horario de Cierre a Sucursal</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_horario(); return false" id="Add_AHC">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fecha_registro">Fecha de Registro</label>
                                        <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" readonly placeholder=""  value="<?php $fechaActual = date('Y-m-d H:i:s'); echo $fechaActual; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Fecha de registro para la asignación.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="sucursal">Sucursal *</label>
                                        <select class="form-control" autofocus type="select" id="sucursal" name="sucursal">
                                            <?php echo $opciones_suc; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hora">Horario de Cierre *</label>
                                        <select class="form-control" autofocus type="select" id="hora" name="hora">
                                            <option value="10:00:00">10:00 a.m</option>
                                            <option value="10:10:00">10:10 a.m</option>
                                            <option value="10:15:00">10:15 a.m</option>
                                            <option value="10:30:00">10:30 a.m</option>
                                            <option value="11:00:00">11:00 a.m</option>
                                            <option value="11:10:00">11:10 a.m</option>
                                            <option value="11:15:00">11:15 a.m</option>
                                            <option value="11:30:00">11:30 a.m</option>
                                            <option value="11:40:00">11:40 a.m</option>
                                            <option value="11:50:00">11:50 a.m</option>
                                            <option value="11:59:00">11:59 p.m</option>
                                        </select>
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

<script>
    function EditarHorario(sucursal, nombre_suc, hora_actual) {


        var o = new Option(nombre_suc, sucursal);
        $(o).html(nombre_suc);
        $("#sucursal_e").append(o);

        document.getElementById("hora_ae").value = hora_actual;

        $('#modal_update_horario').modal('show');

    }
</script>

<?php echo $footer; ?>
