<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="cliente">Código de cliente SICAFIN</label>
                    <input class="form-control" id="cliente" name="cliente" value="<?= $cliente ?>" disabled>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre">Nombre del cliente</label>
                    <input class="form-control" id="nombre" name="nombre" value="<?= $nombre ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="saldoFin">Saldo</label>
                    <input class="form-control" id="saldoFin" name="saldoFin" value="$<?= number_format($saldoFinal, 2, '.', ','); ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="totAbn">Total de depósitos</label>
                    <input class="form-control" id="totAbn" name="totAbn" value="<?= $conteoAbonos ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="montoAbn">Monto depositado</label>
                    <input class="form-control" id="montoAbn" name="montoAbn" value="$<?= number_format($montoAbonos, 2, '.', ','); ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="totCrg">Total de retiros</label>
                    <input class="form-control" id="totCrg" name="totCrg" value="<?= $conteoCargos ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="montoCrg">Monto retirado</label>
                    <input class="form-control" id="montoCrg" name="montoCrg" value="$<?= number_format($montoCargos, 2, '.', ','); ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="totTrn">Total de inversiones</label>
                    <input class="form-control" id="totTrn" name="totTrn" value="<?= $conteoTransferencias ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="montoTrn">Monto invertido</label>
                    <input class="form-control" id="montoTrn" name="montoTrn" value="$<?= number_format($montoTransferencias, 2, '.', ','); ?>" disabled>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form name="all" id="all" method="POST">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="tablaResumenCta">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo cuenta</th>
                                    <th>Concepto</th>
                                    <th>Abono</th>
                                    <th>Cargo</th>
                                    <th>Saldo</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $filas; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">

</div>

<?php echo $script; ?>