<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de Pagos de Clientes Cultiva (PLD)</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el rango de fechas a generar el , solo tienen permitido 7 días </h5>
                </div>

                <div class="card-body">


                    <div class="outer-container">
                        <form action="" method="post"
                              name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
                            <div>
                                <label>Choose Excel
                                    File</label> <input type="file" name="file"
                                                        id="file" accept=".xls,.xlsx">
                                <button type="submit" id="submit" name="import"
                                        class="btn-submit">Import</button>

                            </div>

                        </form>

                    </div>
                    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php echo $footer; ?>
