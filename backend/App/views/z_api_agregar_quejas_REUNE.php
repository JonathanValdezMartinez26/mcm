<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Registrar Quejas REUNE</h3>
                <div class="clearfix"></div>
            </div>
            <div class="card col-md-12">
                <div class="card-header">
                    <h5 class="card-title">Ingrese los datos solicitados</h5>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasTrim">Trimestre a informar *</label>
                            <select class="form-control" id="ConsultasTrim" onchange=validaRequeridos()>
                                <?= $meses; ?>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un mes</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasFolio">Número de folio *</label>
                            <input class="form-control" id="ConsultasFolio" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Ingrese el número de folio</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasEstatusCon">Estatus *</label>
                            <select class="form-control" id="ConsultasEstatusCon" onchange=validaRequeridos()>
                                <option value="1">PENDIENTE</option>
                                <option value="2">CONCLUIDO</option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un estatus</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasFecRecepcion">Fecha de la queja *</label>
                            <input class="form-control" id="ConsultasFecRecepcion" type="date" value="<?= $fecha ?>" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Fecha en la que se recibió la queja</small> -->
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultascatnivelatenId">Nivel de atención o contacto *</label>
                            <select class="form-control" id="ConsultascatnivelatenId" onchange=validaRequeridos()></select>
                            <!-- <small class="form-text text-muted">Seleccione un nivel</small> -->
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Producto">Producto o servicio *</label>
                            <select class="form-control" id="Producto" onchange=validaRequeridos()>
                                <?= $productos; ?>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un producto</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="CausaId">Causa de la queja *</label>
                            <select class="form-control" id="CausaId" onchange=validaRequeridos()>
                                <?= $causas; ?>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione una causa</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="MediosId">Medio de recepción *</label>
                            <select class="form-control" id="MediosId" onchange=validaRequeridos()></select>
                            <!-- <small class="form-text text-muted">Seleccione un medio</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasPori">PORI *</label>
                            <select class="form-control" id="ConsultasPori" onchange=validaRequeridos()>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione una opción</small> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ConsultasCP">CP *</label>
                            <input class="form-control" id="ConsultasCP" maxlength="5" onkeypress="validaEntradaCP(event)" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Ingrese un CP a buscar</small> -->
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="tbnCP">Buscar</label>
                            <button class="btn btn-primary" onclick=validaCP() id="btnCP">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="EstadosId">Estado *</label>
                            <select class="form-control" id="EstadosId" onchange=validaRequeridos() disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione un estado</small> -->
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasMpioId">Municipio *</label>
                            <select class="form-control" id="ConsultasMpioId" onchange=validaRequeridos() disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione un municipio</small> -->
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasLocId">Tipo de localidad *</label>
                            <select class="form-control" id="ConsultasLocId" onchange=validaRequeridos() disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione un tipo de localidad</small> -->
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasColId">Colonia *</label>
                            <select class="form-control" id="ConsultasColId" onchange=validaRequeridos() disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione una colonia</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasFecAten">Fecha de atención *</label>
                            <input type="date" class="form-control" id="ConsultasFecAten" oninput=validaRequeridos() value="<?= $fecha ?>" />
                            <!-- <small class="form-text text-muted">Seleccione una fecha</small> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnAgregar" class="btn btn-primary" onclick=registrarQueja(event) disabled>
                        <span class="glyphicon glyphicon-floppy-disk"></span> Registrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>