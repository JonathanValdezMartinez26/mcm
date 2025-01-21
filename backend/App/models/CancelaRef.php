<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class CancelaRef extends Model
{
    public static function GetRefinanciamientos($datos)
    {
        $qry = <<<SQL
            SELECT
                PRC.CDGCL AS CLIENTE,
                PD.NOMBRE,
                PD.CDGNS AS CREDITO,
                PD.CICLO,
                (
                    SELECT SALDOTOTALPRN(
                        'EMPFIN', 
                        PD.CDGNS, 
                        PD.CICLO, 
                        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL
                    )
                    FROM DUAL
                ) AS SALDO_TOTAL,
                PRN.SITUACION,
                TIPO_OPERACION(PD.TIPO) AS OPERACION,
                TO_CHAR(PD.FECHA, 'DD/MM/YYYY') AS FECHA,
                PD.MONTO,
                PD.CDGOCPE AS EJECUTIVO,
                PD.CDGPE AS REGISTRO,
                PD.SECUENCIA,
                PD.ESTATUS,
                (
                    SELECT
                        MAX(CICLO)
                    FROM
                        PRC
                    WHERE
                        CDGNS = PD.CDGNS
                ) AS ULTIMO_CICLO
                
            FROM
                PAGOSDIA PD
                JOIN PRC ON PRC.CDGNS = PD.CDGNS AND PRC.CICLO = PD.CICLO
                JOIN PRN ON PRN.CDGNS = PD.CDGNS AND PRN.CICLO = PD.CICLO
            WHERE
                PD.CDGNS = :credito 
                AND PD.TIPO = 'R'
            ORDER BY
                PD.FECHA DESC
        SQL;

        $prm = [
            'credito' => $datos['credito']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Refinanciamientos obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener refinanciamientos', null, $e->getMessage());
        }
    }

    public static function CancelaRefinanciamiento($datos)
    {
        try {
            $db = new Database();
            $db->AutoCommitOff();
            $db->IniciaTransaccion();

            $r = self::DesactivaRefinancieminto($db, $datos);
            if (!$r['success']) throw new \Exception(json_encode($r));

            $r = self::ActualizaSituacion($db, $datos);
            if (!$r['success']) throw new \Exception(json_encode($r));

            $r = self::ActualizaFechas($db, $datos);
            if (!$r['success']) throw new \Exception(json_encode($r));

            $r = self::InsertaDevengo($db, $datos);
            if (!$r['success']) throw new \Exception(json_encode($r));

            $db->ConfirmaTransaccion();
            return self::Responde(true, 'Refinanciamiento cancelado correctamente.');
        } catch (\Exception $e) {
            $db->CancelaTransaccion();
            return json_decode($e->getMessage());
        }
    }

    public static function DesactivaRefinancieminto($db, $datos)
    {
        $qry = <<<SQL
            UPDATE
                PAGOSDIA
            SET
                ESTATUS = 'E',
                FACTUALIZA = SYSDATE 
            WHERE
                CDGNS = :credito
                AND CICLO = :ciclo
                AND SECUENCIA = :secuencia
        SQL;

        $prm = [
            'credito' => $datos['credito'],
            'ciclo' => $datos['ciclo'],
            'secuencia' => $datos['secuencia']
        ];

        try {
            if ($db->actualizar($qry, $prm)) return self::Responde(true, 'Refinanciamiento desactivado.');
            return self::Responde(false, 'Error al desactivar refinanciamiento.');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al desactivar refinanciamiento.', null, $e->getMessage());
        }
    }

    public static function ActualizaSituacion($db, $datos)
    {
        $sp = <<<SQL
            CALL SPACTUALIZASITUACION('EMPFIN', :credito, :ciclo, 'E', :output)
        SQL;

        $prm2 = [
            'credito' => $datos['credito'],
            'ciclo' => $datos['ciclo']
        ];

        try {
            $res = $db->EjecutaSP($sp, $prm2);
            if ($res === '1 Proceso realizado exitosamente') return self::Responde(true, 'Situación actualizada.');
            return self::Responde(false, $res, null);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al actualizar situación.', null, $e->getMessage());
        }
    }

    public static function ActualizaFechas($db, $datos)
    {
        $qry = <<<SQL
            UPDATE
                TBL_CIERRE_DIA
            SET
                FECHA_LIQUIDA = NULL
            WHERE
                CDGCLNS = :credito
                AND CICLO = :ciclo
                AND FECHA_LIQUIDA IS NOT NULL
        SQL;

        $prm = [
            'credito' => $datos['credito'],
            'ciclo' => $datos['ciclo']
        ];

        try {
            if ($db->actualizar($qry, $prm)) return self::Responde(true, 'Fechas actualizadas.');
            return self::Responde(false, 'Error al actualizar fechas.');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al actualizar fechas.', null, $e->getMessage());
        }
    }

    public static function InsertaDevengo($db, $datos)
    {
        $qry = <<<SQL
            INSERT INTO
                DEVENGO_DIARIO(
                    FECHA_CALC,
                    CDGEM,
                    CDGCLNS,
                    CICLO,
                    INICIO,
                    DEV_DIARIO,
                    DIAS_DEV,
                    INT_DEV,
                    CDGPE,
                    FREGISTRO,
                    DEV_DIARIO_SIN_IVA,
                    IVA_INT,
                    PLAZO,
                    PERIODICIDAD,
                    PLAZO_DIAS,
                    FIN_DEVENGO,
                    ESTATUS,
                    CLNS
                )
            SELECT
            *
            FROM
            (
                WITH CTE_Numero AS (
                    SELECT
                        LEVEL - 1 AS NUM
                    FROM
                        DUAL CONNECT BY LEVEL - 1 <= (
                        SELECT
                            MAX(LEAST(SYSDATE -1, FIN) - (INICIO + 1))
                        FROM
                            CREDITOS_ACTIVOS
                        )
                ),
                CTE_Fechas AS (
                    SELECT
                        CA.CDGNS,
                        CA.CICLO,
                        (CA.INICIO + 1) + NUM AS FECHA,
                        CA.INICIO,
                        CA.INTERES_DIARIO,
                        CTE_Numero.NUM,
                        CA.FIN,
                        CA.PLAZO
                    FROM
                        CREDITOS_ACTIVOS CA
                        CROSS JOIN CTE_Numero
                    WHERE
                        CA.CDGNS = :credito
                        AND CICLO = :ciclo
                        AND (CA.INICIO + 1) + NUM <= LEAST(SYSDATE - 1, CA.FIN)
                )
                SELECT
                    CTE.FECHA AS FECHA_CALC,
                    'EMPFIN' AS CDGEM,
                    CTE.CDGNS AS CDGCLNS,
                    CTE.CICLO,
                    CTE.INICIO,
                    CTE.INTERES_DIARIO AS DEV_DIARIO,
                    (CTE.NUM + 1) AS DIAS_DEV,
                    (CTE.INTERES_DIARIO * (CTE.NUM + 1)) AS INT_DEV,
                    'AMGM' AS CDGPE,
                    CAST(SYSTIMESTAMP AS TIMESTAMP(3)) AS FREGISTRO,
                    ROUND(CTE.INTERES_DIARIO / 1.16, 2) AS DEV_DIARIO_SIN_IVA,
                    ROUND(CTE.INTERES_DIARIO *.16, 2) AS IVA_INT,
                    CTE.PLAZO AS PLAZO,
                    'S' AS PERIORICIDAD,
                    (CTE.PLAZO * 7) AS PLAZO_DIAS,
                    CTE.FIN AS FIN_DEVENGO,
                    'RE' AS ESTATUS,
                    'G' AS CLNS
                FROM
                    CTE_Fechas CTE
                    LEFT JOIN DEVENGO_DIARIO DD ON CTE.FECHA = DD.FECHA_CALC
                    AND CTE.CDGNS = DD.CDGCLNS
                    AND CTE.CICLO = DD.CICLO
                WHERE
                    DD.FECHA_CALC IS NULL
            )
        SQL;

        $prm = [
            'credito' => $datos['credito'],
            'ciclo' => $datos['ciclo']
        ];

        try {
            $res = $db->insertar($qry, $prm);
            return self::Responde(true, 'Devengo insertado.');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al insertar devengo.', null, $e->getMessage());
        }
    }
}
