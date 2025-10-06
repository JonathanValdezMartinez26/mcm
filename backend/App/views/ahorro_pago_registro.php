<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Pago de Ahorro</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-header" style="margin: 20px 0;">
                    <span class="card-title">Ingrese el número de crédito</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="creditoBuscar">Crédito:</label>
                                <input class="form-control" style="font-size: 24px;" type="text" id="creditoBuscar" placeholder="000000" maxlength="6">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group" style="min-height: 68px; display: flex; align-items: center; justify-content: space-between;">
                                <button type="button" class="btn btn-primary" id="buscar">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado" style="display: none;">
            <div class="botones">
                <button type="button" class="btn btn-success" id="aprobarSolicitud">
                    <span class="glyphicon glyphicon-ok">&nbsp;</span>Aprobar Solicitud
                </button>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title" style="margin: 10px 0; color: white;">
                                <i class="fa fa-info-circle"></i> Información del Ahorro
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fa fa-calendar"></i> Fecha de Apertura de Ahorro:
                                        </label>
                                        <div class="info-value" id="fechaAperturaAhorro">-</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fa fa-credit-card"></i> Crédito:
                                        </label>
                                        <div class="info-value" id="creditoInfo">-</div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card card-stat bg-success">
                                        <div class="card-body text-center">
                                            <i class="fa fa-dollar fa-3x text-white mb-3"></i>
                                            <h5 class="text-white">Pagos del Día</h5>
                                            <h2 class="text-white" id="pagosDia">$0.00</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-stat bg-warning">
                                        <div class="card-body text-center">
                                            <i class="fa fa-minus-circle fa-3x text-white mb-3"></i>
                                            <h5 class="text-white">Retiros de Ahorro Simple</h5>
                                            <h2 class="text-white" id="retirosAhorroSimple">$0.00</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-stat bg-primary">
                                        <div class="card-body text-center">
                                            <i class="fa fa-calculator fa-3x text-white mb-3"></i>
                                            <h5 class="text-white">Total</h5>
                                            <h2 class="text-white font-weight-bold" id="totalAhorro">$0.00</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-group {
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border-left: 4px solid #007bff;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
        margin-bottom: 8px;
        display: block;
    }

    .info-label i {
        margin-right: 5px;
        color: #007bff;
    }

    .info-value {
        font-size: 18px;
        font-weight: 700;
        color: #212529;
        padding: 5px 0;
    }

    .card-stat {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 20px;
    }

    .card-stat:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }

    .card-stat .card-body {
        padding: 30px 20px;
    }

    .card-stat h5 {
        font-size: 14px;
        font-weight: 600;
        margin: 15px 0 10px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-stat h2 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    .bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
    }

    .bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    }

    .card-header.bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        border-radius: 5px 5px 0 0;
    }

    .mb-3 {
        margin-bottom: 1rem !important;
    }

    .text-white {
        color: #fff !important;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .botones {
        margin-bottom: 15px;
    }

    .resultado {
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #creditoBuscar {
        font-weight: 600;
        text-align: center;
    }

    hr {
        margin: 20px 0;
        border-top: 2px solid #e9ecef;
    }
</style>

<?= $footer; ?>