<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3> Registro de Pagos</h3>
            <div class="clearfix"></div>
        </div>

        <div class="card card-danger col-md-5">
            <div class="card-header">
                <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
            </div>

            <div class="card-body">
                <form class="" action="/Pagos/PagosConsultaUsuarios/" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-control mr-sm-3" style="font-size: 18px;" autofocus type="select" id="opcion_credito" name="opcion_credito" placeholder="000000">
                                <option value="credito">Crédito</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="text" onKeypress="if (event.keyCode < 9 || event.keyCode > 57) event.returnValue = false;" id="Credito" name="Credito" placeholder="000000" aria-label="Search">

                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-default" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>