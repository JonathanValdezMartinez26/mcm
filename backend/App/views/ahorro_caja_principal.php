<?php echo $header; ?>

<div class="right_col">
    <hr>
    <div class="col-md-3">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h5><b><i class="glyphicon glyphicon-user"></i>&nbsp <?php echo $Cliente[0]['NOMBRE']; ?></b></h5>
            </div>
            <div class="card col-md-12">
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contrato">Contrato</label>
                            <input type="text" class="form-control" id="contrato" aria-describedby="contrato" readonly placeholder="">
                            <small id="emailHelp" class="form-text text-muted">Contrato.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo_cl">Número de cliente</label>
                            <input type="number" class="form-control" id="codigo_cl" name="codigo_cl" readonly value="<?php echo $credito; ?>">
                            <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre">CURP</label>
                            <input type="text" class="form-control" id="curp_" name="curp_" readonly="" value="<?php echo $Cliente[0]['CURP']; ?>">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr>
                        <p><b><span class="fa fa-sticky-note"></span> Productos Contratados</b></p>
                        <br>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
          <div class="panel panel-body" style="margin-bottom: 0px; ">
               <div class="row">

                  <div class="card card-danger col-md-5">
                      <div class="card-header">
                          <p><b><span class="fa fa-sticky-note"></span> Movimientos de ahorro del cliente</b></p>
                          <br>
                      </div>
                      <div class="card-body">
                          <form class="" action="/Pagos/PagosRegistro/" method="GET">
                              <div class="row">
                                  <div class="col-md-4">

                                  </div>
                                  <div class="col-md-4">

                                  </div>
                                  <div class="col-md-4">

                                  </div>
                              </div>
                          </form>
                      </div>
                  </div>
                  <div class="card card-danger col-md-7">
                      <br>
                      <ul class="nav navbar-nav navbar-right">
                          <button type="button" id="recibo_pagos" class="btn btn-primary"  data-toggle="modal" data-target="#modal_agregar_movimiento" style="border: 1px solid #338300; background: #40a200;" data-keyboard="false">
                              <i class="fa fa-print" style="color: #ffffff"></i> <span style="color: #ffffff"> Nuevo moviento</span>
                          </button>
                      </ul>
                  </div>
              </div>


                <div class="row">

                    <div class="dataTable_wrapper">
                        <hr>
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Fecha</th>
                                <th>Tipo Movimiento</th>
                                <th>Descripción</th>
                                <th>Estatus</th>

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

<div class="modal fade" id="modal_agregar_movimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <center><h4 class="modal-title" id="myModalLabel">Movimientos Cuenta de Ahorro Corriente</h4></center>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form onsubmit="enviar_add(); return false" id="Add">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Fecha">Fecha</label>
                                        <input onkeydown="return false" type="date" class="form-control" id="Fecha" name="Fecha" min="<?php echo $inicio_f; ?>" max="<?php echo $fin_f; ?>" value="<?php echo $fin_f; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                    </div>
                                </div>

                                <div class="col-md-4" style="display: none">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo $usuario; ?>">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="movil">Medio de Registro</label>
                                        <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="CENTRAL">
                                        <small id="emailHelp" class="form-text text-muted">Medio de registro del pago.</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cdgns">CDGNS</label>
                                        <input type="number" class="form-control" id="cdgns" name="cdgns" readonly value="<?php echo $credito; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                                    </div>
                                </div>

                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del Cliente</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" readonly value="<?php echo $Administracion[0]['CLIENTE']; ?>">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ciclo">Ciclo</label>
                                        <input type="number" class="form-control" id="ciclo" name="ciclo" min="1" value="<?php echo $Administracion[0]['CICLO']; ?>">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="tipo">Tipo de Operación</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                            <option value="P">PAGO</option>
                                            <option value="M">MULTA</option>
                                            <option value="G">GARANTÍA</option>
                                            <option value="D">DESCUENTO</option>
                                            <?php
                                            if($cdgco == '007' ||
                                                $cdgco == '014' ||
                                                $cdgco == '020' ||
                                                $cdgco == '025' ||
                                                $cdgco == '026' ||
                                                $cdgco == '027'


                                                || $usuario == 'AMGM' || $usuario == 'GASC')
                                            {
                                                $imp = '<option value="D">DESCUENTO DE CAMPAÑA POR LEALTAD</option>';

                                                echo $imp;
                                            }
                                            ?>
                                            <option value="R">REFINANCIAMIENTO</option>
                                            <option value="S">SEGURO</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="monto">Monto *</label>
                                        <input autofocus type="text" class="form-control" id="monto" name="monto" autocomplete="off" max="10000">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ejecutivo">Nombre del Ejecutivo</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="ejecutivo" name="ejecutivo">
                                            <?php echo $status; ?>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Nombre del ejecutivo que entrega el pago.</small>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                    </form>
                </div>

            </div>
        </div>
    </div>



<style>
    .center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 40%;
    }
</style>

<?php echo $footer; ?>