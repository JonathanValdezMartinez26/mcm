<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;

class AhorroSimple
{

    public static function ConsultarPagosAdministracion($noCredito, $hora)
    {

        $query = <<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO),
        PAGOSDIA.SECUENCIA,
        TO_CHAR(PAGOSDIA.FECHA, 'YYYY-MM-DD' ) AS FECHA,
        TO_CHAR(PAGOSDIA.FECHA, 'DD/MM/YYYY' ) AS FECHA_TABLA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.NOMBRE,
        PAGOSDIA.CICLO,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.TIPO AS TIP,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        (PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' ||PE.PRIMAPE || ' ' ||PE.SEGAPE) AS NOMBRE_CDGPE,
        PAGOSDIA.FREGISTRO,
        ------PAGOSDIA.FIDENTIFICAPP,
        TRUNC(FECHA) AS DE,
        TRUNC(FECHA) + 1 + 10/24 +  10/1440 AS HASTA,
        CASE
            WHEN SYSDATE 
            BETWEEN (FECHA) 
            AND TO_DATE((TO_CHAR((TRUNC(FECHA) + 1),  'YYYY-MM-DD') || ' ' || '$hora'), 'YYYY-MM-DD HH24:MI:SS')
            THEN 'SI'
        Else 'NO'
        END AS DESIGNATION,
        CASE
        WHEN SYSDATE BETWEEN (FECHA) AND (TRUNC(FECHA) + 2 + 11/24 + 0/1440) THEN 'SI'
        Else 'NO'
        END AS DESIGNATION_ADMIN
    FROM
        PAGOSDIA, NS, CO, RG, PE    
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND PAGOSDIA.CDGNS = '$noCredito'
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
        AND PE.CODIGO = PAGOSDIA.CDGPE
        AND PE.CDGEM = 'EMPFIN'
    ORDER BY
        FREGISTRO DESC, SECUENCIA
sql;

        // var_dump($query);
        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosApp()
    {

        $query = <<<SQL
        SELECT
            (COD_SUC || COUNT(NOMBRE) || COMP_BARRA || CAST(SUM(MONTO) AS INTEGER)) AS BARRAS, COD_SUC, SUCURSAL, COUNT(NOMBRE) AS NUM_PAGOS, NOMBRE, FECHA_D, FECHA, 
        FECHA_REGISTRO, CDGOCPE,
        SUM(PAGOS) AS TOTAL_PAGOS, 
        SUM(MULTA) AS TOTAL_MULTA, 
        SUM(REFINANCIAMIENTO) AS TOTAL_REFINANCIAMIENTO, 
        SUM(DESCUENTO) AS TOTAL_DESCUENTO, 
        SUM(GARANTIA) AS GARANTIA, 
        SUM(MONTO) AS MONTO_TOTAL
        FROM
        (
        SELECT TO_CHAR(FECHA, 'DDMMYYYY' ) AS COMP_BARRA ,CO.CODIGO AS COD_SUC, CO.NOMBRE AS SUCURSAL, CORTECAJA_PAGOSDIA.EJECUTIVO AS NOMBRE, 
        TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') || '- ' || TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MON-YYYY' ) AS FECHA_D ,
        TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) AS FECHA,
        TO_CHAR(CORTECAJA_PAGOSDIA.FREGISTRO) AS FECHA_REGISTRO,
        CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'P' THEN MONTO END PAGOS,
        CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'M' THEN MONTO END MULTA,
        CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'R' THEN MONTO END REFINANCIAMIENTO,
        CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'D' THEN MONTO END DESCUENTO,
        CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'G' THEN MONTO END GARANTIA, 
        CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.CDGOCPE
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE PROCESA_PAGOSDIA = '0'
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = CO.CODIGO
        )
        GROUP BY NOMBRE, FECHA_D, FECHA, CDGOCPE, FECHA_REGISTRO, COD_SUC, SUCURSAL, COMP_BARRA
        SQL;

        /////AND PRN.SITUACION = 'E' PONER ESTA CUANDO ESTEMOS EN PRODUCTIVO
        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosAppHistorico($fi, $ff)
    {
        $qry = <<<SQL
            SELECT
                (
                    COD_SUC || COUNT(NOMBRE) || COMP_BARRA || CAST(SUM(MONTO) AS INTEGER)
                ) AS BARRAS,
                COD_SUC,
                SUCURSAL,
                COUNT(NOMBRE) AS NUM_PAGOS,
                NOMBRE,
                FECHA_D,
                FECHA,
                FECHA_REGISTRO,
                CDGOCPE,
                SUM(PAGOS) AS TOTAL_PAGOS,
                SUM(MULTA) AS TOTAL_MULTA,
                SUM(REFINANCIAMIENTO) AS TOTAL_REFINANCIAMIENTO,
                SUM(DESCUENTO) AS TOTAL_DESCUENTO,
                SUM(GARANTIA) AS GARANTIA,
                SUM(MONTO) AS MONTO_TOTAL
            FROM
                (
                    SELECT
                        TO_CHAR(FECHA, 'DDMMYYYY') AS COMP_BARRA,
                        CO.CODIGO AS COD_SUC,
                        CO.NOMBRE AS SUCURSAL,
                        CORTECAJA_PAGOSDIA.EJECUTIVO AS NOMBRE,
                        TO_CHAR(
                            CORTECAJA_PAGOSDIA.FECHA,
                            'DAY',
                            'NLS_DATE_LANGUAGE=SPANISH'
                        ) || '- ' || TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MON-YYYY') AS FECHA_D,
                        TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY') AS FECHA,
                        TO_CHAR(CORTECAJA_PAGOSDIA.FREGISTRO) AS FECHA_REGISTRO,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'P' THEN MONTO
                        END PAGOS,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'M' THEN MONTO
                        END MULTA,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'R' THEN MONTO
                        END REFINANCIAMIENTO,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'D' THEN MONTO
                        END DESCUENTO,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'G' THEN MONTO
                        END GARANTIA,
                        CORTECAJA_PAGOSDIA.MONTO,
                        CORTECAJA_PAGOSDIA.CDGOCPE
                    FROM
                        CORTECAJA_PAGOSDIA
                        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS
                        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
                    WHERE
                        PROCESA_PAGOSDIA = '1'
                        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
                        AND PRN.CDGCO = CO.CODIGO
                        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'YYYY-MM-DD') BETWEEN '$fi' AND '$ff'
                )
            GROUP BY
                NOMBRE,
                FECHA_D,
                FECHA,
                CDGOCPE,
                FECHA_REGISTRO,
                COD_SUC,
                SUCURSAL,
                COMP_BARRA
        SQL;

        $mysqli = new Database();
        return $mysqli->queryAll($qry);
    }

    public static function ConsultarPagosAppDetalle($ejecutivo, $fecha, $suc)
    {
        $query = <<<sql
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND PROCESA_PAGOSDIA = '0'
        ORDER BY decode(CORTECAJA_PAGOSDIA.TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        $query2 = <<<sql
        SELECT
            SUM(CASE 
        WHEN (ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS_TOTAL,
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_VALIDADOS, 
        SUM(CASE 
        WHEN ((TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS,
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) AS TOTAL_NUEVOS_MONTOS, 
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END) AS TOTAL_MONT_SIN_MOD, 
        (SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) + SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END)) AS TOTAL
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PROCESA_PAGOSDIA = '0'
        ORDER BY decode(TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        //var_dump($query);
        $mysqli = new Database();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosAppDetalleImprimir($ejecutivo, $fecha, $suc)
    {
        $query = <<<sql
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND PROCESA_PAGOSDIA = '1'
        ORDER BY decode(CORTECAJA_PAGOSDIA.TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        $query2 = <<<sql
        SELECT
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_VALIDADOS, 
        SUM(CASE 
        WHEN ((TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS,
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) AS TOTAL_NUEVOS_MONTOS, 
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END) AS TOTAL_MONT_SIN_MOD, 
        (SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) + SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END)) AS TOTAL
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PROCESA_PAGOSDIA = '1'
        ORDER BY decode(TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        //var_dump($query);
        $mysqli = new Database();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosAppResumen($ejecutivo, $fecha, $suc)
    {

        $query = <<<sql
        SELECT * FROM (
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP 
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND CORTECAJA_PAGOSDIA.ESTATUS_CAJA = '1' AND (CORTECAJA_PAGOSDIA.TIPO = 'P' OR CORTECAJA_PAGOSDIA.TIPO = 'M')
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND PROCESA_PAGOSDIA = '0'
        UNION
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        0 AS MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP 
        FROM CORTECAJA_PAGOSDIA INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha' 
        AND CORTECAJA_PAGOSDIA.ESTATUS_CAJA = '0' 
        AND (CORTECAJA_PAGOSDIA.TIPO != 'P' OR CORTECAJA_PAGOSDIA.TIPO != 'M') 
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO AND PRN.CDGCO = '$suc' AND PROCESA_PAGOSDIA = '0' )
        ORDER BY decode(TIPO , 'P',1, 'M',2, 'G',3, 'D', 4, 'R', 5 ) ASC

sql;
        //var_dump($query);
        $query2 = <<<sql
        SELECT
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_VALIDADOS, 
        
        SUM(CASE 
        WHEN ((TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS,
    
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) AS TOTAL_NUEVOS_MONTOS, 
        
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END) AS TOTAL_MONT_SIN_MOD, 
        
        
        (SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) + SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END)) AS TOTAL
        FROM CORTECAJA_PAGOSDIA
        WHERE CDGOCPE = '$ejecutivo' 
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND ESTATUS_CAJA = '1'
        ORDER BY decode(TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D', 4,
                        'R', 5
                        ) asc
sql;


        //var_dump($query2);
        $mysqli = new Database();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosFechaSucursal($cdgns)
    {

        $query = <<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO) AS NOMBRE_SUCURSAL,
        PAGOSDIA.SECUENCIA,
        PAGOSDIA.FECHA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.NOMBRE,
        PAGOSDIA.CICLO,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.TIPO AS TIP,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        TO_CHAR(PAGOSDIA.FREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO
    FROM
        PAGOSDIA, NS, CO, RG
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
		AND PAGOSDIA.TIPO IN ('B', 'F')
        AND PAGOSDIA.CDGNS = $cdgns
    ORDER BY
        FREGISTRO DESC, SECUENCIA
sql;
        $mysqli = new Database();

        return $mysqli->queryAll($query);
    }

}
