<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class LogTransaccionesAhorro
{
    public static function BindingQuery($qry, $parametros = null)
    {
        if ($parametros) {
            foreach ($parametros as $parametro => $valor)
                $qry = str_replace(":" . $parametro, "'" . $valor . "'", $qry);
        }

        return $qry;
    }

    public static function LogTransaccion($datos)
    {
        $qry = <<<sql
        INSERT INTO LOG_TRANSACCIONES_AHORRO (
            ID_TRANSACCION,
            FECHA_TRANSACCION,
            QUERY_TRANSACCION,
            SUCURSAL,
            USUARIO,
            CONTRATO,
            MODULO,
            TIPO
        )
        VALUES (
            (SELECT NVL(MAX(TO_NUMBER(ID_TRANSACCION)),0) FROM LOG_TRANSACCIONES_AHORRO) + 1,
            SYSDATE,
            :query,
            :sucursal,
            :usuario,
            :contrato,
            :modulo,
            :tipo_transaccion
        )
        sql;

        $parametros = [
            'query' => self::BindingQuery($datos['query'], $datos['parametros']),
            'sucursal' => $datos['sucursal'],
            'usuario' => $datos['usuario'],
            'contrato' => $datos['contrato'],
            'modulo' => $datos['modulo'],
            'tipo_transaccion' => $datos['tipo']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $parametros);
            return [$qry, $parametros];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public static function LogTransacciones($qyrs, $parametros, $sucursal, $usuario, $contrato, $tipo)
    {
        $tmp = [];
        foreach ($qyrs as $qry => $q) {
            $log['query'] = $q;
            $log['parametros'] = $parametros[$qry];
            $log['sucursal'] = $sucursal;
            $log['usuario'] = $usuario;
            $log['contrato'] = $contrato;
            $log['modulo'] = debug_backtrace()[1]['function'];
            $log['tipo'] = $tipo;
            $tmp[] = self::LogTransaccion($log);
        }
        return $tmp;
    }
}
