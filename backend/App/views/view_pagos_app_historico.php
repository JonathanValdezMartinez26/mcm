<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Histórico de pagos capturados en campo por los ejecutivos <span class="fa fa-mobile"></span></h3>
            </div>
            <div class="card col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha inicio</label>
                            <input class="form-control mr-sm-2" type="date" id="fInicio" value="<?= $fInicio; ?>" max="<?= $fFin; ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha fin</label>
                            <input class="form-control mr-sm-2" type="date" id="fFin" value="<?= $fFin; ?>" max="<?= $fFin; ?>">
                        </div>
                    </div>
                    <div class="col-md-3" style="display: flex; align-content: flex-end; flex-wrap: wrap; height: 64px;">
                        <button class="btn btn-primary btn-circle" type="submit" onclick=buscar()><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
                <hr>
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="tbl-historico">
                        <thead>
                            <tr>
                                <th>Código de Barras</th>
                                <th>Sucursal</th>
                                <th>Pagos Cobrados</th>
                                <th>Ejecutivo</th>
                                <th>Cobro</th>
                                <th>Pagos</th>
                                <th>Multas</th>
                                <th>Ref</th>
                                <th>Des</th>
                                <th>Gar</th>
                                <th>Monto Total Recolectado (Entregar)</th>
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

<?= $footer; ?>