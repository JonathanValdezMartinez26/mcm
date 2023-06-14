<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
          <h3> Resumen de cobros realizados por el ejecutivo </h3>
          <div class="clearfix"></div>
      </div>
      <div class="x_content">

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

<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Agregar Nuevo Registro  Pagos</h4></center>
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
                                        <?php echo $status; ?>
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


<?php echo $footer;?>
