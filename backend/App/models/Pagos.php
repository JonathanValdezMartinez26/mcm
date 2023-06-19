<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;

class Pagos{

    public static function ConsultarPagosAdministracion( $noCredito){

            $query=<<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO),
        PAGOSDIA.SECUENCIA,
        PAGOSDIA.FECHA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.NOMBRE,
        PAGOSDIA.CICLO,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        PAGOSDIA.FREGISTRO,
        PAGOSDIA.FIDENTIFICAPP
    FROM
        PAGOSDIA, NS, CO, RG
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND PAGOSDIA.CDGNS = '$noCredito'
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
    ORDER BY
        FECHA DESC, SECUENCIA
sql;

      $mysqli = Database::getInstance();
      return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosAdministracionOne($noCredito){

            $query=<<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		SC.SITUACION,
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
		SN, SC, SC Q2 
	WHERE
		SC.CDGNS = '$noCredito'
		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
		AND SC.CANTSOLIC <> '9999' order by SC.SOLICITUD  desc
sql;


        $mysqli = Database::getInstance();
        return $mysqli->queryOne($query);
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

    public static function getAllByIdCorteCaja($user){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT *
FROM CORTECAJA_PAGOSDIA 

sql;

        return $mysqli->queryAll($query);
    }

    public static function insertProcedure(){
        $query=<<<sql
        CALL SPACCIONPAGODIA('EMPFIN',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'001237','04','10','PRUEBA PRUEBA LOL','TESP','TERESA SANCHEZ PEREZ','DGNV','2652','P','1',?, '')
sql;

        $mysqli = Database::getInstance();
        return $mysqli->queryProcedurePago($query);

    }

    public static function ListaEjecutivos(){

        $query=<<<sql
        SELECT
	CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) EJECUTIVO,
	CODIGO ID_EJECUTIVO
FROM
	PE
WHERE
	CDGEM = 'EMPFIN' 
	AND CDGCO = '018'
	AND ACTIVO = 'S'
ORDER BY 1
sql;

        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);

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
	AND PGD.FECHA BETWEEN TIMESTAMP '$f1 00:00:00.000000' AND TIMESTAMP '$f2 00:00:00.000000'
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
    var_dump($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        return $mysqli->update($query);
    }

}
