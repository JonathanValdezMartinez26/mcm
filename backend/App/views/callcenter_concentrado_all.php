<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Concentrado CallCenter</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >

                <div class="card-body">
                    <form class="" id="consulta" action="/Operaciones/ReportePLDPagosNacimiento/" method="GET" onsubmit="return Validar()">
                        <div class="row">

                            <div class="col-md-5 col-sm-8">
                                <div class="form-group">
                                    <label for="ejecutivo">Región *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <?php echo $Region; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="ejecutivo">Fecha *</label>
                                <input class="form-control mr-sm-2"  autofocus type="date" id="fecha" name="fecha" placeholder="000000" aria-label="Search" value="<?php echo $fechaActual; ?>">
                                <span id="availability1"></span>
                            </div>

                            <div class="col-md-3" style="padding-top: 25px !important;">
                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <form name="all" id="all" method="POST">
                    <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                            <tr>
                                <th>-</th>
                                <th>Región/Agencia</th>

                                <th>Cliente</th>
                                <th>Estatus</th>
                                <th>F. Solicitud</th>
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

<?php echo $footer; ?>
