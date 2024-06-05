<?php

namespace App\models;

include 'C:/xampp/htdocs/mcm/backend/Core/Database_cultiva.php';

use \Core\Database_cultiva;
use Exception;

class ConsultaUdiDolar
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = [
            "success" => $respuesta,
            "mensaje" => $mensaje
        ];

        if ($datos !== null) $res['datos'] = $datos;
        if ($error !== null) $res['error'] = $error;

        return $res;
    }

    public static function AddUdiDolar($fecha, $dolar, $udi)
    {
        $mysqli = Database_cultiva::getInstance();
        $ret_dolar = '';
        $ret_udi = '';

        if ($dolar != 0) {
            $query_dolar = <<<sql
        INSERT INTO ESIACOM.UNIDAD
        (CODIGO, DESCRIPCION, VALOR, FECHA_CALC, ABREV, CDGEM)
        VALUES('USD', 'MX: $dolar MXN = 1 USD $fecha BM Para pagos', $dolar, TIMESTAMP '$fecha 00:00:00.000000', 'USD', 'EMPFIN')
             
sql;
            try {
                $res = $mysqli->insert($query_dolar);
                $ret_dolar = self::Responde(true, "Dolar registrado correctamente.", ['query' => $query_dolar, 'res' => $res]);
            } catch (Exception $e) {
                $ret_dolar = self::Responde(false, null, $e->getMessage());
            }
        }

        if ($udi != 0) {
            $query_udi = <<<sql
        INSERT INTO ESIACOM.UNIDAD
        (CODIGO, DESCRIPCION, VALOR, FECHA_CALC, ABREV, CDGEM)
        VALUES('UDI', 'MX: $udi UDIS $fecha BM', $udi, TIMESTAMP '$fecha 00:00:00.000000', 'UDI', 'EMPFIN')
             
sql;

            try {
                $res = $mysqli->insert($query_udi);
                $ret_udi = self::Responde(true, "UDI registrada correctamente.", ['query' => $query_udi, 'res' => $res]);
            } catch (Exception $e) {
                $ret_udi = self::Responde(false, null, $e->getMessage());
            }
        }

        return [$ret_dolar, $ret_udi];
    }
}
