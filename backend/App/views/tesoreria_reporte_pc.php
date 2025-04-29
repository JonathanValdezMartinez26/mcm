<?php
$fecha = date('Y-m-d');
?>

<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Reporte Productora Cultiva</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaI" value="<?= $fecha ?>" max="<?= $fecha ?>">
                                <span>Fecha inicial</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaF" value="<?= $fecha ?>" max="<?= $fecha ?>">
                                <span>Fecha final</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select class="form-control" id="sucursal">
                                    <?= $sucursales ?>
                                </select>
                                <span>Sucursal</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="buscar">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado">
            <div class="botones">
                <button type="button" class="btn btn-success" id="excel">
                    <span class="fa fa-file-excel-o">&nbsp;</span>Exportar a Excel
                </button>
            </div>
            <hr>
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="reporte">
                    <thead>
                        <tr>
                            <th>Fecha de solicitud</th>
                            <th>Crédito</th>
                            <th>Ciclo</th>
                            <th>Cliente</th>
                            <th>RFC</th>
                            <th>Fecha inicio</th>
                            <th>Tipo operación</th>
                            <th>Sucursal</th>
                            <th>Región</th>
                            <th>Monto</th>
                            <th>Banco</th>
                            <th>CLABE</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>