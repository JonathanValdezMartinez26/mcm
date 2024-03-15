<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <br>
            <div class="col-md-4">
                <div class="panel panel-body" style="margin-bottom: 0px;">
                    <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                        <a class="navbar-brand"><span><i class="fa fa-smile-o"></i></span> Mi espacio</a>
                        &nbsp;&nbsp;
                    </div>

                    <div class="card col-md-12">
                        <div class="row">
                            <br>
                            <br>
                            <div class="row">
                                <div class="col-md-4 imagen" style="margin-left: 59px; margin-right: 48px; border: 1px solid #dfdfdf; border-radius: 10px;" data-toggle="modal" data-target="#modal_agregar_pago">
                                    <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 3px;">
                                        <img  src="https://cdn-icons-png.flaticon.com/512/651/651183.png" style="border-radius: 3px;" width="110" height="110" alt="" title="">
                                    </div>
                                    <br>
                                    <p style="font-size: 14px"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cobrar Ahorro</b></p>
                                </div>
                                <div class="col-md-4 imagen" style=" margin-right: 60px; border: 1px solid #dfdfdf; border-radius: 10px;" data-toggle="modal" data-target="#modal_agregar_pago">
                                    <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 3px;">
                                        <img  src="https://cdn-icons-png.flaticon.com/512/3029/3029259.png" style="border-radius: 3px;" width="110" height="110" alt="" title="">
                                    </div>
                                    <br>
                                    <p style="font-size: 14px"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nueva Inversión </b></p>
                                </div>


                            </div>
                            <br>
                            <br>
                            <div class="row">
                                <div class="col-md-4 imagen" style="margin-left: 59px; margin-right: 48px; border: 1px solid #dfdfdf; border-radius: 10px;" data-toggle="modal" data-target="#modal_agregar_pago">
                                    <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 3px;">
                                        <img  src="https://cdn-icons-png.flaticon.com/512/3286/3286186.png" style="border-radius: 3px;" width="110" height="110" alt="" title="">
                                    </div>
                                    <br>
                                    <p style="font-size: 14px"><b>&nbsp;&nbsp;Estados de cuenta </b></p>
                                </div>

                                <div class="col-md-4 imagen" style=" margin-right: 60px; border: 1px solid #dfdfdf; border-radius: 10px;" data-toggle="modal" data-target="#modal_agregar_pago">
                                    <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 3px;">
                                        <img  src="https://cdn-icons-png.flaticon.com/512/6195/6195266.png" style="border-radius: 3px;" width="110" height="110" alt="" title="">
                                    </div>
                                    <br>
                                    <p style="font-size: 14px"><b>&nbsp;&nbsp;Mis saldos del día </b></p>
                                </div>


                            </div>

                            <br>
                            <br>
                            <div class="row">
                                <div class="col-md-4 imagen" style="margin-left: 59px; margin-right: 48px; border: 1px solid #dfdfdf; border-radius: 10px;" data-toggle="modal" data-target="#modal_agregar_pago">
                                    <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 3px;">
                                        <img  src="https://cdn-icons-png.flaticon.com/512/3222/3222637.png" style="border-radius: 3px;" width="110" height="110" alt="" title="">
                                    </div>
                                    <br>
                                    <p style="font-size: 14px"><b>&nbsp;&nbsp;Solicitud de retiro</b></p>
                                </div>
                                <div class="col-md-4 imagen" style=" margin-right: 60px; border: 1px solid #dfdfdf; border-radius: 10px;" data-toggle="modal" data-target="#modal_agregar_pago">
                                    <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 3px;">
                                        <img  src="https://cdn-icons-png.flaticon.com/512/14126/14126162.png" style="border-radius: 3px;" width="110" height="110" alt="" title="">
                                    </div>
                                    <br>
                                    <p style="font-size: 14px"><b>&nbsp;&nbsp;Reimprimir Tickets </b></p>
                                </div>

                            </div>
                            <br>
                            <br>



                        </div>

                    </div>
                </div>
            </div>

            <form id="registroInicialAhorro" name="registroInicialAhorro">
                <div class="col-md-8">
                    <div class="panel panel-body" style="margin-bottom: 0px;">
                        <nav class="navbar navbar-date-picker">
                            <div class="container-fluid">

                                <ul class="nav navbar-nav">

                                    <li class="active"><a href="#">Ahorro Cuenta Corriente</a></li>
                                    <li><a href="#">Ahorro Cuenta Peques</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </form>
    </div>
</div>

<style>

    .imagen
    {
        transform: scale(var(--escala, 1));
        transition: transform 0.25s;
    }

    .imagen:hover
    {
        --escala: 1.2;
        cursor:pointer;
    }

</style>


<?php echo $footer; ?>