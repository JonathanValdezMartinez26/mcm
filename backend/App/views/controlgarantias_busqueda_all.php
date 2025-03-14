<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Control de Garantías</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Ingrese el número de crédito</h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Creditos/ControlGarantias/" method="get">
                        <div class="row">
                                <div class="col-md-4">
                                    <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>" maxlength="6">
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
                <form name="all" id="all" method="POST">
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_articulo">
                        <i class="fa fa-plus"></i> Agregar Artículo
                    </button>
                    <button id="export_excel" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>

                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                            <th>Fecha de Registro</th>
                            <th>Secuencia</th>
                            <th>Articulo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Serie</th>
                            <th>Monto</th>
                            <th>Factura</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_agregar_articulo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Agregar Registro de Artículo a Garantias</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Credito *</label>
                                    <input type="text" class="form-control" id="credito" name="credito" aria-describedby="credito" value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Nombre del Artículo *</label>
                                    <input type="text" class="form-control" id="articulo" name="articulo" aria-describedby="articulo" placeholder="Escribe el nombre del artículo" value="" maxlength="80" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="marca">Marca *</label>
                                    <input type="text" class="form-control" id="marca" name="marca" aria-describedby="marca" placeholder="Escribe la marca del artículo" value=""  maxlength="80" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modelo">Modelo *</label>
                                    <input type="text" class="form-control" id="modelo" name="modelo" aria-describedby="modelo" placeholder="Escribe el modelo del artículo" value="" maxlength="50" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="serie">Número de Serie *</label>
                                    <input type="text" class="form-control" id="serie" name="serie" aria-describedby="serie" placeholder="Escribe el número de serie" value="" maxlength="20" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valor">Valor *</label>
                                    <input type="number" class="form-control" id="valor" name="valor" aria-describedby="valor" placeholder="Escribe el valor del artículo" value="" maxlength="6">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="factura">Factura *</label>
                                    <input type="text" class="form-control" id="factura" name="factura" aria-describedby="factura" placeholder="Escribe el número de factura" value=""  maxlength="50" onkeyup="mayus(this);">
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
<div class="modal fade" id="modal_editar_articulo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Registro de un Artículo en Garantía</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_update(); return false" id="AddUpdate">
                        <div class="row">

                            <div class="col-md-6" >
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Secuencia *</label>
                                    <input type="text" class="form-control" id="secuencia_e" name="secuencia_e" aria-describedby="secuencia_e" value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6" >
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Credito *</label>
                                    <input type="text" class="form-control" id="credito_e" name="credito_e" aria-describedby="credito_e"  value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Nombre *</label>
                                    <input type="text" class="form-control" id="articulo_e" name="articulo_e" aria-describedby="articulo_e" placeholder="Escribe el nombre del artículo" value="" maxlength="80" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="marca">Marca *</label>
                                    <input type="text" class="form-control" id="marca_e" name="marca_e" aria-describedby="marca_e" placeholder="Escribe la marca del artículo" value="" maxlength="20" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modelo">Modelo *</label>
                                    <input type="text" class="form-control" id="modelo_e" name="modelo_e" aria-describedby="modelo_e" placeholder="Escribe el modelo del artículo" value=""  maxlength="50" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="serie">Número de Serie *</label>
                                    <input type="text" class="form-control" id="serie_e" name="serie_e" aria-describedby="serie_" placeholder="Escribe el número de serie" value="" onkeypress="return check_t(event)" maxlength="20" onkeyup="mayus(this);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valor">Valor *</label>
                                    <input type="number" class="form-control" id="valor_e" name="valor_e" aria-describedby="valor_e" placeholder="Escribe el valor del artículo" value="" maxlength="6" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="factura">Factura *</label>
                                    <input type="text" class="form-control" id="factura_e" name="factura_e" aria-describedby="factura_e" placeholder="Escribe el número de factura" value="" onkeypress="return check_t(event)" maxlength="20" onkeyup="mayus(this);">
                                </div>
                            </div>

                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar_update" class="btn btn-primary" value="enviar_update"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>

        </div>
    </div>
</div>




<?php echo $footer; ?>
