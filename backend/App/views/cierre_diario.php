<?= $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3>Cierres por día</h3>
            <div class="clearfix"></div>
        </div>

        <div class="card card-danger col-md-5">
            <div class="card-header">
                <h5 class="card-title">Ingrese una fecha a buscar</h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control mr-sm-2" type="date" id="fecha" name="fecha" value="<?= $fecha; ?>" max="<?= $fecha; ?>">
                        <span id="availability1" style="font-size:15px">Día</span>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-default" type="submit" onclick=buscarCierre()>Buscar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card col-md-12" id="datosEncontrados">
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
            <hr style="border-top: 1px solid #787878; margin-top: 5px;">
            <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="cierres">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">Sucursal</th>
                            <th style="text-align: center; vertical-align: middle;">Asesor</th>
                            <th style="text-align: center; vertical-align: middle;">Código Grupo</th>
                            <th style="text-align: center; vertical-align: middle;">Código Cliente</th>
                            <th style="text-align: center; vertical-align: middle;">CURP Cliente</th>
                            <th style="text-align: center; vertical-align: middle;">Nombre Cliente</th>
                            <th style="text-align: center; vertical-align: middle;">Código Aval</th>
                            <th style="text-align: center; vertical-align: middle;">CURP Aval</th>
                            <th style="text-align: center; vertical-align: middle;">Nombre Aval</th>
                            <th style="text-align: center; vertical-align: middle;">Ciclo</th>
                            <th style="text-align: center; vertical-align: middle;">Fecha Inicio</th>
                            <th style="text-align: center; vertical-align: middle;">Saldo Total</th>
                            <th style="text-align: center; vertical-align: middle;">Mora Total</th>
                            <th style="text-align: center; vertical-align: middle;">Días Mora</th>
                            <th style="text-align: center; vertical-align: middle;">Tipo Cartera</th>
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

<?= $footer; ?>