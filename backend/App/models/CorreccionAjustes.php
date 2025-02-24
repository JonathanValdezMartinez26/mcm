<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class CorreccionAjustes extends Model
{
    public static function GetRazones()
    {
        $qry = <<<SQL
            SELECT
                CMA.CODIGO
                , CMA.DESCRIPCION
            FROM
                ESIACOM.CAT_MOVS_AJUSTE CMA
            ORDER BY
                CMA.DESCRIPCION
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Razones obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener razones', null, $e->getMessage());
        }
    }

    public static function GetAjustes($datos)
    {
        $qry = <<<SQL
            SELECT 
                MPR.CDGNS AS CREDITO
                , MPR.CICLO
                , MPR.PERIODO
                , MPR.SECUENCIA
                , MPR.RAZON
                , CMA.DESCRIPCION AS RAZON_DESC
                , MPR.OBSERVACIONES
                , TO_CHAR(MPR.FECHA, 'DD/MM/YYYY') AS FECHA
                , MP.CANTIDAD
                , MP.REFERENCIA
            FROM
                MPR
                LEFT JOIN MP ON MPR.CDGNS = MP.CDGNS AND MPR.CICLO = MP.CICLO AND MPR.PERIODO = MP.PERIODO AND MPR.SECUENCIA = MP.SECUENCIA
                LEFT JOIN CAT_MOVS_AJUSTE CMA ON CMA.CODIGO = MPR.RAZON
            WHERE
                MPR.CDGNS = :credito 
                AND MPR.CICLO = :ciclo
        SQL;

        $prm = [
            'credito' => $datos['credito'],
            'ciclo' => $datos['ciclo']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Refinanciamientos obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener refinanciamientos', null, $e->getMessage());
        }
    }

    public static function ActualizaRazon($datos)
    {
        $qry = <<<SQL
            UPDATE
                MPR
            SET
                RAZON = :razon
            WHERE
                CDGNS = :credito
                AND CICLO = :ciclo
        SQL;

        $prm = [
            'credito' => $datos['credito'],
            'ciclo' => $datos['ciclo'],
            'razon' => $datos['razon'],
        ];

        if ($datos['periodos'] && count($datos['periodos']) > 0) $qry .= ' AND PERIODO IN (' . implode(',', $datos['periodos']) . ')';
        if ($datos['secuencias'] && count($datos['secuencias']) > 0) $qry .= ' AND SECUENCIA IN (' . implode(',', $datos['secuencias']) . ')';

        try {
            $db = new Database();
            $db->insertar($qry, $prm);
            return self::Responde(true, 'Razón actualizada', [$qry, $prm], $datos);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al actualizar la razón', null, $e->getMessage());
        }
    }
}
