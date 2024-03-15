<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Apertura de cuentas</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5">
                <div class="card-header">
                    <h5 class="card-title">Ingrese el número del cliente y valide que sus datos sean correctos</h5>
                </div>
                <div class="card-body">
                    <form class="" action="/Apertura/Ahorro/" method="GET">
                        <div class="row">

                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="text" onKeypress="if (event.keyCode < 9 || event.keyCode > 57) event.returnValue = false;" id="Cliente" name="Cliente" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-default" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row">
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="x_content">
                            <br />
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <label style="font-size: 14px; color: black;">Consideraciones:</label>
                                <li style="color: black;">
                                    Valide que el Cliente tenga una solicitud de registro con su Administrador.
                                </li>
                                <li style="color: black;">
                                    Valide la información del Cliente.
                                </li>
                                <li style="color: black;">
                                    Valide que el Cliente se haya registrado exitosamente con su Administrador.
                                </li>
                                <li style="color: black;">
                                    Si usted tiene problemas con la apertura de cuenta contacte a su Analista Soporte para pedir apoyo.
                                </li>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>



<?php echo $footer; ?>