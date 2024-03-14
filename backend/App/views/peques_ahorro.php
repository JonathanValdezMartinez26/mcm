<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <br>
            <div class="col-md-4">
                <div class="panel panel-body" style="margin-bottom: 0px;">
                    <div class="x_title">
                        <h3>Alta del cliente (Peque)</h3>
                    </div>
                    <div class="card col-md-12">
                        <div class="row">
                            <p>Para poder aperturar una cuenta de Ahorro Peque el CO-TITULAR debe tener una cuenta activa de Ahorro Corriente, si el CO-TITULAR no tienen una cuenta abierta <a href="/Ahorro/Apertura/" target="_blank">presione aquí</a>
                                .  </p>
                            <hr>
                            <div class="col-md-9">
                                <label for="movil">Clave de contrato o código del cliente (SICAFIN)</label>
                                <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="Cliente" name="Cliente" value="" placeholder="000000" required>
                            </div>

                            <div class="col-md-3" style="padding-top: 25px">
                                <button type="button" class="btn btn-primary" onclick="buscaCliente()">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movil">Fecha de registro</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro.</small>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="cdgns">Clave de cliente (Co-Titular)</label>
                                    <input type="number" class="form-control" id="cdgns" name="cdgns" readonly="" value="003011">
                                    <small id="emailHelp" class="form-text text-muted">Número de acreditado MCM</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Primer nombre*</label>
                                    <input type="text" class="form-control" id="nom_cliente" name="nombre1" >
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="movil">Fecha de registro (Peque)</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro.</small>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="cdgns">Clave de cliente (Peque)</label>
                                    <input type="number" class="form-control" id="cdgns" name="cdgns" readonly="" value="003011">
                                    <small id="emailHelp" class="form-text text-muted">Número de acreditado MCM</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Primer nombre*</label>
                                    <input type="text" class="form-control" id="nom_cliente" name="nombre1" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Segundo nombre</label>
                                    <input type="text" class="form-control" id="nom_cliente" name="nombre2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Apellido materno*</label>
                                    <input type="text" class="form-control" id="nom_cliente" name="apellido_p">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Apellido paterno*</label>
                                    <input type="text" class="form-control" id="nom_cliente" name="apellio_m">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="nombre">Sexo*</label>
                                <div class="form-group">

                                    <div class="col-md-6">
                                        <input type="radio" name="sexo" id="cHombre" checked>
                                        <label for="cHombre">Hombre</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="sexo" id="cMujer">
                                        <label for="cMujer">Mujer</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Fecha de nacimiento*</label>
                                    <input type="date" class="form-control" id="fec_nacimiento" name="fec_nacimiento">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Pais de nacimiento*</label>
                                    <input type="text" class="form-control" id="nom_cliente" name="apellio_m" value="MÉXICO" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Entidad de nacimiento</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option value="P">CDMX</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nombre">CURP*</label>
                                    <input type="text" class="form-control" id="curp_" name="curp_" readonly="" value="<?php echo $Cliente[0]['CURP']; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nombre">Edad*</label>
                                    <input type="text" class="form-control" id="edad" name="edad" readonly="" value="<?php echo $Cliente[0]['EDAD']; ?> Años">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input type="radio" name="direccion" id="cDireccion" checked>
                                    <label for="cDireccion">Dirección (Autoriza usar la dirección del acreditado) *</label>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                    <textarea type="text" class="form-control" id="direccion" name="direccion" rows="3" cols="50" readonly>
                            </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>

            <form id="registroInicialAhorro" name="registroInicialAhorro">
                <div class="col-md-8">
                    <div class="panel panel-body" style="margin-bottom: 0px;">
                        <div class="x_title">
                            <h3> Apertura de cuenta Ahorro Peques</h3>
                        </div>
                        <p><b><span class="fa fa-sticky-note"></span> Datos básicos de apertura</b></p>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="Fecha">Fecha de apertura</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="fecha" name="fecha" min="2024-03-07" max="2024-03-11" value="2024-03-11">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tipo">Tasa Anual</label>
                                    <select class="form-control mr-sm-3" autofocus="" type="select" id="tasa" name="tasa">
                                        <option value="5">5 %</option>
                                    </select>
                                    <small id="emailHelp" class="form-text text-muted">Rendimiento.</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">Monto mínimo</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="monto_min" name="monto_min" value="$100.00" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Ahorro para cuenta corriente.</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">Monto máximo</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="monto_max" name="monto_max" value="NO APLICA" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Ahorro para cuenta corriente.</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">Monto máximo</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="monto_max" name="monto_max" value="NO APLICA" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Ahorro para cuenta corriente.</small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>

<?php echo $footer; ?>