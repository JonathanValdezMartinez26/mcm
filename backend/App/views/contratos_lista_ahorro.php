<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Contratos de Ahorro - Asignación de Beneficiarios (Pendientes)</h3>
                <div class="clearfix"></div>
            </div>
            <div class="x_title d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_alta_contrato">
                    <i class="fa fa-plus"></i> Dar de Alta Contrato
                </button>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-contratos">
                        <thead>
                            <tr>
                                <th>CDGNS</th>
                                <th>Nombre</th>
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

<!-- Modal dar de alta contrato Alberto-->
<div class="modal fade" id="modal_alta_contrato" tabindex="-1" role="dialog" aria-labelledby="modalAltaContratoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAltaContratoLabel">Dar de Alta Contrato y Beneficiarios</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_alta_contrato" onsubmit="return false;">

                    <!-- CDGNS + Botón Buscar -->
                    <div class="row align-items-end mb-3">
                        <div class="col-md-4">
                            <label>Código del Crédito</label>
                            <input type="text" class="form-control form-control-sm" id="alta_cdgns" name="cdgns" placeholder="Ej. 000000" maxlength="6" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-sm mt-2" id="btnBuscarNuevo">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                        <br>
                    </div>
                    <hr>
                    <br>

                    <!-- Datos del cliente y contrato -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input type="text" class="form-control form-control-sm" id="alta_nombre" name="nombre" placeholder="Nombre completo" required disabled>
                            <input type="hidden" id="noCredito" name="noCredito">
                            <input type="hidden" id="noCliente" name="noCliente">
                        </div>
                        <div class="col-md-3">
                            <label>Tipo de Ahorro</label>
                            <input type="text" class="form-control form-control-sm" value="Ahorro Simple" name="tipo" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>Tasa (%)</label>
                            <input type="text" class="form-control form-control-sm" value="6.00" name="tasa" readonly>
                        </div>
                    </div>

                    <hr>
                    <h5>Beneficiarios</h5>

                    <!-- Beneficiarios -->
                    <div id="contenedor-beneficiarios">
                        <div class="row beneficiario-row-header mb-2">
                            <div class="col-md-6" style="display: flex; align-items: center; justify-content: center;">
                                <label>Nombre completo</label>
                            </div>
                            <div class="col-md-3" style="display: flex; align-items: center; justify-content: center;">
                                <label>Parentesco</label>
                            </div>
                            <div class="col-md-3" style="display: flex; align-items: center; justify-content: center;">
                                <label>Porcentaje (%)</label>
                            </div>
                        </div>

                        <div class="row beneficiario-row mb-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm nombreBeneficiario" name="beneficiario_nombre[]" required disabled>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control form-control-sm parentesco-select parentescoBeneficiario" name="beneficiario_parentesco[]" required disabled>
                                    <?= $parentescosOptions; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control form-control-sm porcentajeBeneficiario" name="beneficiario_porcentaje[]" max="100" min="0" step="0.01" required disabled>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-success btn-sm btnAgregaBeneficiario" disabled>
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-md-1" style="display: none;">
                                <button type="button" class="btn btn-danger btn-sm btnEliminaBeneficiario" disabled>
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" form="form_alta_contrato" id="btnRegistraContrato">
                    <span class="glyphicon glyphicon-floppy-disk"></span> Guardar Contrato
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal alta de contrato -->
<div class="modal fade" id="modal_contrato" tabindex="-1" role="dialog" aria-labelledby="modalContratoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h4 class="modal-title" id="modalContratoLabel">Registrar Contrato y Beneficiarios</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_contrato" onsubmit="guardarContrato(); return false;">
                    <div class="row">
                        <div class="col-md-2">
                            <label>CDGNS</label>
                            <input type="text" class="form-control form-control-sm" id="modal_cdgns" name="cdgns" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Nombre</label>
                            <input type="text" class="form-control form-control-sm" id="modal_nombre" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>Tipo de Ahorro</label>
                            <input type="text" class="form-control form-control-sm" value="Ahorro Simple" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>Tasa Anual (%)</label>
                            <input type="text" class="form-control form-control-sm" value="6.00" readonly>
                        </div>
                    </div>

                    <hr>
                    <h5>Beneficiario 1</h5>
                    <div id="contenedor-beneficiarios" class="row">

                        <div class="col-md-4">
                            <label>Nombre completo</label>
                            <input type="text" class="form-control form-control-sm" name="beneficiario_nombre[]" required>
                        </div>

                        <div class="col-md-4">
                            <label>Parentesco</label>
                            <select class="form-control form-control-sm" name="beneficiario_parentesco[]" required>
                                <?= $parentescosOptions; ?>
                            </select>
                        </div>


                        <div class="col-md-4">

                            <label>Porcentaje (%)</label>
                            <input type="number" class="form-control form-control-sm" name="beneficiario_porcentaje[]" max="100" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <br>
                            <button type="button" class="btn btn-success btn-sm" onclick="agregarBeneficiario()">
                                <i class="fa fa-plus"></i> Agregar Beneficiario
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" class="btn btn-primary" form="form_contrato">
                    <span class="glyphicon glyphicon-floppy-disk"></span> Guardar Contrato
                </button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>