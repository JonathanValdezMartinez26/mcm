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
                Q1.CDGPE,
                Q1.ANO,
                Q1.MES,
                Q1.TOTAL_INCIDENCIAS,
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE,
                TO_CHAR(TO_DATE(Q1.MES, 'MM'), 'Month') AS MES_LETRA
            FROM
                (
                    SELECT
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
                ) Q1
                JOIN PE ON Q1.CDGPE = PE.CODIGO
            WHERE
                PE.ACTIVO = 'S'
                AND CODIGO IN ('MCDP', 'LVGA', 'ORHM', 'MAPH', 'PHEE')
            UNION ALL
            SELECT
                'AJUSTES' AS CDGPE,
                TO_CHAR(MPR.FREGISTRO, 'YYYY') AS ANO,
                TO_CHAR(MPR.FREGISTRO, 'MM') AS MES,
                COUNT(*) AS TOTAL_INCIDENCIAS,
                'SISTEMA' AS NOMBRE,
                TO_CHAR(TO_DATE(TO_CHAR(MPR.FREGISTRO, 'MM'), 'MM'), 'Month') AS MES_LETRA
            FROM
                MPR
            WHERE
                MPR.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
            GROUP BY
                TO_CHAR(MPR.FREGISTRO, 'YYYY'),
                TO_CHAR(MPR.FREGISTRO, 'MM')
            ORDER BY
                ANO DESC,
                MES DESC
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
        $qry1 = <<<SQL
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

        $qry2 = <<<SQL
            SELECT
                TO_CHAR(MPR.FREGISTRO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA,
                MPR.CDGNS,
                MPR.CICLO,
                0,
                'AJUSTES MANUALES' AS DESCRIPCION,
                CMA.DESCRIPCION AS TIPO,
                RG.CODIGO || ' - ' || RG.NOMBRE AS REGION,
                CO.CODIGO || ' - ' || CO.NOMBRE AS SUCURSAL
            FROM
                MPR
                JOIN CAT_MOVS_AJUSTE CMA ON MPR.RAZON = CMA.CODIGO
                JOIN PRN ON MPR.CDGNS = PRN.CDGNS AND MPR.CICLO = PRN.CICLO
                JOIN CO ON PRN.CDGCO = CO.CODIGO
                JOIN RG ON CO.CDGRG = RG.CODIGO
            WHERE
                MPR.FREGISTRO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
            ORDER BY
                MPR.FREGISTRO DESC
        SQL;

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        if ($datos['usuario'] === 'AJUSTES') {
            $qry = $qry2;
        } else {
            $qry = $qry1;
            $prm['usuario'] = $datos['usuario'];
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Incidencias de usuario obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener incidencias de usuario', null, $e->getMessage());
        }
    }
}
