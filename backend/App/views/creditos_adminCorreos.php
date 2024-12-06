<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="overflow: auto;">
        <div class="contenedor-card">
            <div class="card-header">
                <div class="x_title">
                    <h3>Administración de correos</h3>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <button class="btn btn-primary" type="button" id="addCorreo"><i class="glyphicon glyphicon-plus"></i> Añadir dirección de correo</button>
                    </div>
                </div>
                <div class="row" style="display: flex;">
                    <div class=" col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Correos</strong>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="areaFiltro">Área</label>
                                            <select class="form-control" id="areaFiltro">
                                                <?= $opcArea ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sucursalFiltro">Sucursal</label>
                                            <select class="form-control" id="sucursalFiltro">
                                                <?= $opcSucursal ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table id="tblCorreos" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Nombre</th>
                                                    <th>Correo</th>
                                                    <th>Área</th>
                                                    <th>Sucursal</th>
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

                    <div class="col-md-1" style="display: flex; justify-content: center; align-items: center;">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <button class="btn btn-primary btn-block" id="btnAgregar">Agregar &gt;&gt;</button>
                                <button class="btn btn-danger btn-block" id="btnQuitar">&lt;&lt; Quitar</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Destinatarios</strong>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="grupoFiltro">Grupo</label>
                                            <select class="form-control" id="grupoFiltro">
                                                <?= $opcGrupo ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table id="tblGrupo" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Correo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Los datos se rellenarán dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registroModal" tabindex="-1" role="dialog" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center ;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="registroModalLabel">Registrar Nueva Dirección</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" placeholder="Ingresa el nombre">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="email" class="form-control" id="correo" placeholder="a.b@masconmenos.com.mx">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="empresa">Área</label>
                            <select class="form-control" id="area">
                                <option value="">Selecciona una opción</option>
                                <option value="Operaciones">Operaciones</option>
                                <option value="Call Center">Call Center</option>
                                <option value="Administración">Administración</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="empresa">Sucursal</label>
                            <select class="form-control" id="sucursal">
                                <?= $opcSucursales ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="text-align:center ;">
                    <p>Si el área o sucursal que desea no se encuentra en la lista correspondiente, comuníquese con soporte.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="guardarDireccion" class="btn btn-primary" disabled>Guardar</button>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>