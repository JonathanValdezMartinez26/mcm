<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use \Core\MasterDom;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Creditos
{

    public static function ConsultaGarantias($noCredito)
    {
        $mysqli = new Database();
        $query = <<<sql
SELECT
    GARPREN.SECUENCIA,
    GARPREN.ARTICULO,
    GARPREN.MARCA,
    GARPREN.MODELO,
    GARPREN.SERIE NO_SERIE,
    GARPREN.MONTO,
    GARPREN.FACTURA,
     TO_CHAR(GARPREN.FECREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FECREGISTRO
FROM
    GARPREN
WHERE 
	GARPREN.CDGEM = 'EMPFIN'
	AND GARPREN.ESTATUS = 'A'
	AND GARPREN.CDGNS = '$noCredito'

sql;

        return $mysqli->queryAll($query);
    }

    public static function ActualizacionCredito($noCredito)
    {
        $mysqli = new Database();
        $query = <<<sql
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

    public static function SelectSucursalAllCreditoCambioSuc($noCredito)
    {

        $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		SC.SITUACION,
		SN.PLAZOSOL PLAZO,
		CALCULA_PARCIALIDAD(SN.PERIODICIDAD, SN.TASA, NVL(SC.CANTAUTOR,SC.CANTSOLIC), SN.PLAZOSOL) PARCIALIDAD,
		Q2.CDGCL ID_AVAL,
		GET_NOMBRE_CLIENTE(Q2.CDGCL) AVAL,
		SN.CDGCO ID_SUCURSAL,
		GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
		SN.CDGOCPE ID_EJECUTIVO,
		GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO
	FROM 
		SN, SC, SC Q2, PRN 
	WHERE
		SC.CDGNS = '$noCredito'
		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
	  	AND SC.CICLO !='R1' 
		AND SC.CICLO != 'R2'
		AND SC.CICLO != 'R3'
		AND SC.CICLO != 'R4'
		AND SC.CICLO != 'R5'
		AND SC.CICLO != 'R6'
		AND SC.CICLO != 'R7'
		AND PRN.CDGNS = SN.CDGNS
		AND PRN.CICLO = SN.CICLO
		AND PRN.SITUACION != 'T'
		AND SC.CANTSOLIC <> '9999' order by SN.INICIO DESC
sql;


        $mysqli = new Database();
        return $mysqli->queryOne($query);
    }

    public static function ListaSucursales()
    {
        //////cambiar el parametro CDGPE
        $query = <<<sql
        SELECT DISTINCT 
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
        ORDER BY
	            SUCURSAL ASC
sql;

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function UpdateSucursal($sucursal_c)
    {

        $credito = $sucursal_c->_credito;
        $ciclo = $sucursal_c->_ciclo;
        $nueva_sucursal = $sucursal_c->_nueva_sucursal;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaSucursal($credito, $ciclo, $nueva_sucursal);
    }

    public static function ProcedureGarantias($garantias_c)
    {
        $credito = $garantias_c->_credito;
        $articulo = $garantias_c->_articulo;
        $marca = $garantias_c->_marca;
        $modelo = $garantias_c->_modelo;
        $serie = $garantias_c->_serie;
        $factura = $garantias_c->_factura;
        $usuario = $garantias_c->_usuario;
        $valor = $garantias_c->_valor;

        $mysqli = new Database();
        return $mysqli->queryProcedureInsertGarantias($credito, $articulo, $marca, $modelo, $serie, $factura, $usuario, $valor, 1);
    }

    public static function ProcedureGarantiasDelete($id, $secu)
    {

        $credito = $id;
        $secuencia = $secu;

        $mysqli = new Database();
        return $mysqli->queryProcedureDeleteGarantias($credito, $secuencia, 3);
    }

    public static function ProcedureGarantiasUpdate($garantias_c)
    {
        $credito = $garantias_c->_credito;
        $articulo = $garantias_c->_articulo;
        $marca = $garantias_c->_marca;
        $modelo = $garantias_c->_modelo;
        $serie = $garantias_c->_serie;
        $factura = $garantias_c->_factura;
        $usuario = $garantias_c->_usuario;
        $valor = $garantias_c->_valor;
        $secuencia = $garantias_c->_secuencia;



        $mysqli = new Database();
        return $mysqli->queryProcedureUpdatesGarantias($credito, $articulo, $marca, $modelo, $serie, $factura, $usuario, $valor, $secuencia);
    }

    public static function ConsultarPagosAdministracionOne($noCredito)
    {

        $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		PRN.SITUACION,
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
		AND SC.CANTSOLIC <> '9999' order by SC.SOLICITUD  desc
sql;


        $mysqli = new Database();
        return $mysqli->queryOne($query);
    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    public static function UpdateActulizaCredito($credito_c)
    {

        $credito = $credito_c->_credito;
        $credito_n = $credito_c->_credito_nuevo;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCredito($credito, $credito_n);
    }
    public static function UpdateActulizaCiclo($credito_c)
    {

        $credito = $credito_c->_credito;
        $ciclo_nuevo = $credito_c->_ciclo_nuevo;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCreditoCiclo($credito, $ciclo_nuevo);
    }
    public static function UpdateActulizaSituacion($credito_c)
    {

        $credito = $credito_c->_credito;
        $ciclo_nuevo = $credito_c->_ciclo_nuevo;
        $situacion = $credito_c->_situacion;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCreditoSituacion($credito, $ciclo_nuevo, $situacion);
    }
}
