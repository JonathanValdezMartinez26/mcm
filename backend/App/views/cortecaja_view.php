<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
          <h3> Resumen de cobros realizados por el ejecutivo </h3>
      </div>
      <div class="x_content">
          <a href="/Pagos/PagosRegistro/?Credito=003011" type="button" class="btn btn-primary">
              Agregar Pago
          </a>
          <hr style="border-top: 1px solid #787878; margin-top: 5px;">
          <div class="row" >
              <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i class="fa fa-calendar"></i> Fechas de Corte</span>

                      <div class="count" style="font-size: 14px"><?php echo $Administracion['CLIENTE']; ?></div>
                  </div>
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i class="fa fa-dollar"></i> Pagos Registrados</span>
                      <div class="count" style="font-size: 14px"><?php echo $Administracion['CICLO']; ?> </div>
                  </div>
                  <div class="col-md-1 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i></i><i class="fa fa-dollar"></i> Monto Total</span>
                      <div class="count" style="font-size: 14px"> $ <?php echo number_format($Administracion['MONTO']); ?></div>
                  </div>
                  <div class="col-md-1 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i></i><i class="fa fa-dollar"></i> Total a Pagos</span>
                      <div class="count" style="font-size: 14px"> $ <?php echo number_format($Administracion['MONTO']); ?></div>
                  </div>
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i></i><i class="fa fa-dollar"></i> Monto a Garantias</span>
                      <div class="count" style="font-size: 14px"> $ <?php echo number_format($Administracion['MONTO']); ?></div>
                  </div>
              </div>
          </div>

          <div class="form-group ">
              <div class="panel-body">
                  <div class="dataTable_wrapper">
                      <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                          <thead>
                          <tr>
                              <th>Medio</th>
                              <th>Fecha</th>
                              <th>CDGNS</th>
                              <th>Nombre Cliente</th>
                              <th>Ciclo</th>
                              <th>Tipo de Pago</th>
                              <th>Monto</th>
                              <th>Estatus</th>
                              <th>Ejecutivo</th>
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
  </div>
</div>

<div class="modal fade" id="modal_editar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Registro de Pago (App Móvil)</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Fecha</label>
                                    <input type="text" class="form-control" id="Fecha" aria-describedby="Fecha" disabled placeholder="" value="<?php $fechaActual = date('d-m-Y H:i:s'); echo $fechaActual; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Aparecera la fecha en la que registras el pago.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input type="number" class="form-control" id="monto" name="monto" placeholder="$1260.10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Pago *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <option value="credito">Pago</option>
                                        <option value="fecha">Garantía</option>
                                        <option value="fecha">Multa</option>
                                        <option value="fecha">Descuento</option>
                                        <option value="fecha">Refinanciamiento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Ejecutivo *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <?php echo $getSucursales; ?>
                                    </select>
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
<script>
    function EditarPago(id, id1)
    {
        $('#modal_editar_pago').modal('show'); // abri
    }
</script>

<?php echo $footer;?>
