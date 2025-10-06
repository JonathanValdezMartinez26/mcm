<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;

class AhorroSimple
{

    public static function ConsultarPagosFechaSucursal($cdgns)
    {
		
		$query_datos = <<<sql
		SELECT
			PD.PAGOSDIA,
			PD.RETIROS_AHORRO_SIMPLE,
			PD.PAGOSDIA - PD.RETIROS_AHORRO_SIMPLE AS TOTAL,
			PD.FECHA_APERTURA_AHORRO,
			C.NO_CREDITO,
			C.CICLO,
			C.CLIENTE,
			C.ID_SUCURSAL,
			C.SUCURSAL,
			C.ID_EJECUTIVO,
			C.EJECUTIVO
		FROM (
			SELECT 
				-- Total de pagos del día
				(SELECT NVL(SUM(MONTO), 0)
				   FROM PAGOSDIA
				  WHERE CDGNS = '003011'
					AND ESTATUS = 'A') AS PAGOSDIA,
					
				-- Total de retiros de ahorro simple
				(SELECT NVL(SUM(CANTIDAD_AUTORIZADA), 0)
				   FROM RETIROS_AHORRO_SIMPLE
				  WHERE CDGNS = '003011') AS RETIROS_AHORRO_SIMPLE,
				  
				-- Fecha del primer registro tipo B o F (inicio del ahorro)
				(SELECT MIN(FECHA)
				   FROM PAGOSDIA
				  WHERE CDGNS = '003011'
					AND TIPO IN ('B','F')) AS FECHA_APERTURA_AHORRO
			FROM DUAL
		) PD
		CROSS JOIN (
			SELECT *
			FROM (
				SELECT 
					SC.CDGNS NO_CREDITO,
					SC.CICLO,
					GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
					SN.CDGCO ID_SUCURSAL,
					GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
					SN.CDGOCPE ID_EJECUTIVO,
					GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO
				FROM 
					SN
					JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO
					JOIN SC Q2 ON SC.CDGNS = Q2.CDGNS AND SC.CICLO = Q2.CICLO AND SC.CDGCL <> Q2.CDGCL
					JOIN PRN ON PRN.CICLO = SC.CICLO AND PRN.CDGNS = SC.CDGNS
				WHERE
					SC.CDGNS = '003011'
					AND SC.CANTSOLIC <> '9999'
				ORDER BY SC.SOLICITUD DESC
			)
			WHERE ROWNUM = 1
		) C
sql;	


        $query = <<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO) AS NOMBRE_SUCURSAL,
        PAGOSDIA.SECUENCIA,
        PAGOSDIA.FECHA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.TIPO AS TIP,
		PAGOSDIA.CICLO,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        TO_CHAR(PAGOSDIA.FREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO,
		'ABONO' AS TIPO_OPERA
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
	
		$res1 = $mysqli->queryOne($query_datos);
		$res2 = $mysqli->queryAll($query);
        return [$res1, $res2];
    }

}
