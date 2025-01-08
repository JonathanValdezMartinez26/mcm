<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Model;
use Core\Database;

class Indicadores extends Model
{
    public static function GetIncidenciasUsuarios()
    {
        $qry = <<<SQL
            SELECT
                Q1.*,
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE,
                TO_CHAR(TO_DATE(Q1.MES, 'MM'), 'Month') AS MES_LETRA
            FROM
                (SELECT
                    PD.CDGPE,
                    TO_CHAR(PD.FREGISTRO, 'YYYY') AS ANO,
                    TO_CHAR(PD.FREGISTRO, 'MM') AS MES,
                    COUNT(PD.CDGPE) AS TOTAL_INCIDENCIAS
                FROM
                    PAGOSDIA PD
                WHERE
                    PD.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                GROUP BY
                    PD.CDGPE,
                    TO_CHAR(PD.FREGISTRO, 'YYYY'),
                    TO_CHAR(PD.FREGISTRO, 'MM')
                ORDER BY
                    TO_CHAR(PD.FREGISTRO, 'YYYY') DESC,
                    TO_CHAR(PD.FREGISTRO, 'MM') DESC) Q1
                JOIN PE ON Q1.CDGPE = PE.CODIGO
            WHERE
                PE.ACTIVO = 'S'
                AND CODIGO IN ('MCDP', 'LVGA', 'ORHM', 'MAPH', 'PHEE')
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Incidencias de usuario obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener incidencias de usuario', null, $e->getMessage());
        }
    }

    public static function GetIncidenciasUsuario($datos)
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(PD.FREGISTRO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA,
                PD.CDGNS,
                PD.CICLO,
                PD.MONTO,
                CASE 
                    WHEN PD.ESTATUS = 'A' THEN 'MODIFICADO'
                    WHEN PD.ESTATUS = 'E' THEN 'ELIMINADO'
                    ELSE 'DESCONOCIDO'
                END AS DESCRIPCION,
                TIPO_OPERACION(PD.TIPO) AS TIPO,
                RG.CODIGO || ' - ' || RG.NOMBRE AS REGION,
                CO.CODIGO || ' - ' || CO.NOMBRE AS SUCURSAL
            FROM
                PAGOSDIA PD
                JOIN PRN ON PD.CDGNS = PRN.CDGNS AND PD.CICLO = PRN.CICLO
                JOIN CO ON PRN.CDGCO = CO.CODIGO
                JOIN RG ON CO.CDGRG = RG.CODIGO
            WHERE
                PD.FREGISTRO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                AND PD.CDGPE = :usuario
            ORDER BY
                PD.FREGISTRO DESC
        SQL;

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF'],
            'usuario' => $datos['usuario']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Incidencias de usuario obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener incidencias de usuario', null, $e->getMessage());
        }
    }
}
