<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;

class AhorroSimple
{

    public static function ConsultarPagosFechaSucursal($cdgns)
    {

        $query = <<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO) AS NOMBRE_SUCURSAL,
        PAGOSDIA.SECUENCIA,
        PAGOSDIA.FECHA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.NOMBRE,
        PAGOSDIA.CICLO,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.TIPO AS TIP,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        TO_CHAR(PAGOSDIA.FREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO
    FROM
        PAGOSDIA, NS, CO, RG
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
		AND PAGOSDIA.TIPO IN ('B', 'F')
        AND PAGOSDIA.CDGNS = $cdgns
    ORDER BY
        FREGISTRO DESC, SECUENCIA
sql;
        $mysqli = new Database();

        return $mysqli->queryAll($query);
    }

}
