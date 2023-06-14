<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta Información de Clientes</h3>
                <div class="clearfix"></div>
            </div>
                <div class="x_content">

                    <div class="card card-danger col-md-5" >
                        <div class="card-header">
                            <h5 class="card-title">Ingrese el número de crédito y ciclo</h5>
                        </div>
                        <div class="card-body">
                            <form class="" action="/CallCenter/" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="number" id="credito" name="credito" placeholder="000000" aria-label="Search">
                                    <span id="availability1"></span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="number" id="ciclo" name="ciclo" placeholder="00" aria-label="Search">
                                    <span id="availability1"></span>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

            <br>
        </div>
    </div>
</div>
<?php echo $footer; ?>
