<?php echo $header; ?>

<div class="right_col" style="padding-top: 210px;" style="margin-left: 340px; ">
    <div class="row">
        <div class="col-md-7" style="margin-left: 290px; border: 1px solid #dfdfdf; border-radius: 30px;">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <p style=" font-size: 35px;">Calcula el rendimiento de tus clientes</p>
            </div>

        </div>
    </div>
    <br>
    <br>
    <br>

    <div class="row">
        <div class="col-md-2 imagen" style="margin-left: 290px; border: 1px solid #dfdfdf; border-radius: 30px;" data-toggle="modal" data-target="#modal_agregar_pago">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <img src="https://cdn-icons-png.flaticon.com/512/3050/3050243.png" width="220" height="220" alt="" title="">
            </div>
            <br>
            <p style="font-size: 14px"><b>Un centavo bien ahorrado, es un centavo bien ganado.</b></p>

            <br>
        </div>

        <div class="col-md-2 imagen" style="margin-left: 70px; border: 1px solid #dfdfdf; border-radius: 30px;" data-toggle="modal" data-target="#modal_agregar_pago">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <img  src="https://cdn-icons-png.flaticon.com/512/14991/14991719.png" style="border-radius: 30px;" width="220" height="220" alt="" title="">
            </div>
            <br>
            <p style="font-size: 14px"><b>Inversion de ahorros: Invierte en tu futuro.  </b></p>
        </div>

        <div class="col-md-2 imagen" style="margin-left: 70px; border: 1px solid #dfdfdf; border-radius: 30px;" data-toggle="modal" data-target="#modal_agregar_pago">
            <div class="panel panel-body" style="margin-bottom: 0px;  border-radius: 30px;">
                <img src="   https://cdn-icons-png.flaticon.com/512/2880/2880483.png " width="220" height="220" alt="" title="" class="img-small">
            </div>
            <br>
            <p style="font-size: 14px"><b>El ahorro para los más peques del hogar.</b></p>
        </div>
    </div>



</div>



<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="padding-top: 300px; padding-left: 100px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<style>

    .imagen{
        transform: scale(var(--escala, 1));
        transition: transform 0.25s;
    }
    .imagen:hover{
        --escala: 1.2;
        cursor:pointer;
    }

</style>

<?php echo $footer; ?>

