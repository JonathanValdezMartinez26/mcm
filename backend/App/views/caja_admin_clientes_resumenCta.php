<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="cliente">Código de cliente SICAFIN</label>
                    <input class="form-control" id="cliente" name="cliente" value="<?= $cliente ?>" disabled>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="nombre">Nombre del cliente</label>
                    <input class="form-control" id="nombre" name="nombre" value="<?= $nombre ?>" disabled>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="anio">Año</label>
                    <select class="form-control" id="anio" name="anio" onchange=cambiaAnio()>
                        <?php
                        for ($i = date('Y'); $i >= 2020; $i--) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="mes">Mes</label>
                    <select class="form-control" id="mes" name="mes">
                        <?php
                        $meses = [
                            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                        ];

                        for ($i = 0; $i < count($meses); $i++) {
                            echo "<option value='" . ($i + 1) . "' " . ($i + 1 == date("m") ? "selected" : "") . ">$meses[$i]</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form name="all" id="all" method="POST">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="tablaResumenCta">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th>Abono</th>
                                    <th>Cargo</th>
                                    <th>Saldo</th>
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