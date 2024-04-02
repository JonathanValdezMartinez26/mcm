<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Registrar Quejas REDECO</h3>
                <div class="clearfix"></div>
            </div>
            <div class="card col-md-12">
                <div class="card-header">
                    <h5 class="card-title">Ingrese los datos solicitados</h5>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasNoTrim">Mes a informar *</label>
                            <select class="form-control" id="QuejasNoTrim" onchange=validaRequeridos(event)>
                                <?= $meses; ?>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un mes</small> -->
                        </div>
                    </div>
                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasNum">Número de quejas *</label>
                            <input class="form-control" id="QuejasNum" name="QuejasNum" readonly value="1" oninput=validaRequeridos()/>
                            <small class="form-text text-muted">Número de quejas a reportar</small>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasFolio">Número de folio *</label>
                            <input class="form-control" id="QuejasFolio" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Ingrese el número de folio</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasFecRecepcion">Fecha de la queja *</label>
                            <input class="form-control" id="QuejasFecRecepcion" type="date" value="<?= $fecha ?>" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Fecha en la que se recibió la queja</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="MedioId">Medio de recepción *</label>
                            <select class="form-control" id="MedioId" onchange=validaRequeridos(event)></select>
                            <!-- <small class="form-text text-muted">Seleccione un medio</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="NivelATId">Nivel de atención o contacto *</label>
                            <select class="form-control" id="NivelATId" onchange=validaRequeridos(event)></select>
                            <!-- <small class="form-text text-muted">Seleccione un nivel</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="product">Producto o servicio *</label>
                            <select class="form-control" id="product" onchange=validaRequeridos(event)>
                                <?= $productos; ?>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un producto</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="CausasId">Causa de la queja *</label>
                            <select class="form-control" id="CausasId" onchange=validaRequeridos(event)>
                                <?= $causas; ?>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione una causa</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasPORI">PORI *</label>
                            <select class="form-control" id="QuejasPORI" onchange=validaRequeridos(event)>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione una opción</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasEstatus">Estatus *</label>
                            <select class="form-control" id="QuejasEstatus" onchange=validaRequeridos(event)>
                                <option value="1">PENDIENTE</option>
                                <option value="2">CONCLUIDO</option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un estatus</small> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="QuejasCP">CP *</label>
                            <input class="form-control" id="QuejasCP" maxlength="5" onkeypress="validaEntradaCP(event)" />
                            <!-- <small class="form-text text-muted">Ingrese un CP a buscar</small> -->
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="tbnCP">Buscar</label>
                            <button class="btn btn-primary" id="btnCP" onclick=validaCP()>
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="EstadosId">Estado *</label>
                            <select class="form-control" id="EstadosId" onchange=validaRequeridos(event) disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione un estado</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasMunId">Municipio *</label>
                            <select class="form-control" id="QuejasMunId" onchange=validaRequeridos(event) disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione un municipio</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasLocId">Tipo de localidad *</label>
                            <select class="form-control" id="QuejasLocId" onchange=validaRequeridos(event) disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione un tipo de localidad</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasColId">Colonia *</label>
                            <select class="form-control" id="QuejasColId" onchange=validaRequeridos(event) disabled></select>
                            <!-- <small class="form-text text-muted">Seleccione una colonia</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasTipoPersona">Tipo de persona *</label>
                            <select class="form-control" id="QuejasTipoPersona" onchange=validaRequeridos(event)>
                                <option value="1">FÍSICA</option>
                                <option value="2">MORAL</option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un tipo</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasSexo">Sexo *</label>
                            <select class="form-control" id="QuejasSexo" onchange=validaRequeridos(event)>
                                <option value="H">HOMBRE</option>
                                <option value="M">MUJER</option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un sexo</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasEdad">Edad *</label>
                            <input class="form-control" id="QuejasEdad" type="number" min="18" max="99" onkeypress=validaLargo(event) oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Ingrese la edad</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasFecResolucion">Fecha de resolución *</label>
                            <input type="date" class="form-control" id="QuejasFecResolucion" value="<?= $fecha ?>" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Seleccione una fecha</small> -->
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasFecNotificacion">Fecha notificación al usuario *</label>
                            <input type="date" class="form-control" id="QuejasFecNotificacion" value="<?= $fecha ?>" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Seleccione una fecha</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasRespuesta">Resolución *</label>
                            <select class="form-control" id="QuejasRespuesta" onchange=validaRequeridos()>
                                <option value="1">
                                    1 - Totalmente favorable al usuario
                                </option>
                                <option value="2">2 - Desfavorable al Usuario</option>
                                <option value="3">
                                    3 - Parcialmente favorable al usuario y puede ser
                                    nulo si el Estado de la Queja es igual a (1)
                                    Pendiente
                                </option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un tipo</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasNumPenal">Número de penalización *</label>
                            <input type="number" class="form-control" id="QuejasNumPenal" oninput=validaRequeridos() />
                            <!-- <small class="form-text text-muted">Escriba un número de penalización</small> -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="PenalizacionId">Tipo de penalización *</label>
                            <select class="form-control" id="PenalizacionId" oninput=validaRequeridos()>
                                <option value="1">
                                    CONTRACTUALES - CANCELACIÓN DEL CONTRATO
                                </option>
                                <option value="2">
                                    CONTRACTUALES - REASIGNACIÓN DE CARTERA
                                </option>
                                <option value="3">ECONÓMICAS - MULTA</option>
                            </select>
                            <!-- <small class="form-text text-muted">Seleccione un tipo</small> -->
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