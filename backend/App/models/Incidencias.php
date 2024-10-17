<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class Incidencias
{

    public static function ProcesoCancelarRefinanciamiento($cdgns)
    {
        $fechaActual = date("Y-m-d");
        $dia_cierre = date("Y-m-d", strtotime($fechaActual . "- 1 days"));


        ///Obtener
        $query = "SELECT COUNT(*) AS EXISTE FROM SN WHERE SITUACION != 'A' AND CDGNS = '$cdgns'";

        $query_calcular_cierre = "SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'";

        $query_ultimo_refinanciamiento_pagos = "SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'";

        $obtener_fecha_cierre_credito = "SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'";

        $query_ultimo_refinanciamiento_pagos = "SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'";

        try {
            $mysqli = new Database();

            $situacion =  $mysqli->queryOne($query);
            $situacion_cierre =  $mysqli->queryOne($query_calcular_cierre);
            $ultimo_refinanciamiento_pagos = $mysqli->queryOne($query_ultimo_refinanciamiento_pagos);


            return [$situacion, $situacion_cierre, $ultimo_refinanciamiento_pagos];
        } catch (\Exception $e) {
            return "";
        }
    }
}
