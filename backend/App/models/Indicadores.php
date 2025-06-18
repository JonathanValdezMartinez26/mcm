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
                        COUNT(PD.CDGPE) AS TOTAL_INCIDENCIAS,
                        'PAGO DÍA' AS TIPO, 
                        'ACTUALIZACIÓN' AS REFERENCIA
                    FROM
                        PAGOSDIA PD
                    WHERE
                        PD.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                    GROUP BY
                        PD.CDGPE,
                        TO_CHAR(PD.FREGISTRO, 'YYYY'),
                        TO_CHAR(PD.FREGISTRO, 'MM')
                        
                 UNION 
                        
                   SELECT m.ACTUALIZARPE AS CDGPE,  
                   TO_CHAR(m.FDEPOSITO , 'YYYY') AS ANO,
                   TO_CHAR(m.FDEPOSITO , 'MM') AS MES,
                   COUNT(m.ACTUALIZARPE) AS TOTAL_INCIDENCIAS, 
                   m.TIPO , 
                   m.REFERENCIA 
                   FROM MP m
                   WHERE
                        m.FDEPOSITO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                        AND m.ACTUALIZARPE IS NOT NULL
                        AND m.TIPO != 'PD'
                    GROUP BY
                        m.ACTUALIZARPE,
                        TO_CHAR(m.FDEPOSITO, 'YYYY'),
                        TO_CHAR(m.FDEPOSITO, 'MM'),
                        m.TIPO,
                        m.REFERENCIA 
                        
                        
                 UNION       
                        
                         SELECT pgs.CDGPE,  
                   TO_CHAR(pgs.FREGISTRO  , 'YYYY') AS ANO,
                   TO_CHAR(pgs.FREGISTRO  , 'MM') AS MES,
                   COUNT(pgs.CDGPE) AS TOTAL_INCIDENCIAS, 
                   'APLICACION GARANTIA' AS TIPO, 
                   '' AS REFERENCIA
                   FROM PAG_GAR_SIM pgs
                   WHERE
                        pgs.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                        AND pgs.ESTATUS = 'CP'
                    GROUP BY
                       pgs.CDGPE,
                        TO_CHAR(pgs.FREGISTRO, 'YYYY'),
                        TO_CHAR(pgs.FREGISTRO, 'MM')
                        
                        
                ) Q1
                JOIN PE ON Q1.CDGPE = PE.CODIGO
            WHERE
                PE.ACTIVO = 'S'
                AND CODIGO IN ('MCDP', 'LVGA', 'ORHM', 'MAPH', 'PHEE')
            ORDER BY Q1.ANO DESC
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
                 Q1.FECHA AS FECHA,
                    Q1.CDGNS,
                    Q1.CICLO,
                    Q1.MONTO,
                    Q1.REFERENCIA AS DESCRIPCION,
                    Q1.TIPO,
                    Q1.REGION,
                    Q1.SUCURSAL
            FROM
                (
                    SELECT
                    	PD.CDGNS,
                    	PD.CICLO,
                    	PD.MONTO,
                        PD.CDGPE,
                        TO_CHAR(PD.FREGISTRO, 'YYYY') AS ANO,
                        TO_CHAR(PD.FREGISTRO, 'MM') AS MES,
                        PD.FREGISTRO AS FECHA, 
                        COUNT(PD.CDGPE) AS TOTAL_INCIDENCIAS,
                         CASE
            WHEN PD.ESTATUS = 'A' AND PD.FACTUALIZA IS NULL THEN 
                'REGISTRO DE ' || 
                CASE PD.TIPO
                    WHEN 'P' THEN 'PAGO'
                    WHEN 'X' THEN 'PAGO ELECTRÓNICO'
                    WHEN 'Y' THEN 'PAGO EXCEDENTE'
                    WHEN 'M' THEN 'MULTA'
                    WHEN 'Z' THEN 'MULTA GESTORES'
                    WHEN 'L' THEN 'MULTA ELECTRÓNICA'
                    WHEN 'G' THEN 'GARANTÍA'
                    WHEN 'D' THEN 'DESCUENTO'
                    WHEN 'R' THEN 'REFINANCIAMIENTO'
                    WHEN 'H' THEN 'RECOMIENDA'
                    WHEN 'S' THEN 'SEGURO'
                    ELSE 'DESCONOCIDO'
                END
            WHEN PD.ESTATUS = 'E' THEN 
                'ELIMINACIÓN DE ' || 
                CASE PD.TIPO
                    WHEN 'P' THEN 'PAGO'
                    WHEN 'X' THEN 'PAGO ELECTRÓNICO'
                    WHEN 'Y' THEN 'PAGO EXCEDENTE'
                    WHEN 'M' THEN 'MULTA'
                    WHEN 'Z' THEN 'MULTA GESTORES'
                    WHEN 'L' THEN 'MULTA ELECTRÓNICA'
                    WHEN 'G' THEN 'GARANTÍA'
                    WHEN 'D' THEN 'DESCUENTO'
                    WHEN 'R' THEN 'REFINANCIAMIENTO'
                    WHEN 'H' THEN 'RECOMIENDA'
                    WHEN 'S' THEN 'SEGURO'
                    ELSE 'DESCONOCIDO'
                END
            WHEN PD.ESTATUS = 'A' AND PD.FACTUALIZA IS NOT NULL THEN 
                'ACTUALIZACIÓN DE ' || 
                CASE PD.TIPO
                    WHEN 'P' THEN 'PAGO'
                    WHEN 'X' THEN 'PAGO ELECTRÓNICO'
                    WHEN 'Y' THEN 'PAGO EXCEDENTE'
                    WHEN 'M' THEN 'MULTA'
                    WHEN 'Z' THEN 'MULTA GESTORES'
                    WHEN 'L' THEN 'MULTA ELECTRÓNICA'
                    WHEN 'G' THEN 'GARANTÍA'
                    WHEN 'D' THEN 'DESCUENTO'
                    WHEN 'R' THEN 'REFINANCIAMIENTO'
                    WHEN 'H' THEN 'RECOMIENDA'
                    WHEN 'S' THEN 'SEGURO'
                    ELSE 'DESCONOCIDO'
                END
            ELSE 'TIPO NO DEFINIDO'
        END AS TIPO,
        'CAJA PAGOS DÍA' AS REFERENCIA,
                        CO.NOMBRE AS SUCURSAL,
                        RG.NOMBRE AS REGION
                    FROM
                        PAGOSDIA PD
                    JOIN PRN ON PD.CDGNS = PRN.CDGNS AND PD.CICLO = PRN.CICLO
                	JOIN CO ON PRN.CDGCO = CO.CODIGO
                	JOIN RG ON CO.CDGRG = RG.CODIGO
                	
                    WHERE
                        PD.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                    GROUP BY
                    	PD.CDGNS,
                    	PD.CICLO,
                    	PD.MONTO,
                        PD.CDGPE,
                        TO_CHAR(PD.FREGISTRO, 'YYYY'),
                        TO_CHAR(PD.FREGISTRO, 'MM'),
                        PD.FREGISTRO, 
                        CO.NOMBRE ,
                        RG.NOMBRE,
                        PD.ESTATUS,
                        PD.FACTUALIZA,
                        PD.TIPO
                        
                 UNION 
                        
                   SELECT
                   m.CDGNS, 
                   m.CICLO,
                   m.CANTIDAD AS MONTO,
                   m.ACTUALIZARPE AS CDGPE,  
                   TO_CHAR(m.FDEPOSITO , 'YYYY') AS ANO,
                   TO_CHAR(m.FDEPOSITO , 'MM') AS MES,
                   m.FDEPOSITO AS FECHA, 
                   COUNT(m.ACTUALIZARPE) AS TOTAL_INCIDENCIAS, 
                   m.TIPO , 
                   m.REFERENCIA, 
                   CO.NOMBRE AS SUCURSAL,
                   RG.NOMBRE AS REGION
                   FROM MP m
                    JOIN PRN ON m.CDGNS = PRN.CDGNS AND m.CICLO = PRN.CICLO
                	JOIN CO ON PRN.CDGCO = CO.CODIGO
                	JOIN RG ON CO.CDGRG = RG.CODIGO
                   WHERE
                        m.FDEPOSITO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                        AND m.ACTUALIZARPE IS NOT NULL
                        AND m.TIPO != 'PD'
                        AND  m.CANTIDAD > 2 OR m.CANTIDAD < -100
                        AND m.REFERENCIA != 'Interés total del préstamo'
                        
                    GROUP BY
                    m.CDGNS, 
                    m.CICLO,
                    m.CANTIDAD,
                        m.ACTUALIZARPE,
                        TO_CHAR(m.FDEPOSITO, 'YYYY'),
                        TO_CHAR(m.FDEPOSITO, 'MM'),
                        m.FDEPOSITO, 
                        m.TIPO,
                        m.REFERENCIA, 
                        CO.NOMBRE ,
                        RG.NOMBRE 
                        
                        
                 UNION       
                        
                         SELECT 
                         pgs.CDGCLNS AS CDGNS,
                         pgs.CICLO, 
                         pgs.CANTIDAD AS MONTO, 
                         pgs.CDGPE,  
                   TO_CHAR(pgs.FREGISTRO  , 'YYYY') AS ANO,
                   TO_CHAR(pgs.FREGISTRO  , 'MM') AS MES,
                   pgs.FREGISTRO AS FECHA, 
                   COUNT(pgs.CDGPE) AS TOTAL_INCIDENCIAS, 
                   'APLICACION GARANTIA' AS TIPO, 
                   '' AS REFERENCIA,
                   CO.NOMBRE AS SUCURSAL, 
                   RG.NOMBRE AS REGION
                   FROM PAG_GAR_SIM pgs
                   JOIN PRN ON pgs.CDGCLNS = PRN.CDGNS AND pgs.CICLO = PRN.CICLO
                	JOIN CO ON PRN.CDGCO = CO.CODIGO
                	JOIN RG ON CO.CDGRG = RG.CODIGO
                   WHERE
                        pgs.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                        AND pgs.ESTATUS = 'CP'
                    GROUP BY
                    pgs.CDGCLNS, 
                    pgs.CICLO,
                    pgs.CANTIDAD, 
                       pgs.CDGPE,
                        TO_CHAR(pgs.FREGISTRO, 'YYYY'),
                        TO_CHAR(pgs.FREGISTRO, 'MM'),
                        pgs.FREGISTRO, 
                        CO.NOMBRE,
                        RG.NOMBRE 
                       
                        
                        
                ) Q1
                JOIN PE ON Q1.CDGPE = PE.CODIGO
            WHERE
               Q1.FECHA BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                AND Q1.CDGPE = :usuario
            ORDER BY
                Q1.FECHA DESC
        SQL;

        $qry2 = <<<SQL
            
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
