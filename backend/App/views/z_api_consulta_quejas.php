<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Consultar Quejas REUNE</h3>
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
                            <select class="form-control" autofocus type="select" id="QuejasNoTrim" name="QuejasNoTrim">
                                <?= $meses; ?>
                            </select>
                            <small class="form-text text-muted">Seleccione un mes</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasFolio">Número de folio *</label>
                            <input type="text" class="form-control" id="QuejasFolio" name="QuejasFolio" required />
                            <small class="form-text text-muted">Ingrese el número de folio</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasFecRecepcion">Fecha de la queja *</label>
                            <input type="date" class="form-control" id="QuejasFecRecepcion" name="QuejasFecRecepcion" value="<?= $fecha ?>" />
                            <small class="form-text text-muted">Fecha en la que se recibió la queja</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="NivelATId">Nivel de atención o contacto *</label>
                            <select class="form-control" autofocus type="select" id="NivelATId" name="NivelATId" required></select>
                            <small class="form-text text-muted">Seleccione un nivel</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="product">Producto o servicio *</label>
                            <select class="form-control" autofocus type="select" id="product" name="product">
                                <?= $productos; ?>
                            </select>
                            <small class="form-text text-muted">Seleccione un producto</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="CausasId">Causa de la queja *</label>
                            <select class="form-control" autofocus type="select" id="CausasId" name="CausasId">
                                <?= $causas; ?>
                            </select>
                            <small class="form-text text-muted">Seleccione una causa</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="MedioId">Medio de recepción *</label>
                            <select class="form-control" autofocus type="select" id="MedioId" name="MedioId" required></select>
                            <small class="form-text text-muted">Seleccione un medio</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasPORI">PORI *</label>
                            <select class="form-control" autofocus type="select" id="QuejasPORI" name="QuejasPORI">
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                            <small class="form-text text-muted">Seleccione una opción</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasEstatus">Estatus *</label>
                            <select class="form-control" autofocus type="select" id="QuejasEstatus" name="QuejasEstatus">
                                <option value="1">PENDIENTE</option>
                                <option value="2">CONCLUIDO</option>
                            </select>
                            <small class="form-text text-muted">Seleccione un estatus</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="QuejasCP">CP *</label>
                            <input type="text" class="form-control" id="QuejasCP" name="QuejasCP" maxlength="5" value="" onkeypress="validaEntradaCP(event)" required />
                            <small class="form-text text-muted">Ingrese un CP a buscar</small>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="tbnCP">Buscar</label>
                            <button type="button" class="btn btn-primary" onclick="validaCP();return false" id="btnCP">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="EstadosId">Estado *</label>
                            <select class="form-control" autofocus type="select" id="EstadosId" name="EstadosId" disabled required></select>
                            <small class="form-text text-muted">Seleccione un estado</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasMunId">Municipio *</label>
                            <select class="form-control" autofocus type="select" id="QuejasMunId" name="QuejasMunId" disabled required></select>
                            <small class="form-text text-muted">Seleccione un municipio</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasLocId">Tipo de localidad *</label>
                            <select class="form-control" autofocus type="select" id="QuejasLocId" name="QuejasLocId" disabled required></select>
                            <small class="form-text text-muted">Seleccione un tipo de localidad</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasColId">Colonia *</label>
                            <select class="form-control" autofocus type="select" id="QuejasColId" name="QuejasColId" disabled required></select>
                            <small class="form-text text-muted">Seleccione una colonia</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasTipoPersona">Tipo de persona *</label>
                            <select class="form-control" autofocus type="select" id="QuejasTipoPersona" name="QuejasTipoPersona">
                                <option value="1">FÍSICA</option>
                                <option value="2">MORAL</option>
                            </select>
                            <small class="form-text text-muted">Seleccione un tipo</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasFecResolucion">Fecha de resolución *</label>
                            <input type="date" class="form-control" id="QuejasFecResolucion" name="QuejasFecResolucion" value="<?= $fecha ?>" />
                            <small class="form-text text-muted">Seleccione una fecha</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasRespuesta">Resolución *</label>
                            <select class="form-control" autofocus type="select" id="QuejasRespuesta" name="QuejasRespuesta">
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
                            <small class="form-text text-muted">Seleccione un tipo</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="QuejasNumPenal">Número de penalización *</label>
                            <input type="number" class="form-control" id="QuejasNumPenal" name="QuejasNumPenal" />
                            <small class="form-text text-muted">Escriba un número de penalización</small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="PenalizacionId">Tipo de penalización *</label>
                            <select class="form-control" autofocus type="select" id="PenalizacionId" name="PenalizacionId">
                                <option value="1">
                                    CONTRACTUALES - CANCELACIÓN DEL CONTRATO
                                </option>
                                <option value="2">
                                    CONTRACTUALES - REASIGNACIÓN DE CARTERA
                                </option>
                                <option value="3">ECONÓMICAS - MULTA</option>
                            </select>
                            <small class="form-text text-muted">Seleccione un tipo</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button name="agregar" class="btn btn-primary" value="consultar" onclick=consultarQueja(event)>
                        <span class="fa fa-search"></span> Consultar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function EditarHorario(sucursal, nombre_suc, hora_actual) {
        var o = new Option(nombre_suc, sucursal)
        $(o).html(nombre_suc)
        $("#sucursal_e").append(o)

        document.getElementById("hora_ae").value = hora_actual

        $("#modal_update_horario").modal("show")
    }
</script>

<?php echo $footer; ?>