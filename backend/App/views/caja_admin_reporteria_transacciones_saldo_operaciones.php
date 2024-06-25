<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />


            <a id="link" href="/AdminSucursales/Reporteria/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3201/3201558.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b> Consultar Reportes</b></p>
                    <! --https://cdn-icons-png.flaticon.com/512/1605/1605350.png IMAGEN EN COLOR -->
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

                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Transacciones</b></p>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="card col-md-12">
                                        <form class="" id="consulta" action="/Operaciones/PerfilTransaccional/" method="GET" onsubmit="return Validar()">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="Inicial">Desde *</label>
                                                        <input type="date" class="form-control" min="2024-05-22" max="<?php echo $fechaActual;?>" id="Inicial" name="Inicial" value="<?php echo $fecha_inicial; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="Final">Hasta *</label>
                                                        <input type="date" class="form-control" min="2024-05-22" max="<?php echo $fechaActual;?>" id="Final" name="Final" value="<?php echo $fecha_final; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="Operacion">Operación *</label>
                                                        <select class="form-control" id="Operacion" name="Operacion">
                                                            <?php echo $operacion; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="Producto">Producto *</label>
                                                        <select class="form-control" id="Producto" name="Producto">
                                                            <?php echo $productos; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="Sucursal">Sucursal activa*</label>
                                                        <select class="form-control" id="Sucursal" name="Sucursal">
                                                            <option value="0">TODAS LAS SUCURSALES</option>
                                                            <?php echo $sucursales; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4" style="padding-top: 25px">
                                                    <button class="btn btn-primary" onclick="getLog()">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>

                                        <form name="all" id="all" method="POST">
                                            <br>
                                            <button id="export_excel_con_transacciones" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                                            <hr>
                                            <div class="col-md-12">
                                                <div class="col-md-12">
                                                    <p>Atención:
                                                        <br><span class="count_top" style="font-size: 18px"><i class="fa fa-minus" style="color: #00ac00"></i></span> Movimiento virtual ingreso.
                                                        | <span class="count_top" style="font-size: 18px"><i class="fa fa-arrow-down" style="color: #00ac00"></i></span> Movimiento en efectivo egreso.
                                                        | <span class="count_top" style="font-size: 18px"><i class="fa fa-minus" style="color: #ac0000"></i></span> Movimiento virtual egreso.
                                                        | <span class="count_top" style="font-size: 18px"><i class="fa fa-arrow-up" style="color: #ac0000"></i></span> Movimiento en efectivo egreso.
                                                        | <span class="count_top" style="font-size: 18px"><i class="fa fa-asterisk" style="color: #005dac"></i></span> Movimiento virtual (Solicitud Retiro).

                                                </div>
                                            </div>
                                            <div class="dataTable_wrapper">
                                                <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th></th>
                                                            <th>Fecha Transacción</th>
                                                            <th>Detalle Producto</th>
                                                            <th>Ingreso</th>
                                                            <th>Egreso</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?= $tabla; ?>
                                                    </tbody>
                                                </table>
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
        position: absolute;
        /* Position the badge within the relatively positioned button */
        top: 0;
        right: 0;
    }
</style>

<?php echo $footer; ?>