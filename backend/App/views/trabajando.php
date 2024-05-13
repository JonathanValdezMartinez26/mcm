<?php echo $header; ?>

    <div class="right_col">
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
                <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
                <a id="link" href="/AdminSucursales/SaldosDiarios/">
                    <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2910/2910156.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                        <! -- -->
                    </div>
                </a>
                <a id="link" href="/AdminSucursales/SolicitudesReimpresionTicket/">
                    <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2972/2972449.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <span class="button__badge">4</span>
                        <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Solicitudes</b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/2972/2972528.png IAMGEN EN COLOR -->
                    </div>
                </a>
                <a id="link" href="/AdminSucursales/EstadoCuentaCliente/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5864/5864275.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Catalogo de Clientes </b></p>
                        <! -- IMAGEN EN COLOR -->
                    </div>
                </a>
                <a id="link" href="/AdminSucursales/Log/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/10491/10491361.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Log Transaccional </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/2761/2761118.png IMAGEN EN COLOR -->
                    </div>
                </a>
                <a id="link" href="/AdminSucursales/Configuracion/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/10491/10491249.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Configurar Módulo </b></p>
                        <! -- IMAGEN EN COLOR -->
                    </div>
                </a>
                <a id="link" href="/AdminSucursales/Reporteria/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/3201/3201558.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Consultar Reportes </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/3201/3201558.png IMAGEN EN COLOR -->
                    </div>
                </a>
            </div>
            <div class="col-md-9">
                <form id="registroOperacion" name="registroOperacion">
                    <div class="modal-content">
                        <div class="modal-header" style="padding-bottom: 0px">
                            <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                                <a class="navbar-brand">Admin sucursales / Consultar reportes / Transacciones</a>
                            </div>
                            <div>
                                <ul class="nav navbar-nav">
                                    <li class="linea"><a href="/AdminSucursales/Reporteria/">
                                            <p style="font-size: 16px;">Flujo efectivo</p>
                                        </a></li>
                                    <li><a href="">
                                            <p style="font-size: 16px;"><b>Transacciones</b></p>
                                        </a></li>
                                    <li class="linea"><a href="/AdminSucursales/HistorialFondeoSucursal/">
                                            <p style="font-size: 16px;">Historial fondeo sucursal</p>
                                        </a></li>
                                    <li class="linea"><a href="/AdminSucursales/HistorialRetiroSucursal/">
                                            <p style="font-size: 16px;">Historial retiro sucursal</p>
                                        </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="card col-md-12">
                                            <img src=" https://bancoestudiantil.com/wp-content/uploads/2017/07/img-under-construction-02.png" style="border-radius: 3px; padding-top: 5px;" width="900" height="450">

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
            position: absolute;
            /* Position the badge within the relatively positioned button */
            top: 0;
            right: 0;
        }
    </style>

<?php echo $footer; ?>