<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;

class Pagos{

    public static function ConsultarPagosAdministracion($noCredito, $hora){

        $query=<<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO),
        PAGOSDIA.SECUENCIA,
        TO_CHAR(PAGOSDIA.FECHA, 'YYYY-MM-DD' ) AS FECHA,
        TO_CHAR(PAGOSDIA.FECHA, 'DD-MM-YYYY' ) AS FECHA_TABLA,
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
      $mysqli = Database::getInstance();
      return $mysqli->queryAll($query);
    }

    public static function insertHorarios($horario){

        $mysqli = Database::getInstance(1);

        //Agregar un registro completo (Bien) lLAMADA 1
        $query=<<<sql
           INSERT INTO CIERRE_HORARIO
            (ID_CIERRE_HORARIO, HORA_CIERRE, HORA_PRORROGA, CDGCO, CDGPE, FECHA_ALTA)
            VALUES(CIERRE_HORARIO_SECUENCIA.nextval, '$horario->_hora', 'NULL', '$horario->_sucursal', 'AMGM', '$horario->_fecha_registro')
             
sql;
//var_dump($query);
        return $mysqli->insert($query);
    }

    public static function updateHorarios($horario){

        $mysqli = Database::getInstance(1);

        //Agregar un registro completo (Bien) lLAMADA 1
        $query=<<<sql
        UPDATE CIERRE_HORARIO
        SET HORA_CIERRE='$horario->_hora'
        WHERE CDGCO='$horario->_sucursal'
sql;
//var_dump($query);
        return $mysqli->insert($query);
    }

    public static function updateEstatusValidaPago($update){

        $mysqli = Database::getInstance(1);

        //Agregar un registro completo (Bien) lLAMADA 1
        $query=<<<sql
        UPDATE CORTECAJA_PAGOSDIA
        SET ESTATUS_CAJA='$update->_estatus'
        WHERE CORTECAJA_PAGOSDIA_PK='$update->_id_check'
sql;
        return $mysqli->insert($query);
    }


    public static function updatePagoApp($update){

        $mysqli = Database::getInstance(1);

        //Agregar un registro completo (Bien) lLAMADA 1
        $query=<<<sql
        UPDATE CORTECAJA_PAGOSDIA
        SET INCIDENCIA='1', NUEVO_MONTO = '$update->_nuevo_monto', COMENTARIO_INCIDENCIA = '$update->_comentario_detalle', ESTATUS_CAJA = '0', TIPO = '$update->_tipo_pago'
        WHERE CORTECAJA_PAGOSDIA_PK='$update->_id_registro'
sql;
        return $mysqli->insert($query);
    }

    public static function AddPagoApp($pk, $barcode){

        $mysqli = Database::getInstance(1);

        //Agregar un registro completo (Bien) lLAMADA 1
        $query=<<<sql
        INSERT INTO FOLIO_APP
        (ID_FOLIO_APP, FOLIO, CORTECAJA_PAGOSDIA_PK, FECHA_REGISTRO)
        VALUES(FOLIO_APP_I.nextval, '$barcode', '$pk', CURRENT_TIMESTAMP)
sql;
        $query_1=<<<sql
        UPDATE CORTECAJA_PAGOSDIA
        SET  PROCESA_PAGOSDIA = '1'
        WHERE CORTECAJA_PAGOSDIA_PK= '$pk'
sql;
//        UPDATE CORTECAJA_PAGOSDIA SET PROCESA_PAGOSDIA=NULL
        //var_dump($query_1);

        $insert_folio = $mysqli->insert($query);
        $update_pk = $mysqli->insert($query_1);



        return [$insert_folio, $update_pk];
    }


    public static function getByIdReporte($folio){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM FOLIO_APP fa
        INNER JOIN CORTECAJA_PAGOSDIA cp ON cp.CORTECAJA_PAGOSDIA_PK = fa.CORTECAJA_PAGOSDIA_PK
        WHERE FOLIO = '$folio'
        ORDER BY decode(cp.TIPO ,
                                'P',1,
                                'M',2,
                                'G',3,
                                'D',4,
                                'R',5
                                ) asc
sql;

        $query_1=<<<sql
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
        END)) AS TOTAL, CO.NOMBRE AS NOMBRE_SUC, FA.FOLIO, CORTECAJA_PAGOSDIA.EJECUTIVO
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN FOLIO_APP FA ON FA.CORTECAJA_PAGOSDIA_PK = CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND FA.FOLIO = '$folio'
        AND PROCESA_PAGOSDIA = '1'
        GROUP BY FA.FOLIO, CO.NOMBRE, CORTECAJA_PAGOSDIA.EJECUTIVO
sql;

        $tabla = $mysqli->queryAll($query);
        $datos = $mysqli->queryOne($query_1);


        return [$datos, $tabla];

    }


    public static function ConsultarHorarios(){

        $query=<<<sql
        SELECT * FROM CIERRE_HORARIO
        INNER JOIN CO ON CO.CODIGO = CIERRE_HORARIO.CDGCO
        ORDER BY CIERRE_HORARIO.FECHA_ALTA ASC 
sql;

        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }

    public static function ConsultarDiasFestivos(){

        $query=<<<sql
        SELECT 
        TO_CHAR(FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') || '- ' || TO_CHAR(FECHA, 'DD-MON-YYYY' ) AS FECHA,
        UPPER(DESCRIPCION) AS DESCRIPCION, 
        TO_CHAR(FECHA_CAPTURA , 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') || '- ' || TO_CHAR(FECHA_CAPTURA , 'DD-MON-YYYY' ) AS FECHA_CAPTURA
        FROM DIAS_FESTIVOS
        ORDER BY DIA_FESTIVO_PK ASC 
sql;

        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosApp(){

        $query=<<<sql
               SELECT (COD_SUC || COUNT(NOMBRE) || COMP_BARRA || CAST(SUM(MONTO) AS INTEGER)) AS BARRAS, COD_SUC, SUCURSAL, COUNT(NOMBRE) AS NUM_PAGOS, NOMBRE, FECHA_D, FECHA, 
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
        
sql;


        /////AND PRN.SITUACION = 'E' PONER ESTA CUANDO ESTEMOS EN PRODUCTIVO
        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }
    public static function ConsultarPagosAppDetalle($ejecutivo, $fecha, $suc){
        $query=<<<sql
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

        $query2=<<<sql
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
        $mysqli = Database::getInstance();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosAppDetalleImprimir($ejecutivo, $fecha, $suc){
        $query=<<<sql
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

        $query2=<<<sql
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
        $mysqli = Database::getInstance();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosAppResumen($ejecutivo, $fecha, $suc){

        $query=<<<sql
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
        $query2=<<<sql
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
        $mysqli = Database::getInstance();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosFechaSucursal($id_sucursal, $Inicial, $Final){

        if($id_sucursal)
        {
            $valor_sucursal = 'AND NS.CDGCO ='.$id_sucursal;
        }
        $query=<<<sql
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
        TO_CHAR(PAGOSDIA.FREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO,       
        ----------------PAGOSDIA.FIDENTIFICAPP,
        TRUNC(FREGISTRO) + 12/24 AS DE,
        TRUNC(FREGISTRO) + 1 + 12/24 AS HASTA,
        CASE
        WHEN FREGISTRO >= TRUNC(FREGISTRO) + 12/24 AND FREGISTRO <=TRUNC(FREGISTRO) + 1 + 12/24 THEN 'SI'
        Else 'NO'
        END AS DESIGNATION
    FROM
        PAGOSDIA, NS, CO, RG
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND PAGOSDIA.FECHA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd') 
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
        $valor_sucursal
    ORDER BY
        FREGISTRO DESC, SECUENCIA
sql;
        $mysqli = Database::getInstance();

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosAdministracionOne( $noCredito, $perfil, $user){

        if($perfil != 'ADMIN')
        {
            $Q1 = "AND PRN.CDGCO = 
            
            ANY(SELECT
        CO.CODIGO ID_SUCURSAL
        FROM
        PCO, CO, RG
        WHERE
        PCO.CDGCO = CO.CODIGO
        AND CO.CDGRG = RG.CODIGO
        AND PCO.CDGEM = 'EMPFIN'
        AND PCO.CDGPE = '$user') 
            
            
            ";
        }
        else
        {
            $Q1 = '';
        }

        $query=<<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		PRN.SITUACION,
        CASE PRN.SITUACION
        WHEN 'S'THEN 'SOLICITADO' 
        WHEN 'E'THEN 'ENTREGADO' 
        WHEN 'A'THEN 'AUTORIZADO' 
        WHEN 'L'THEN 'LIQUIDADO' 
        ELSE 'DESCONOCIDO'
      END SITUACION_NOMBRE,
               CASE PRN.SITUACION
    WHEN 'S'THEN '#1F6CC1FF'
    WHEN 'E'THEN '#298732FF' 
    WHEN 'A'THEN '#A31FC1FF' 
    WHEN 'L'THEN '#000000FF' 
    ELSE '#FF0000FF'
  END COLOR,
               CASE PRN.SITUACION
    WHEN 'E'THEN ''
    ELSE 'none'
  END ACTIVO,
		SN.PLAZOSOL PLAZO,
		SN.PERIODICIDAD,
		SN.TASA,
		DIA_PAGO(SN.NOACUERDO) DIA_PAGO,
		CALCULA_PARCIALIDAD(SN.PERIODICIDAD, SN.TASA, NVL(SC.CANTAUTOR,SC.CANTSOLIC), SN.PLAZOSOL) PARCIALIDAD,
		Q2.CDGCL ID_AVAL,
		GET_NOMBRE_CLIENTE(Q2.CDGCL) AVAL,
		SN.CDGCO ID_SUCURSAL,
		GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
		SN.CDGOCPE ID_EJECUTIVO,
		GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO,
		SC.CDGPI ID_PROYECTO
	FROM 
		SN, SC, SC Q2, PRN
	WHERE
		SC.CDGNS = '$noCredito'
		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
	    AND PRN.CICLO = SC.CICLO 
		AND PRN.CDGNS = SC.CDGNS 
		AND PRN.SITUACION IN('E', 'L')
	    $Q1
		AND SC.CANTSOLIC <> '9999' order by SC.SOLICITUD  desc
sql;
        //var_dump($query);



        $mysqli = Database::getInstance();
        $consulta = $mysqli->queryOne($query);

        $cdgco = $consulta['ID_SUCURSAL'];

        $query_horario=<<<sql
        SELECT * FROM CIERRE_HORARIO WHERE CDGCO = '$cdgco'
sql;

        $fechaActual = date("Y-m-d");

        $query_dia_festivo=<<<sql
        SELECT COUNT(*) AS TOT, TO_CHAR(FECHA_CAPTURA, 'YYYY-mm-dd') as FECHA_CAPTURA FROM DIAS_FESTIVOS WHERE FECHA_CAPTURA = TIMESTAMP '$fechaActual 00:00:00.000000'
        GROUP BY FECHA_CAPTURA 
sql;

        //var_dump($query_dia_festivo);
        $consulta_horario = $mysqli->queryOne($query_horario);
        $consulta_dia_festivo = $mysqli->queryOne($query_dia_festivo);

        return[$consulta, $consulta_horario, $consulta_dia_festivo];

    }

    public static function ConsultarCierreCajaCajera($user){

        $mysqli = Database::getInstance();
        $query_horario=<<<sql
        SELECT * FROM CIERRE_HORARIO WHERE CDGPE = '$user'
sql;

        return $mysqli->queryOne($query_horario);
    }

    public static function ActualizacionCredito($noCredito){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT
    GARPREN.SECUENCIA,
    GARPREN.ARTICULO,
    GARPREN.MARCA,
    GARPREN.MODELO,
    GARPREN.SERIE NO_SERIE,
    GARPREN.MONTO,
    GARPREN.FACTURA
FROM
    GARPREN
WHERE 
	GARPREN.CDGEM = 'EMPFIN'
	AND GARPREN.ESTATUS = 'A'
	AND GARPREN.CDGNS = '$noCredito'

sql;

        return $mysqli->queryAll($query);
    }

    public static function getAllCorteCaja(){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT COUNT(CDGPE) AS NUM_PAG, CDGPE, SUM(MONTO) AS MONTO_TOTAL,
SUM(CASE WHEN TIPO = 'P' THEN monto ELSE 0 END) AS MONTO_PAGO,
SUM(CASE WHEN TIPO = 'M' THEN monto ELSE 0 END) AS MONTO_GARANTIA,
SUM(CASE WHEN TIPO = 'D' THEN monto ELSE 0 END) AS MONTO_DESCUENTO,
SUM(CASE WHEN TIPO = 'R' THEN monto ELSE 0 END) AS MONTO_REFINANCIAMIENTO,
SUM(CASE WHEN TIPO = 'G' THEN monto ELSE 0 END) AS MONTO_MULTA
FROM CORTECAJA_PAGOSDIA
GROUP BY CDGPE 
HAVING COUNT (CDGPE) > 0

sql;

        return $mysqli->queryAll($query);
    }

    public static function getAllCorteCajaByID($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT EJECUTIVO, COUNT(CDGPE) AS NUM_PAG, CDGPE, SUM(MONTO) AS MONTO_TOTAL,
SUM(CASE WHEN TIPO = 'P' THEN monto ELSE 0 END) AS MONTO_PAGO,
SUM(CASE WHEN TIPO = 'M' THEN monto ELSE 0 END) AS MONTO_GARANTIA,
SUM(CASE WHEN TIPO = 'D' THEN monto ELSE 0 END) AS MONTO_DESCUENTO,
SUM(CASE WHEN TIPO = 'R' THEN monto ELSE 0 END) AS MONTO_REFINANCIAMIENTO,
SUM(CASE WHEN TIPO = 'G' THEN monto ELSE 0 END) AS MONTO_MULTA
FROM CORTECAJA_PAGOSDIA
WHERE CDGPE = '$id'
GROUP BY CDGPE, EJECUTIVO 
HAVING COUNT (CDGPE) > 0 


sql;
        return $mysqli->queryOne($query);
    }

    public static function getAllByIdCorteCaja($user){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT *
FROM CORTECAJA_PAGOSDIA 

sql;

        return $mysqli->queryAll($query);
    }

    public static function insertProcedure($pago){

        $credito_i = $pago->_credito;
        $fecha_i = $pago->_fecha;
        $ciclo_i = $pago->_ciclo;
        $monto_i = $pago->_monto;
        $tipo_i = $pago->_tipo;
        $nombre_i = $pago->_nombre;
        $user_i = $pago->_usuario;
        $ejecutivo_i = $pago->_ejecutivo;
        $ejecutivo_nombre_i = $pago->_ejecutivo_nombre;
        $tipo_procedure_ = 1;
        $fecha_aux = "";


        $mysqli = Database::getInstance();
        return $mysqli->queryProcedurePago($credito_i, $ciclo_i, $monto_i, $tipo_i, $nombre_i, $user_i,  $ejecutivo_i, $ejecutivo_nombre_i,  $tipo_procedure_, $fecha_aux, "", $fecha_i);

    }

    public static function EditProcedure($pago){

        $credito_i = $pago->_credito;
        $fecha = $pago->_fecha;
        $secuencia_i = $pago->_secuencia;
        $ciclo_i = $pago->_ciclo;
        $monto_i = $pago->_monto;
        $tipo_i = $pago->_tipo;
        $nombre_i = $pago->_nombre;
        $user_i = $pago->_usuario;
        $ejecutivo_i = $pago->_ejecutivo;
        $ejecutivo_nombre_i = $pago->_ejecutivo_nombre;
        $tipo_procedure_ = 2;
        $fecha_aux = $pago->_fecha_aux;


        $mysqli = Database::getInstance();

        return $mysqli->queryProcedurePago($credito_i, $ciclo_i, $monto_i, $tipo_i, $nombre_i, $user_i,  $ejecutivo_i, $ejecutivo_nombre_i, $tipo_procedure_, $fecha_aux, $secuencia_i, $fecha);



    }

    public static function ListaEjecutivos($cdgco){

        $query=<<<sql
        SELECT
        CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) EJECUTIVO,
        CODIGO ID_EJECUTIVO
        FROM
            PE
        WHERE
            CDGEM = 'EMPFIN' 
            AND CDGCO = '$cdgco'
            AND ACTIVO = 'S'
        ORDER BY 1
sql;

        //var_dump($query);

        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);

    }


    public static function ListaEjecutivosAdmin($credito){
        $query_cdgco=<<<sql
        SELECT PRN.CDGCO, PRN.CDGOCPE  FROM PRN WHERE PRN.CDGNS = '$credito' ORDER BY PRN.CICLO DESC
sql;

        $mysqli = Database::getInstance();
        $res_cdgco = $mysqli->queryOne($query_cdgco);
        //var_dump($query_cdgco);

        $cdgco = $res_cdgco['CDGCO'];
        $cdgocpe = $res_cdgco['CDGOCPE'];

        if($cdgco == '026' || $cdgco == '025' || $cdgco == '014')
            {
                $cdgco = "'026','025','014'";
            }
            else
            {
                $cdgco = "'".$cdgco."'";
            }


        $query=<<<sql
        SELECT
	CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) EJECUTIVO,
	CODIGO ID_EJECUTIVO
FROM
	PE
WHERE
	CDGEM = 'EMPFIN' 
    AND CDGCO IN($cdgco)
	AND ACTIVO = 'S'
    AND BLOQUEO = 'N'
ORDER BY 1
sql;
 //var_dump($query);
        $val = $mysqli->queryAll($query);
        return [$val, $cdgocpe];

    }

    public static function ListaSucursales($id_user){

        $query=<<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        CO.CODIGO ID_SUCURSAL,
        CO.NOMBRE SUCURSAL
        FROM
        PCO, CO, RG
        WHERE
        PCO.CDGCO = CO.CODIGO
        AND CO.CDGRG = RG.CODIGO
        AND PCO.CDGEM = 'EMPFIN'
        AND PCO.CDGPE = '$id_user'
        ORDER BY
        ID_REGION,
        ID_SUCURSAL
sql;

        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);

    }

    public static function GeneraLayoutContable($f1, $f2){

        $query=<<<sql
        	SELECT
	FECHA,
	CASE
		WHEN PGD.TIPO = 'P' THEN 'P' || PRN.CDGNS || PRN.CDGTPC || FN_DV('P' || PRN.CDGNS || PRN.CDGTPC)
		WHEN PGD.TIPO = 'G' THEN '0' || PRN.CDGNS || PRN.CDGTPC || FN_DV('0' || PRN.CDGNS || PRN.CDGTPC)
		ELSE 'NO IDENTIFICADO'
	END REFERENCIA,
	PGD.MONTO,
	'MN' MONEDA
FROM
	PAGOSDIA PGD, PRN
WHERE
	PGD.CDGEM = PRN.CDGEM
	AND PGD.CDGNS = PRN.CDGNS
	AND PGD.CICLO = PRN.CICLO
	AND PGD.CDGEM = 'EMPFIN'
	AND PGD.ESTATUS = 'A'
	AND PGD.TIPO IN('P','G')
	AND PGD.FECHA BETWEEN TO_DATE('$f1', 'YY-mm-dd') AND TO_DATE('$f2', 'YY-mm-dd') 
ORDER BY
	PGD.FECHA
sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return "";
        }

    }

    public static function DeletePago($id, $secuencia, $fecha){
        $mysqli = Database::getInstance(true);
        $query=<<<sql
      UPDATE PAGOSDIA SET ESTATUS = 'E' WHERE CDGNS = '$id' AND SECUENCIA = '$secuencia' AND FREGISTRO <> TIMESTAMP '$fecha 00:00:00.000000'
sql;
        $accion = new \stdClass();
        $accion->_sql= $query;
        return $mysqli->update($query);
    }

    public static function DeleteProcedure($cdgns, $fecha, $user, $secuencia){
        $mysqli = Database::getInstance();
        return $mysqli->queryProcedureDeletePago($cdgns, $fecha, $user, $secuencia);

    }


}
