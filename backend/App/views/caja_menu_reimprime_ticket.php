<?php echo $header; ?>

    <div class="right_col">
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

            <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
                <a id="link" href="/Ahorro/CuentaCorriente/">
                    <div class="col-md-5 imagen"  style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5575/5575938.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Ahorro </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5575/5575939.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/ContratoInversion/">
                    <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5836/5836503.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Inversión </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5836/5836477.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/ContratoCuentaPeque/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2995/2995390.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Ahorro Peque </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/2995/2995467.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/EstadoCuenta/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/12202/12202939.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Estado de Cuenta </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/12202/12202918.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/SaldosDia/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5833/5833855.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Mis saldos del día </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5833/5833897.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/SolicitudRetiro/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/942/942803.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Solicitud de retiro </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/942/942752.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/ReimprimeTicket/">
                    <div class="col-md-5" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/7325/7325359.png" style="border-radius: 3px; padding-top: 5px;" width="98" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Reimprime Ticket </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/7325/7325275.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/Calculadora/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5833/5833756.png" style="border-radius: 3px; padding-top: 5px;" width="98" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Calculadora  </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5833/5833832.png -->
                    </div>
                </a>
            </div>



            <div class="col-md-9">
                <form id="registroInicialAhorro" name="registroInicialAhorro">
                    <div class="modal-content">
                        <div class="modal-header" style="padding-bottom: 0px">
                            <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                                <a class="navbar-brand">Mi espacio / Reimpresión de Tickets</a>
                                &nbsp;&nbsp;
                            </div>
                            <div>
                                <ul class="nav navbar-nav" >
                                    <li><a onclick=mostrarAhorro() href=""><p style="font-size: 16px;"><b>Tickets</b></p></a></li>
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
                                                            <div class="col-md-4">
                                                                <p>Para poder depositar a una cuenta de Ahorro, el cliente debe tener una cuenta activa de Ahorro Corriente, si el cliente no tiene una cuenta abierta <a href="/Ahorro/Apertura/" target="_blank">presione aquí</a>.</p><hr>
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12" style="text-align:center;">
                                                            <h4>Mi historial de tickets</h4>
                                                            <br>
                                                        </div>
                                                    </div>

                                                    <div class="card col-md-12">
                                                        <form name="all" id="all" method="POST">
                                                            <div class="dataTable_wrapper">
                                                                <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Fecha</th>
                                                                        <th>Monto</th>
                                                                        <th>Cliente</th>
                                                                        <th>Caja</th>
                                                                        <th>Acciones</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                                                        <td style="padding: 0px !important;" width="45" nowrap="" onclick="InfoAdmin();"><span class="count_top" style="font-size: 14px"> &nbsp;&nbsp;<i class="fa fa-barcode" style="color: #787b70"></i> </span>0030110147895210 &nbsp;</td>
                                                                        <td style="padding: 0px !important;">15/03/2024 12:24:04</td>
                                                                        <td style="padding: 0px !important;">1200.00</td>
                                                                        <td style="padding: 0px !important;">EJEMPLO EJEMPLO EJEMPLO</td>
                                                                        <td style="padding: 0px !important;">AMGM</td>
                                                                        <td style="padding: 0px !important;" class="center">
                                                                            <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-print"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                                                        <td style="padding: 0px !important;" width="45" nowrap="" onclick="InfoAdmin();"><span class="count_top" style="font-size: 14px"><i class="fa fa-barcode" style="color: #787b70"></i> </span>0030110147895210</td>
                                                                        <td style="padding: 0px !important;">15/03/2024 12:24:04</td>
                                                                        <td style="padding: 0px !important;">1200.00</td>
                                                                        <td style="padding: 0px !important;">EJEMPLO EJEMPLO EJEMPLO</td>
                                                                        <td style="padding: 0px !important;">AMGM</td>
                                                                        <td style="padding: 0px !important;" class="center">
                                                                            <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-print"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                                                        <td style="padding: 0px !important;" width="45" nowrap="" onclick="InfoAdmin();"><span class="count_top" style="font-size: 14px"><i class="fa fa-barcode" style="color: #787b70"></i> </span>0030110147895210</td>
                                                                        <td style="padding: 0px !important;">15/03/2024 12:24:04</td>
                                                                        <td style="padding: 0px !important;">1200.00</td>
                                                                        <td style="padding: 0px !important;">EJEMPLO EJEMPLO EJEMPLO</td>
                                                                        <td style="padding: 0px !important;">AMGM</td>
                                                                        <td style="padding: 0px !important;" class="center">
                                                                            <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-print"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
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
    </style>


<?php echo $footer; ?>