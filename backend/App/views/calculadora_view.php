<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider</title>

    <!-- Google-Fonts-Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">


    <!-- noUiSlider -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.0/nouislider.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.0/nouislider.min.js"></script>


    <!-- wNumb.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.2.0/wNumb.min.js"></script>
    <!--     <script src="js/wnumb.min.js"></script> -->

    <!-- bootstrap 5.0 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Custom styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <!--     <link rel="stylesheet" href="css/style.css"> -->

    <style>

    </style>
</head>
<body>
<div class="container">
    <div class="card overflow-visible mt-4">
        <div class="card-header cabecera p-3">
            <h1 class="card-title text-white text-center ">¡Calcula tu pr&eacute;stamo aqu&iacute;!</h1>
        </div>
        <div class="card-body m-4">
            <div class="card-subtitle mb-4">
                <p><strong>Quiero pagar:</strong></p>
            </div>
            <!-- form -->
            <form action="" method="get">

                <!-- noUiSlider -->
                <!-- Monto -->
                <div class="form-group mt-4">
                    <div class="slider-round" id="slider-round-1"></div>

                    <div class="card-text d-flex justify-content-between mt-3 mb-4">
                        <p class="">$3,000.00</p>
                        <p class="m-4">$15,000.00</p>
                    </div>
                </div>
                <!--  -->
                <!-- Plazo -->
                <div class="form-group mt-4">
                    <div class="slider-round" id="slider-round-2"></div>

                    <div class="card-text d-flex justify-content-between mt-3">
                        <p class="">1 Mes</p>
                        <p class="mb-4">12 Meses</p>
                    </div>
                </div>
                <!--  -->

                <div class="card-text d-flex justify-content-between mt-3 fw-bold" id="calculoResultado">
                    <p id="pagoFrecuencia">Pago Quincenal</p>
                    <p id="pagoCuota">RD$1,190.00</p>
                </div>
                <div class="card-text d-flex justify-content-between mt-3 fw-bold" id="">
                    <p id="frecuenciaParaPagar">Quincenas para pagar</p>
                    <p id="plazoParaPagar">12 Quincenas</p>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <input class="btn btn-primary w-100" type="button" value="¡Quiero este préstamo!">
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>


<script>

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>


<style>
    html{
        font-family: Inter, Ubuntu, 'Open Sans', sans-serif;
        font-weight: 400;
        font-size: 16px;
    }
    body {
        max-width: 580px;

    }
    .cabecera {
        background-color: #004AA1 !important;
        padding: 2rem;
        box-shadow: 0px 3px 5px 0px rgba(135,135,135,0.5);

    }
    .header h1 {
        color: white;
    }

    /* Estilos del slider deslizante */
    .slider-round {
        height: .75rem;
    }

    .slider-round .noUi-target {
        border-radius: 12px !important;
        border: 0 !important;
    }

    .slider-round .noUi-connect {
        background: #008FD5;
        border-style: none;
        border-radius: .75rem;
    }
    .slider-round .noUi-connects {
        background: #EFF2F6;
        border-radius: .75rem;
        overflow: hidden;
        z-index: 0;
    }
    .slider-round .noUi-touch-area {
        background: #008FD5;
        border-radius: 12px;
    }
    .slider-round .noUi-touch-area:hover {
        background: #006192;
    }

    .slider-round .noUi-handle {
        height: 1.5rem;
        width: 1.5rem;
        content: "";
        top: -0.416rem;
        /* right: -12px;  */
        /* right is the half of the width*/
        border: solid;
        border-color: #ffffff;
        border-width: .25rem;
        border-radius: .75rem;
        box-shadow: 0px 2px 3px 0px rgba(135,135,135,0.5);
    }
    .slider-round .noUi-handle:active {
        transform: scale(.9);
    }
    .slider-round .noUi-tooltip {
        /* top: -2rem; */
        /* bottom: initial; */
        margin-top: .25rem;
        margin-bottom: .25rem;
        /* border: none; */
        /* background-color: transparent; */
        border-radius: .5rem;
        border-color: rgba(0, 143, 213, .5) ;
        box-shadow: 0px 3px 5px 0px rgba(135,135,135,0.5);
        /* opacity: 1;
        transition: opacity 1s; */

    }
    .slider-round .noUi-tooltip:hover {
        border-color: rgba(0, 143, 213, 1) ;

    }
    .slider-round .noUi-tooltip::after {
        content: " ";
        position: absolute;
        top: 100%; /* parte inferior del tooltip */
        left: 50%;
        margin-left: -.375rem;
        border-width: .375rem;
        border-style: solid;
        border-color: rgba(0, 143, 213, .8) transparent transparent transparent;
    }

    .slider-round .noUi-handle-lower::before {
        display: none!important;
    }
    .slider-round .noUi-handle-lower::after {
        display: none!important;
    }


</style>

<script>
    window.onload = function() {

        // Scripts de los Sliders
        const slider1 = document.getElementById('slider-round-1');
        const slider2 = document.getElementById('slider-round-2');

        // const calculoResultado = document.getElementById('calculoResultado');
        const frecuenciaParaPagar = document.getElementById('frecuenciaParaPagar');
        const plazoParaPagar = document.getElementById('plazoParaPagar');
        const pagoFrecuencia = document.getElementById('pagoFrecuencia');
        const pagoCuota = document.getElementById('pagoCuota');

        const checkMensual = document.getElementById('formCheckMensual');
        const checkQuincenal = document.getElementById('formCheckQuincenal');



        const gastosDeCierre = 500;
        const tasaMensual = 0.06;
        const tasaQuincenal = 0.03;

        const currency = function(number){return new Intl.NumberFormat('es-DO', {style: 'currency',currency: 'DOP', minimumFractionDigits: 2}).format(number)}; // Funcion que sirve para dar formato de moneda local

        let prestamo;
        let interes;
        let monto;
        let plazo;
        let tasa;
        let frecuencia;
        let frecuenciaLiteral;
        let capitalCuota;
        let montoCuota;

        // Slider 1
        noUiSlider.create(slider1, {
            start: [10000],
            range: {
                'min': 1000,
                'max': 15000
            },
            step: 1000,
            padding: [2000, 0],
            connect: 'lower',
            tooltips: wNumb({
                mark: '.',
                thousand: ',',
                decimals: 2,
                prefix: 'Ahorrara $'
            })
        });

        // Slider 2
        noUiSlider.create(slider2, {
            range: {
                'min': 1,
                'max': 12
            },
            start: [12],
            step: 1,
            connect: 'lower',
            tooltips: wNumb({
                decimals: 0,
                suffix: ' Quincenas',
            })
        });

        // Escuchar el evento 'change' para ver si el valor del slider2 es igual a 1 para cambiar el sufijo de plurar a singular y viceversa
        // slider2.noUiSlider.on('change.one', actualizarTooltip);

        // function actualizarTooltip () {
        //    value = slider2.noUiSlider.get();
        //    if ( value == 1 ){ slider2.noUiSlider.updateOptions({
        //             tooltips: wNumb({decimals: 0, suffix: ' Mes'})
        //             });
        //     } else {
        //         slider2.noUiSlider.updateOptions({
        //             tooltips: wNumb({decimals: 0, suffix: ' Meses'})
        //         });
        //     }
        // }

        slider1.noUiSlider.on('change.one', calcularCuota);
        slider2.noUiSlider.on('change.one', calcularCuota);
        checkMensual.addEventListener('change', calcularCuota);
        checkQuincenal.addEventListener('change', calcularCuota);

        function calcularCuota() {
            valuePlazo = slider2.noUiSlider.get();

            monto = parseInt( slider1.noUiSlider.get());
            // console.log(monto);
            plazo = parseInt( slider2.noUiSlider.get());
            // console.log(plazo);
            prestamo =  monto + gastosDeCierre;
            // console.log(prestamo);
            tasa = ( checkMensual.checked ? tasaMensual : tasaQuincenal);

            plural = ( valuePlazo == 1 ) ?  "" : "";

            // frecuenciaLiteral = (checkMensual.checked) ? "Meses" : "Quincenas";
            frecuenciaLiteral = (checkMensual.checked) ? ( valuePlazo == 1 ) ?  " Mes" : " Meses" : ( valuePlazo == 1 ) ? " Quincena" : " Quincenas";

            // console.log(tasa);
            capitalCuota = prestamo / plazo;
            // console.log(capitalCuota);
            interes = prestamo * tasa;
            // console.log(interes);
            montoCuota = capitalCuota + interes;
            montoCuota = montoCuota.toFixed(2);
            // console.log(montoCuota);

            pagoFrecuencia.innerHTML = "Pago " + frecuencia;
            pagoCuota.innerHTML = currency(montoCuota);
            frecuenciaParaPagar.innerHTML = frecuenciaLiteral + " para pagar";
            plazoParaPagar.innerHTML = plazo + " " + frecuenciaLiteral;

            slider2.noUiSlider.updateOptions({
                tooltips: wNumb({decimals: 0, suffix: frecuenciaLiteral})
            });
            // console.log(currency(montoCuota));
            //
        }

    }
</script>