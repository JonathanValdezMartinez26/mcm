<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Cargar Archivo</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione un archivo </h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Operaciones/ReportePLDPagos/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <select class="form-control mr-sm-3" style="font-size: 18px;" autofocus type="select" id="" name="" placeholder="000000" aria-label="Search">
                                        <option value="1">Consulta</option>
                                        <option value="2">Aclaración</option>
                                        <option value="3">Reclamación</option>
                                    </select>
                                    <span id="availability1" style="font-size:15px"> Tipo de Documento *</span>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control mr-sm-2" autofocus type="file" id="myfile" name="myfile">
                                    <span id="availability1" style="font-size:15px">.ZIP *</span>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Cargar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php echo $footer; ?>
