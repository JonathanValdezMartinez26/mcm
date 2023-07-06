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
        PAGOSDIA.TIPO AS TIP,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        PAGOSDIA.FREGISTRO,
        ------PAGOSDIA.FIDENTIFICAPP,
        TRUNC(FREGISTRO) + 11/24 +  0/1440 AS DE,
        TRUNC(FREGISTRO) + 1 + 12/24 +  10/1440 AS HASTA,
        CASE
        WHEN SYSDATE BETWEEN (TRUNC(FECHA) + 11/24 + 0/1440) AND (TRUNC(FECHA) + 1 + 11/24 + 10/1440) THEN 'SI'
        Else 'NO'
        END AS DESIGNATION,
        CASE
        WHEN SYSDATE BETWEEN (TRUNC(FECHA) + 11/24 + 0/1440) AND (TRUNC(FECHA) + 2 + 11/24 + 0/1440) THEN 'SI'
        Else 'NO'
        END AS DESIGNATION_ADMIN
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
        FREGISTRO DESC, SECUENCIA
sql;

      $mysqli = Database::getInstance();
      return $mysqli->queryAll($query);
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
        //var_dump($mysqli->queryAll($query));
        return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosAdministracionOne($noCredito){

            $query=<<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL,1) CLIENTE,
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
	    AND sc.SITUACION = 'A'
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
