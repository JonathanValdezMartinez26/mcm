<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Radar de Cobranza - Dashboard Día</h3>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cerrarSesion()" style="display: none;" id="btnCerrarSesion">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión API
                    </button>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <div class="card-body">
                    <div class="accordion" id="accordionDias">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Login -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">
                    <i class="fa fa-lock"></i> Acceso a Radar de Cobranza
                </h5>
            </div>
            <div class="modal-body">
                <form id="loginForm">
                    <div class="form-group">
                        <label for="usuario">Usuario:</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="loginBtn">
                    <i class="fa fa-sign-in"></i> Iniciar Sesión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle del Ejecutivo -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleTitle">Detalle del Ejecutivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalDetalleSubtitle" class="text-muted mb-3"></div>
                <div id="modalDetalleContent">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }

    .card-header .btn-link {
        text-decoration: none;
        color: #333;
    }

    .card-header .btn-link:hover {
        text-decoration: none;
        color: #007bff;
    }

    .card-header .btn-link:focus {
        text-decoration: none;
        box-shadow: none;
    }

    .accordion .card {
        margin-bottom: 0.5rem;
        border: 1px solid #dee2e6;
    }

    .accordion .card-header {
        padding: 0;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .accordion .card-header .btn {
        padding: 1rem 1.25rem;
        width: 100%;
        text-align: left;
        border: none;
        border-radius: 0;
        background: none;
    }

    .accordion .card-body {
        padding: 1.25rem;
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-danger {
        background-color: #dc3545;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-secondary {
        background-color: #6c757d;
    }

    .card .card-body {
        padding: 1rem;
    }

    .card .card-title {
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .card .card-text {
        margin-bottom: 0.75rem;
    }

    #accordionDias .btn-link i {
        transition: transform 0.2s;
    }

    #accordionDias .btn-link.collapsed i {
        transform: rotate(-90deg);
    }

    #accordionDias .btn-link:not(.collapsed) i {
        transform: rotate(0deg);
    }
</style>

<?php echo $footer; ?>