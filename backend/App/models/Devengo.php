<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class Devengo
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

    public static function ConsultaExiste($noCredito, $noCiclo)
    {
        $qryValidacion = <<<sql
        SELECT
            COUNT(*) AS EXISTE
        FROM
            TBL_CIERRE_DIA
        WHERE
            CDGEM = 'EMPFIN'
            AND CDGCLNS = '$noCredito'
            AND CICLO = '$noCiclo'
            AND FECHA_LIQUIDA IS NOT NULL
        sql;

        try {
            $mysqli = Database::getInstance();
            $existe = $mysqli->queryOne($qryValidacion);

            if ($existe['EXISTE'] == 1) {
                $qryDevengo = <<<sql
                SELECT
                    TCD.NOMBRE,
                    TCD.CDGCLNS,
                    TCD.CICLO,
                    TCD.COD_SUCURSAL,
                    TCD.NOM_SUCURSAL,
                    TCD.REGION,
                    TO_CHAR(TCD.INICIO, 'YYYY-MM-DD') AS FECHA_INICIO,
                    TO_CHAR(TCD.FIN, 'YYYY-MM-DD') AS FECHA_FIN,
                    TO_CHAR(
                        CASE 
                            WHEN TCD.FIN > SYSDATE THEN SYSDATE
                            ELSE TCD.FIN
                        END
                    , 'YYYY-MM-DD') AS FECHA_FIN_CALCULO,
                    TO_CHAR(TCD.FECHA_LIQUIDA, 'YYYY-MM-DD') AS FECHA_LIQUIDACION,
                    INTERES_GLOBAL,
                    TO_NUMBER(CASE 
                                WHEN TCD.FIN > SYSDATE THEN TO_DATE(SYSDATE)
                                ELSE TCD.FIN
                            END - TCD.FECHA_LIQUIDA) AS DIAS_PENDIENTES,
                    (
                        SELECT
                            DD.DEV_DIARIO
                        FROM
                            DEVENGO_DIARIO DD
                        WHERE
                            DIAS_DEV = 1
                            AND CDGCLNS = TCD.CDGCLNS
                            AND CICLO = TCD.CICLO
                    ) AS INT_DIARIO,
                    TO_NUMBER(TCD.FIN - TCD.INICIO) AS PLAZO_DIAS,
                    (
                        SELECT
                            SUM(DD.DEV_DIARIO)
                        FROM
                            DEVENGO_DIARIO DD
                        WHERE
                            DD.CDGCLNS = TCD.CDGCLNS
                            AND DD.CICLO = TCD.CICLO
                            AND DD.FECHA_CALC <= TCD.FECHA_LIQUIDA
                    ) AS DEVENGADO,
                    (
                        SELECT UNIQUE
                            DD.DEV_DIARIO_SIN_IVA * (CASE 
                                                        WHEN TCD.FIN > SYSDATE THEN TO_DATE(SYSDATE)
                                                        ELSE TCD.FIN
                                                    END - (TCD.FECHA_LIQUIDA))
                        FROM
                            DEVENGO_DIARIO DD
                        WHERE
                            DD.CDGCLNS = TCD.CDGCLNS
                            AND DD.CICLO = TCD.CICLO
                    ) AS INT_PENDIENTE,
                    (
                        SELECT UNIQUE
                            DD.IVA_INT * (CASE 
                                            WHEN TCD.FIN > SYSDATE THEN TO_DATE(SYSDATE)
                                            ELSE TCD.FIN
                                        END - (TCD.FECHA_LIQUIDA))
                        FROM
                            DEVENGO_DIARIO DD
                        WHERE
                            DD.CDGCLNS = TCD.CDGCLNS
                            AND DD.CICLO = TCD.CICLO
                    ) AS IVA_PENDIENTE,
                    (
                        SELECT
                            MAX(DD.DIAS_DEV) + 1
                        FROM
                            DEVENGO_DIARIO DD
                        WHERE
                            DD.CDGCLNS = TCD.CDGCLNS
                            AND DD.CICLO = TCD.CICLO
                    ) AS CONSECUTIVO
                FROM
                    TBL_CIERRE_DIA TCD
                WHERE
                    TCD.CDGEM = 'EMPFIN'
                    AND TCD.CDGCLNS = '$noCredito'
                    AND TCD.CICLO = '$noCiclo'
                    AND TCD.FECHA_LIQUIDA IS NOT NULL
                sql;

                $datos = $mysqli->queryOne($qryDevengo);
                return self::Responde(true, "Crédito encontrado", $datos);
            } else {
                return self::Responde(false, "El crédito $noCredito no existe o no tiene liquidación anticipada");
            }
        } catch (\Exception $e) {
            return self::Responde(false, $e->getMessage());
        }
    }

    public static function ReactivaCredito($datos)
    {
        if (count($datos) == 0) return self::Responde(false, "No se recibió información para aplicar el reverso de refinanciamiento");

        $qryValoresDefecto = <<<sql
        SELECT
            *
        FROM
            DEVENGO_DIARIO DD
        WHERE
            DD.CDGEM = 'EMPFIN'
            AND DD.DIAS_DEV = 1
            AND DD.CDGCLNS = '{$datos[0]['credito']}'
            AND DD.CICLO = '{$datos[0]['ciclo']}'
        sql;

        try {
            $mysqli = Database::getInstance();
            $valoresDefecto = $mysqli->queryOne($qryValoresDefecto);

            if (!$valoresDefecto) return self::Responde(false, "No se encontraron valores por defecto para el crédito {$datos[0]['credito']}", $datos);

            $qryReverso = <<<sql
            INSERT INTO DEVENGO_DIARIO
                (FECHA_CALC, CDGEM, CDGCLNS, CICLO, INICIO, DEV_DIARIO, DIAS_DEV, INT_DEV, CDGPE, FREGISTRO, DEV_DIARIO_SIN_IVA, IVA_INT, PLAZO, PERIODICIDAD, PLAZO_DIAS, FIN_DEVENGO, ESTATUS, CLNS)
            VALUES
                (:fecha_calculo, :cdgem, :cdgclns, :ciclo, :inicio, :dev_diario, :consecutivo, :int_dev, :cdgpe, :fregistro, :dev_diario_sin_iva, :iva_int, :plazo, :periodicidad, :plazo_dias, :fin_devengo, :estatus, :clns)
            sql;

            $qrys = [];
            $parametros = [];
            foreach ($datos as $d => $dato) {
                $parametro = [];
                foreach ($valoresDefecto as $vd => $valorDefecto) {
                    switch ($vd) {
                        case 'FECHA_CALC':
                            $parametro['fecha_calculo'] = $dato['fecha_calculo'];
                            break;
                        case 'DIAS_DEV':
                            $parametro['consecutivo'] = $dato['consecutivo'];
                            break;
                        case 'INT_DEV':
                            $parametro['interes_devengado'] = $dato['interes_devengado'];
                            break;
                        default:
                            $parametro["$vd"] = $valorDefecto;
                    }
                }
                $qrys[] = $qryReverso;
                $parametros[] = $parametro;
            }

            return self::Responde(true, "Reverso de refinanciamiento", ["qry" => $qryReverso, "parametros" => $parametros]);
            $res = $mysqli->insertaMultiple($qrys, $parametros);
            if (!$res) return self::Responde(false, "Error al aplicar el reverso de refinanciamiento");
            return self::Responde(true, "Reverso de refinanciamiento aplicado correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, $e->getMessage());
        }
    }

    public static function ReactivarCredito($noCredito, $noCiclo)
    {
        $mysqli = Database::getInstance();

        $query = <<<sql
        SELECT * FROM TBL_CIERRE_DIA 
        WHERE CDGEM = 'EMPFIN'
        AND CDGCLNS = '$noCredito'
        AND CICLO = '$noCiclo'
        AND FECHA_LIQUIDA IS NOT NULL 
        sql;

        return $mysqli->queryOne($query);
    }
}
