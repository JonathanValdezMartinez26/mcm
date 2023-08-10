<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de Desembolsos Cultiva (PLD)</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el rango de fechas a generar el , solo tiene permitido un rango 7 días </h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Operaciones/ReportePLDDesembolsos/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2"  autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>">
                                    <span id="availability1" >Desde</span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2"  autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $Final; ?>">
                                    <span id="availability1" >Hasta</span>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <form name="all" id="all" method="POST">
                    <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                                    <th>Localidad</th>
                                    <th>Sucursal</th>
                                    <th>Tipo de Operación</th>
                                    <th>Cliente</th>
                                    <th>N.Cuenta</th>
                                    <th>Instrumento Monetario</th>
                                    <th>Moneda</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Tipo de Receptor</th>
                                    <th>Clave de Receptor</th>
                                    <th>Caja</th>
                                    <th>Id Cajero</th>
                                   <th>Fecha y Hora</th>
                                   <th>N.Tarjeta</th>
                                   <th>Tipo de Tarjeta</th>
                                   <th>Código de Autorización</th>
                                   <th>Atraso</th>
                                   <th>oficina</th>
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

<?php echo $footer; ?>
