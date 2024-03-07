<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Agregar Quejas REDECO</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_usuario">
                    <i class="fa fa-plus"></i> Reportar queja
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                            <th>Mes</th>
                            <th>Folio</th>
                            <th>Fecha</th>
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

<div class="modal fade" id="modal_agregar_usuario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Agregar queja REDECO</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_user(); return false" id="Add_user">
                        <div class="row">


                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="hora">Mes a informar *</label>
                                        <select class="form-control" autofocus type="select" id="hora" name="hora">
                                            <option value="1">ENERO</option>
                                            <option value="2">FEBRERO</option>
                                            <option value="2">MARZO</option>
                                            <option value="2">ABRIL</option>
                                            <option value="2">MAYO</option>
                                            <option value="2">JUNIO</option>
                                            <option value="2">JULIO</option>
                                            <option value="2">AGOSTO</option>
                                            <option value="2">SEPTIEMBRE</option>
                                            <option value="2">OCTUBRE</option>
                                            <option value="2">NOVIEMBRE</option>
                                            <option value="2">DICIEMBRE</option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Perfil</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Numero de quejas *</label>
                                        <input type="number" class="form-control" id="num_quejas" name="num_quejas" readonly value="1">
                                        <small id="emailHelp" class="form-text text-muted">Número de quejas a reportar</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Número de folio *</label>
                                        <input type="text" class="form-control" id="num_quejas" name="num_quejas" placeholder="YYYY/REDECO/00000">
                                        <small id="emailHelp" class="form-text text-muted">Número de folio</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Fecha de recepción *</label>
                                        <input type="date" class="form-control" id="num_quejas" name="num_quejas">
                                        <small id="emailHelp" class="form-text text-muted">Fecha en la que se recibio la queja</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="medio_recepcion">Medio de recepción *</label>
                                        <select class="form-control" autofocus type="select" id="medio_recepcion" name="medio_recepcion">
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nivel_atencion">Nivel de atención o contacto *</label>
                                        <select class="form-control" autofocus type="select" id="nivel_atencion" name="nivel_atencion">
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Producto o servicio *</label>
                                        <input type="input" class="form-control" id="num_quejas" name="num_quejas" placeholder="">
                                        <small id="emailHelp" class="form-text text-muted">Escriba el producto o servicio</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Causa de la queja *</label>
                                        <input type="text" class="form-control" id="num_quejas" name="num_quejas">
                                        <small id="emailHelp" class="form-text text-muted">Escriba la causa</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">PORI *</label>
                                        <select class="form-control" autofocus type="select" id="pori" name="pori">
                                            <option value="SI">SI</option>
                                            <option value="NO">NO</option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Estatus *</label>
                                        <select class="form-control" autofocus type="select" id="pori" name="pori">
                                            <option value="1">PENDIENTE</option>
                                            <option value="2">CONCLUIDO</option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="estado">Estado *</label>
                                        <select class="form-control" autofocus type="select" id="estado" name="estado">
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="municipio">Municipio *</label>
                                        <select class="form-control" autofocus type="select" id="municipio" name="municipio">
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="localidad">Localidad *</label>
                                        <select class="form-control" autofocus type="select" id="localidad" name="localidad">
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="colonia">Colonia *</label>
                                        <select class="form-control" autofocus type="select" id="colonia" name="colonia">
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">CP *</label>
                                        <input type="number" class="form-control" id="num_quejas" name="num_quejas" maxlength="5">
                                        <small id="emailHelp" class="form-text text-muted">Escriba un CP valido</small>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Tipo de Persona *</label>
                                        <select class="form-control" autofocus type="select" id="pori" name="pori">
                                            <option value="1">FISICA</option>
                                            <option value="2">MORAL</option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Sexo *</label>
                                        <select class="form-control" autofocus type="select" id="pori" name="pori">
                                            <option value="H">HOMBRE</option>
                                            <option value="M">MUJER</option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Edad *</label>
                                        <input type="input" class="form-control" id="num_quejas" name="num_quejas">
                                        <small id="emailHelp" class="form-text text-muted">Escriba la edad</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Fecha de resolucion *</label>
                                        <input type="date" class="form-control" id="num_quejas" name="num_quejas">
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una fecha</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Fecha notificación al usuario *</label>
                                        <input type="date" class="form-control" id="num_quejas" name="num_quejas">
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una fecha</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Tipo de penalización *</label>
                                        <select class="form-control" autofocus type="select" id="pori" name="pori">
                                            <option value="1">1 - Totalmente favorable al usuario</option>
                                            <option value="2">2 - Desfavorable al Usuario</option>
                                            <option value="3">3 - Parcialmente
                                                favorable al  usuario
                                                y puede ser
                                                nulo si el
                                                Estado de la
                                                Queja es
                                                igual a (1)
                                                Pendiente
                                            </option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Número de penalización *</label>
                                        <input type="number" class="form-control" id="num_quejas" name="num_quejas">
                                        <small id="emailHelp" class="form-text text-muted">Escriba un numero de penalización</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_registro">Tipo de penalización *</label>
                                        <select class="form-control" autofocus type="select" id="pori" name="pori">
                                            <option value="1">CONTRUACTALES - CANCELACIÓN DEL CONTRATO</option>
                                            <option value="2">CONTRACTUALES - REASIGNACIÓN DE CARTERA</option>
                                            <option value="3">ECONOMICAS - MULTA</option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Selecciona una opción</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Registrar Usuario</button>
                </form>
            </div>

        </div>
    </div>
</div>


<script>
    function EditarHorario(sucursal, nombre_suc, hora_actual) {


        var o = new Option(nombre_suc, sucursal);
        $(o).html(nombre_suc);
        $("#sucursal_e").append(o);

        document.getElementById("hora_ae").value = hora_actual;

        $('#modal_update_horario').modal('show');

    }
</script>

<?php echo $footer; ?>
