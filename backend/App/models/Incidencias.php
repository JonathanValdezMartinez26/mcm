<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;

class Incidencias{

    public static function ProcesoCancelarRefinanciamiento($cdgns){

        $fechaActual = date("Y-m-d");
        $dia_cierre = date("Y-m-d",strtotime($fechaActual."- 1 days"));


        ///Obtener
        $query =<<<sql
        SELECT COUNT(*) AS EXISTE FROM SN WHERE SITUACION !='A' AND CDGNS = '$cdgns'
sql;

        $query_calcular_cierre =<<<sql
        SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'
sql;

        $query_ultimo_refinanciamiento_pagos =<<<sql
        SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'
sql;

        $obtener_fecha_cierre_credito =<<<sql
        SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'
sql;

        $query_ultimo_refinanciamiento_pagos =<<<sql
        SELECT COUNT(*) AS DEVENGO_DIARIO FROM DEVENGO_DIARIO WHERE FECHA_CALC = TIMESTAMP '$dia_cierre 00:00:00.000000'
sql;

        try {
            $mysqli = Database::getInstance();

            $situacion =  $mysqli->queryOne($query);
            $situacion_cierre =  $mysqli->queryOne($query_calcular_cierre);
            $ultimo_refinanciamiento_pagos = $mysqli->queryOne($query_ultimo_refinanciamiento_pagos);


            return [$situacion, $situacion_cierre, $ultimo_refinanciamiento_pagos];

        } catch (Exception $e) {
            return "";
        }
    }

}
