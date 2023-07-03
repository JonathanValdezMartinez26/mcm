<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database_cultiva;

class Operaciones{

    public static function ConsultarDesembolsos($Inicial, $Final){

        $query=<<<sql
        SELECT PRN.CANTENTRE, PRC.CDGEM, PRN.CICLO, EF.NOMBRE AS LOCALIDAD, '001'  AS SUCURSAL, 
        '08' AS TIPO_OPERACION, CL.CODIGO AS ID_CLIENTE,  
        PRC.CDGNS AS NUM_CUENTA, '01' AS INSTRUMENTO_MONETARIO,  'MXN' AS MONEDA,
        PRC.CANTENTRE AS MONTO, to_char(PRN.INICIO,'yyyymmdd') AS FECHA_OPERACION, '4' AS TIPO_RECEPTOR, 
        'Imbursa' AS CLAVE_RECEPTOR, '0' AS NUM_CAJA, '0' AS ID_CAJERO, to_char(PRN.INICIO,'yyyymmdd') AS FECHA_HORA,
        '0' AS NOTARJETA_CTA, PRC.NOCHEQUE AS TIPOTARJETA, '0' AS COD_AUTORIZACION, 'NO' AS ATRASO,
        PRN.CDGCO AS OFICINA_CLIENTE, PRN.SITUACION
        FROM PRC 
        INNER JOIN PRN ON PRC.CDGNS = PRN.CDGNS 
        INNER JOIN CL ON PRC.CDGCL = CL.CODIGO 
        INNER JOIN EF ON CL.CDGEF = EF.CODIGO 
        INNER JOIN CO ON PRN.CDGCO = CO.CODIGO 
        WHERE PRC.CDGEM = 'EMPFIN'
        AND PRN.SITUACION = 'E'
        AND PRC.SITUACION = 'E'
        
        AND PRC.FEXPCHEQUE BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd') ORDER BY PRN.INICIO

sql;

        //AND PRC.CDGNS = '003065'

        try {
            $mysqli = Database_cultiva::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return "";
        }
    }

    public static function ConsultarPagos($Inicial, $Final){

        $query=<<<sql
                SELECT PRN.CANTENTRE, PRC.CDGEM, PRN.CICLO, EF.NOMBRE AS LOCALIDAD, PRN.CDGCO AS SUCURSAL,
                '09' AS TIPO_OPERACION, CL.CODIGO AS ID_CLIENTE, 
                PRC.CDGNS AS NUM_CUENTA, '01' AS INSTRUMENTO_MONETARIO, 'MXN' AS MONEDA, 
                ROUND((MP.CANTIDAD * PRC.CANTENTRE)/PRN.CANTENTRE, 2)  AS MONTO, to_char(PRN.INICIO,'yyyymmdd') AS FECHA_OPERACION,  
                (CASE WHEN CB.NOMBRE = 'OXXO' THEN 1 ELSE 4 END) AS TIPO_RECEPTOR,
                IB.NOMBRE AS CLAVE_RECEPTOR, '0' AS NUM_CAJA, '0' AS ID_CAJERO, to_char(PRN.INICIO,'yyyymmdd') AS FECHA_HORA,
                '0' AS NOTARJETA_CTA, '4' AS TIPOTARJETA, '0' AS COD_AUTORIZACION, 'NO' AS ATRASO,
                PRN.CDGCO AS OFICINA_CLIENTE, PRN.SITUACION
                FROM PRC 
                
                INNER JOIN PRN ON PRC.CDGNS = PRN.CDGNS 
                
                INNER JOIN MP ON PRN.CDGNS = MP.CDGNS 
                INNER JOIN CL ON CL.CODIGO = PRC.CDGCL 
                INNER JOIN EF ON CL.CDGEF = EF.CODIGO -------------EF ES EL ESTADO
                INNER JOIN CB ON CB.CDGIB = MP.CDGCB  -------------CB ES EL 
                INNER JOIN IB ON CB.CDGIB = IB.CODIGO -------------IB ES EL LISTADOI DE LOS BANCOS
                
                WHERE MP.CDGEM = 'EMPFIN' AND MP.TIPO = 'PD' AND MP.ESTATUS = 'B'
                AND (CDGNS) IN (SELECT CDGNS FROM MP)
                AND MP.CDGNS = PRN.CDGNS 
                AND PRN.CDGNS = PRC.CDGNS 
                AND prn.SITUACION = 'E'
                AND PRN.INICIO BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd') ORDER BY PRN.INICIO

sql;

            $mysqli = Database_cultiva::getInstance();
            return $mysqli->queryAll($query);

    }

}
