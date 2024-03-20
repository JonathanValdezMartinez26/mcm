<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">

            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet"/>
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <span class="button__badge">4</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910156.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <span class="button__badge">4</span>
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2910/2910306.png -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/SolicitudesRetiroInmediato/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/7379/7379586.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <span class="button__badge">4</span>
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Solicitudes </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/7379/7379556.png -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/ClientesAhorro/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/7379/7379524.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Clientes</b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/7379/7379494.png -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/LogTransaccional/">
                <div class="col-md-5" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/7379/7379458.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Log Transaccional </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/7379/7379490.png -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/Personal/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491273.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Cajeras </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/10491/10491278.png -->
                </div>
            </a>

        </div>

        <div class="col-md-9">
            <form id="registroOperacion" name="registroOperacion">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Admin sucursales  / Saldos del día</a>
                            &nbsp;&nbsp;
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li><a onclick=mostrarAhorro() href=""><p style="font-size: 16px;"><b>Log Diario Global</b></p> </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="card col-md-12">
                                        <form id="AddPagoApertura">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-12">
                                                        <p>Para poder depositar a una cuenta de Ahorro, el cliente debe tener una cuenta activa de Ahorro Corriente, si el cliente no tiene una cuenta abierta <a href="/Ahorro/Apertura/" target="_blank">presione aquí</a>.</p>
                                                        <hr>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <div class="form-group">
                                                            <label for="ejecutivosuc">Desde* </label>
                                                            <input class="form-control mr-sm-2" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $fechaActual; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <div class="form-group">
                                                            <label for="ejecutivosuc">Hasta* </label>
                                                            <input class="form-control mr-sm-2" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $fechaActual; ?>">

                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-sm-3">
                                                        <div class="form-group">
                                                            <label for="ejecutivosuc">Operación* </label>
                                                            <select class="form-control" autofocus type="select" id="Suc" name="Suc" aria-label="Search">
                                                                <option  value="000">TODAS LAS OPERACIONES</option>
                                                                <option  value="000">ALTA CONTRATO AHORRO (+)</option>
                                                                <option  value="000">ALTA CONTRATO PEQUE (+)</option>
                                                                <option  value="000">ALTA CONTRATO INVERSIÓN (+)</option>
                                                                <option  value="000">ABONOS AHORRO (+)</option>
                                                                <option  value="000">RETIROS AHORRO (-)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <div class="form-group">
                                                            <label for="ejecutivosuc">Usuario* </label>
                                                            <select class="form-control" autofocus type="select" id="Suc" name="Suc" aria-label="Search">
                                                                <option  value="000">TODOS</option>
                                                                <option  value="000">AUTOMATICO (+)</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2" style="padding-top: 25px">
                                                        <button type="button" class="btn btn-primary" onclick="buscaCliente()">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="text-align:center;">
                                                    <hr>
                                                    <h3>Resumen de movimientos</h3>
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                                    <h4>Saldo actual cuenta ahorro corriente</h4>
                                                </div>
                                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                    <h4>$</h4>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="saldoActual" name="saldoActual" value="0.00" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <h4 id="simboloOperacion">+</h4>
                                                </div>
                                                <div class="col-md-7" style="display: flex; justify-content: flex-start;">
                                                    <h4 id="descOperacion">Depósito a cuenta ahorro corriente</h4>
                                                </div>
                                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                    <h4>$</h4>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="montoOperacion" name="montoOperacion" value="0.00" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                                    <h4>Saldo final cuenta ahorro corriente</h4>
                                                </div>
                                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                    <h4>$</h4>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="saldoFinal" name="saldoFinal" value="0.00" readonly>
                                                </div>
                                                <div class="col-md-12" style="display: flex; justify-content: center; color: red;">
                                                    <label id="tipSaldo" style="opacity:0">El saldo final no puede ser menor a $0.00.</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" id="btnRegistraOperacion" name="agregar" class="btn btn-primary" value="enviar" onclick=registraOperacion(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Procesar Transaccion</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .imagen {
        transform: scale(var(--escala, 1));
        transition: transform 0.25s;
    }

    .imagen:hover {
        --escala: 1.2;
        cursor: pointer;
    }

    .linea:hover {
        --escala: 1.2;
        cursor: pointer;
        text-decoration: underline;
    }


    /* Make the badge float in the top right corner of the button */
    .button__badge {
        background-color: #fa3e3e;
        border-radius: 50px;
        color: white;
        padding: 2px 10px;
        font-size: 19px;
        position: absolute; /* Position the badge within the relatively positioned button */
        top: 0;
        right: 0;
    }

    .button__badge_sub {
        background-color: #fa3e3e;
        border-radius: 50px;
        color: white;
        padding: 1px 7px;
        font-size: 12px;
        position: absolute; /* Position the badge within the relatively positioned button */
        top: 0;
        right: 0;
    }
</style>


<?php echo $footer; ?>