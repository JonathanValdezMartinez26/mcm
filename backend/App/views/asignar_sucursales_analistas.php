<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Asignación de Analistas a Sucursales</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_pago" onclick="BotonPago('<?php echo $Administracion['SITUACION_NOMBRE']; ?>');">
                    <i class="fa fa-plus"></i> Asignar Sucursal
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                                    <th>Usuario</th>
                                    <th>Nombre</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Fecha de Registro</th>
                                    <th>ADMIN</th>
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Fecha de Registro</label>
                                        <input type="text" class="form-control" id="Fecha" aria-describedby="Fecha" disabled placeholder="" value="<?php $fechaActual = date('d-m-Y H:i:s'); echo $fechaActual; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Aparecera la fecha en la que registras el pago.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Fecha de Inicio</label>
                                        <input type="date" class="form-control" id="Fecha" aria-describedby="Fecha" placeholder="" value="<?php $fechaActual = date('Y-m-d'); echo $fechaActual; ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Fecha de Fin</label>
                                        <input type="date" class="form-control" id="Fecha" aria-describedby="Fecha" placeholder="" value="<?php $fechaActual = date('Y-m-d'); echo $fechaActual; ?>">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo $Analistas; ?>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo $Analistas; ?>
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
