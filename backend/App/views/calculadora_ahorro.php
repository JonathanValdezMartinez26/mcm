<?php echo $header; ?>

<div class="right_col" style="padding-top: 210px;" style="margin-left: 340px; ">
    <div class="row">
        <div class="col-md-7" style="margin-left: 290px; border: 1px solid #dfdfdf; border-radius: 30px;">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <p style=" font-size: 35px;">Calcula el rendimiento de tus clientes</p>
            </div>

        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-2" style="margin-left: 290px; border: 1px solid #dfdfdf; border-radius: 30px;">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <img src="https://cdn-icons-png.flaticon.com/512/3050/3050243.png" width="220" height="220" alt="hucha icono gratis" title="hucha icono gratis">
            </div>
            <br>
            <p style="font-size: 14px"><b>Un centavo bien ahorrado, es un centavo bien ganado.</b></p>

            <br>
        </div>

        <div class="col-md-2" style="margin-left: 70px; border: 1px solid #dfdfdf; border-radius: 30px;">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <img src="https://cdn-icons-png.flaticon.com/512/14991/14991719.png" style="border-radius: 30px;" width="220" height="220" alt="eficiencia icono gratis" title="eficiencia icono gratis">
            </div>
            <br>
            <p style="font-size: 14px"><b>Inversion de ahorros: Invierte en tu futuro.  </b></p>
        </div>

        <div class="col-md-2" style="margin-left: 70px; border: 1px solid #dfdfdf; border-radius: 30px;">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <img src="   https://cdn-icons-png.flaticon.com/512/2880/2880483.png " width="220" height="220" alt="" title="" class="img-small">
            </div>
            <br>
            <p style="font-size: 14px"><b>El ahorro para los más peques del hogar.</b></p>
        </div>
    </div>



</div>

<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Registro de pago por apertura y ahorro inicial cuenta corriente</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_addssss(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_pago">Fecha</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="fecha_pago" name="fecha_pago" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contrato">Contrato</label>
                                    <input type="text" class="form-control" id="contrato" aria-describedby="contrato" readonly placeholder="">
                                    <small id="emailHelp" class="form-text text-muted">Contrato.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="codigo_cl">Número de cliente</label>
                                    <input type="number" class="form-control" id="codigo_cl" name="codigo_cl" readonly value="<?php echo $credito; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" readonly value="<?php echo $Cliente[0]['NOMBRE']; ?>">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="curp">CURP</label>
                                    <input type="text" class="form-control" id="curp" name="curp" readonly value="<?php echo $Cliente[0]['CURP']; ?>">
                                </div>
                            </div>


                            <div class="col-sm-12">
                                <table class="table table-striped table-bordered table-hover dataTable no-footer">
                                    <thead>
                                    <tr role="row">
                                        <th class="header" rowspan="1" colspan="1" aria-label="Cod Sucursal" style="width: 250px;">Concepto del pago</th>
                                        <th class="header sorting" tabindex="0" aria-controls="muestra-cupones" rowspan="1" colspan="1" aria-label="Nombre Sucursal: activate to sort column ascending" style="width: 46px;">Monto</th>
                                    </thead>
                                    <tbody>
                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-12" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="apertura_cuenta" name="apertura_cuenta" autocomplete="off" max="10000" value="APERTURA DE CUENTA - INSCRIPCIÓN" readonly>
                                                    <small id="emailHelp" class="form-text text-muted">Monto fijo.</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-2" style="padding-top: 5px;">
                                                <div class="form-group">
                                                    <h4>$</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-9" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="monto_apertura" name="monto_apertura" autocomplete="off" max="10000" value="50" readonly>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-12" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="monto" name="monto" autocomplete="off" max="10000" value="CAPITAL INICIAL - CUENTA CORRIENTE" readonly>
                                                    <small id="emailHelp" class="form-text text-muted">El monto debe ser mayor a $100.00 MN.</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-2" style="padding-top: 5px;">
                                                <div class="form-group">
                                                    <h4>$</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-9" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="monto_ahorro" name="monto_ahorro" autocomplete="off" max="10000">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>

