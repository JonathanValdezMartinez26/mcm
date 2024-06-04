<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database_cultiva;


class ConsultaUdiDolar{


    public static function AddUdiDolar($fecha, $dolar, $udi){

        $mysqli = Database_cultiva::getInstance(1);


        if($dolar != 0)
        {
            $query_dolar=<<<sql
        INSERT INTO ESIACOM.UNIDAD
        (CODIGO, DESCRIPCION, VALOR, FECHA_CALC, ABREV, CDGEM)
        VALUES('USD', 'MX: $dolar MXN = 1 USD $fecha BM Para pagos', $dolar, TIMESTAMP '$fecha 00:00:00.000000', 'USD', 'EMPFIN')
             
sql;
            $ret_dolar = $mysqli->insert($query_dolar);
        }
        else
        {
            $ret_dolar = '';
        }

        if($udi != 0)
        {
            $query_udi=<<<sql
        INSERT INTO ESIACOM.UNIDAD
        (CODIGO, DESCRIPCION, VALOR, FECHA_CALC, ABREV, CDGEM)
        VALUES('UDI', 'MX: $udi UDIS $fecha BM', $udi, TIMESTAMP '$fecha 00:00:00.000000', 'UDI', 'EMPFIN')
             
sql;
            $ret_udi = $mysqli->insert($query_udi);
        }
        else
        {
            $ret_udi = '';
        }


        return [$ret_dolar, $ret_udi];
    }

}
