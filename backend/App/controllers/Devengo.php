<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Devengo as DevengoDao;
use DateTime;

class Devengo extends Controller
{

    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function getUsuario()
    {
        return $this->__usuario;
    }

    public function index()
    {
        $extraHeader = <<<html
        <title>Devengar Crédito</title>
        <link rel="shortcut icon" href="/img/logo.png">
        html;

        $extraFooter = <<<html
        <script>
            const reactivaCredito = () => {
                const tabla = document.querySelector('#devengoPendiente')
                 
                const datos = []
                tabla.querySelectorAll('tr').forEach((tr, index) => {
                    const tds = tr.querySelectorAll('td')
                    datos.push({
                        credito: document.querySelector('#credito').innerText,
                        ciclo: document.querySelector('#ciclo').innerText,
                        consecutivo: tds[0].innerText,
                        interes_devengado: tds[1].innerText,
                        fecha_calculo: tds[2].innerText
                    })
                })
                 console.log(datos)
                $.ajax({
                    type: 'POST',
                    url: '/Devengo/ReactivaCredito/',
                    data: {d: datos},
                    success: (resultado) => {
                        console.log(resultado)
                    },
                    error: (error) => {
                        console.log("Error: " + error)
                    }
                })
            }
        </script>
        html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];

        $Administracion = DevengoDao::ConsultaExiste($credito, $ciclo);
        $Administracion = json_decode($Administracion, true);

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('credito', $credito);
        View::set('ciclo', $ciclo);

        if ($Administracion['success']) {
            $tabla = "";
            for ($i = 0; $i < $Administracion['datos']['DIAS_PENDIENTES']; $i++) {
                $tabla .= "<tr>";
                $tabla .= "<td>" . ($Administracion['datos']['CONSECUTIVO'] + $i) . "</td>";
                $tabla .= "<td>" . number_format($Administracion['datos']['INT_DIARIO'] * ($Administracion['datos']['CONSECUTIVO'] + $i), 2, '.', ',') . "</td>";
                $tabla .= "<td>" . self::ModificaFecha($Administracion['datos']['FECHA_LIQUIDACION'], $i + 1) . "</td>";
                $tabla .= "</tr>";
            }

            View::set('tabla', $tabla);
            View::set('Administracion', $Administracion['datos']);
            View::render("devengo_busqueda_all");
        } else {
            View::render("devengo_all");
        }
    }

    public function ReactivaCredito()
    {
        $datos = $_POST;
        $respuesta = DevengoDao::ReactivaCredito($datos['d']);
        echo $respuesta;
    }

    public function ModificaFecha($fecha_str, $dias, $tipo = '+')
    {
        $fecha = DateTime::createFromFormat('Y-m-d', $fecha_str);
        $fecha->modify($tipo . $dias . ' day');
        return $fecha->format('Y-m-d');
    }

    public function Calcular()
    {
        $fecha = $_POST['fecha'];
        $cdgns = $_POST['cdgns'];
        $ciclo = $_POST['ciclo'];
        $inicio = $_POST['inicio'];
        $dev_diario = $_POST['dev_diario'];
        $dias_dev = $_POST['dias_dev'];
        $int_dev = $_POST['int_dev'];
        $dev_diario_sin_iva = $_POST['dev_diario_sin_iva'];
        $iva_int = $_POST['iva_int'];
        $plazo = $_POST['plazo'];
        $plazo_dias = $_POST['plazo_dias'];
        $fin = $_POST['fin'];

        $query = "
        INSERT INTO DEVENGO_DIARIO
        (FECHA_CALC, CDGEM, CDGCLNS, CICLO, INICIO, DEV_DIARIO, DIAS_DEV, INT_DEV, CDGPE, FREGISTRO, DEV_DIARIO_SIN_IVA, IVA_INT, PLAZO, PERIODICIDAD, PLAZO_DIAS, FIN_DEVENGO, ESTATUS, CLNS)
        VALUES(TIMESTAMP '$fecha 00:00:00.000000', 'EMPFIN', '$cdgns', '$ciclo', TIMESTAMP '$inicio 00:00:00.000000', $dev_diario, $dias_dev, $int_dev, 'AMGM', TIMESTAMP '$fecha 00:00:00.000000', $dev_diario_sin_iva, $iva_int, $plazo,'S', $plazo_dias
        , TIMESTAMP '$fin 00:00:00.000000', 'RE', 'G')";
        //$devengo = DevengoDao::ProcedureGarantiasDelete($id, $secuencia);

        var_dump($query);

        //return $query;

    }
}
