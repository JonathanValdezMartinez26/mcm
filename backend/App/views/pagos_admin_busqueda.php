<?php echo $header; ?>
<style type="text/css">
    panel {  }
</style>
<div class="right_col">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Administración de Pagos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Pagos/" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control mr-sm-3" style="font-size: 21px;" autofocus type="select" id="" name="" placeholder="000000" aria-label="Search">
                                    <option value="credito">Crédito</option>
                                    <option value="fecha">Fecha</option>
                                </select>
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-default" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_pago">
                     <i class="fa fa-plus"></i> Agregar Pago
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>

                            <div class="count" style="font-size: 14px"><?php echo $Administracion['CLIENTE']; ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-clock-o"></i> Ciclo</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['CICLO']; ?> </div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Prestamo</span>
                            <div class="count" style="font-size: 14px"> $ <?php echo number_format($Administracion['MONTO']); ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Día de Pago</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['DIA_PAGO']; ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Parcialidad</span>
                            <div class="count" style="font-size: 14px">$ <?php echo number_format($Administracion['PARCIALIDAD']); ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Sucursal</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['SUCURSAL']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Ejecutivo</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['EJECUTIVO']; ?> </div>
                        </div>
                    </div>
                </div>
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                                    <th>Medio</th>
                                    <th>Consecutivo</th>
                                    <th>CDGNS</th>
                                    <th>Fecha</th>
                                    <th>Ciclo</th>
                                    <th>Monto</th>
                                    <th>Tipo</th>
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

<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Agregar Registro de Pago</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Fecha</label>
                                    <input type="text" class="form-control" id="Fecha" aria-describedby="Fecha" disabled placeholder="" value="<?php $fechaActual = date('d-m-Y H:i:s'); echo $fechaActual; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Aparecera la fecha en la que registras el pago.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input type="number" class="form-control" id="monto" name="monto" placeholder="$1260.10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Pago *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <option value="credito">Pago</option>
                                        <option value="fecha">Garantía</option>
                                        <option value="fecha">Multa</option>
                                        <option value="fecha">Descuento</option>
                                        <option value="fecha">Refinanciamiento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Ejecutivo *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <?php echo $status; ?>
                                    </select>
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

<div class="modal fade" id="modal_editar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Registro de Pago</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Fecha</label>
                                    <input type="text" class="form-control" id="Fecha" aria-describedby="Fecha" disabled placeholder="" value="<?php $fechaActual = date('d-m-Y H:i:s'); echo $fechaActual; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Aparecera la fecha en la que registras el pago.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input type="number" class="form-control" id="monto" name="monto" placeholder="$1260.10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Pago *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <option value="credito">Pago</option>
                                        <option value="fecha">Garantía</option>
                                        <option value="fecha">Multa</option>
                                        <option value="fecha">Descuento</option>
                                        <option value="fecha">Refinanciamiento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Ejecutivo *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <?php echo $status; ?>
                                    </select>
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
    function EditarPago(id_suc)
    {
        credito = getParameterByName('Credito');
        id_sucursal = id_suc;

        $('#modal_editar_pago').modal('show'); // abri
    }
</script>



<?php echo $footer; ?>
