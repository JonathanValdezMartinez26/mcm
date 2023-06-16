<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de Pagos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Pagos/Layout/" method="GET">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <select class="form-control mr-sm-3" style="font-size: 21px;" autofocus type="select" id="id_sucursal" name="id_sucursal" placeholder="000000" aria-label="Search">
                                        <?php echo $getSucursales; ?>
                                    </select>
                                    <span id="availability1"></span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
                                    <span id="availability1"></span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
                                    <span id="availability1"></span>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>
<?php echo $footer; ?>
