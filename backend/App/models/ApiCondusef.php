<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database_cultiva;
use Exception;

class ApiCondusef
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = array(
            "success" => $respuesta,
            "mensaje" => $mensaje
        );

        if ($datos != null) $res['datos'] = $datos;
        if ($error != null) $res['error'] = $error;

        return json_encode($res);
    }

    public static function GetProductos()
    {
        $query = <<<sql
        SELECT
            CODIGO,
            SUBPRODUCTO as producto
        FROM
            CAT_PROD_SERV_RED
sql;

        try {
            $mysqli = Database_cultiva::getInstance();
            $resultado = $mysqli->queryAll($query);
            if ($resultado == null) return array();
            return $resultado;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetCausas()
    {
        $query = <<<sql
        SELECT
            CODIGO,
            DESCRIPCION
        FROM
            CAT_CAUSA_QUEJA_RED
sql;

        try {
            $mysqli = Database_cultiva::getInstance();
            $resultado = $mysqli->queryAll($query);
            if ($resultado == null) return array();
            return $resultado;
        } catch (Exception $e) {
            return array();
        }
    }
}
