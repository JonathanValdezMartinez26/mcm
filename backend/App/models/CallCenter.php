<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;

class CallCenter{


    public static function getAllDescription($credito, $ciclo){

        $mysqli = Database::getInstance();
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
		SC.CDGNS = '$credito'
		AND SC.CICLO = '$ciclo'
		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
		AND SC.CANTSOLIC <> '9999'
sql;

        $credito_ = $mysqli->queryOne($query);
        $id_cliente = $credito_['ID_CLIENTE'];
        $id_aval= $credito_['ID_AVAL'];

        $query2=<<<sql
         SELECT
        CONCATENA_NOMBRE(CL.NOMBRE1,CL.NOMBRE2,CL.PRIMAPE,CL.SEGAPE) NOMBRE,
        CL.NACIMIENTO,
        TRUNC(MONTHS_BETWEEN(SYSDATE, CL.NACIMIENTO) / 12) EDAD,
        CL.SEXO,
        EDO_CIVIL(CL.EDOCIVIL) EDO_CIVIL,
        CL.TELEFONO,
        EF.NOMBRE ESTADO,
        MU.NOMBRE MUNICIPIO,
        LO.NOMBRE LOCALIDAD,
        COL.NOMBRE COLONIA,
        COL.CDGPOSTAL CP,
         CL.CALLE
    FROM
        CL,
        EF,
        MU,
        LO,
        COL
    WHERE
        CL.CODIGO = '$id_cliente'
        AND EF.CODIGO = CL.CDGEF
        AND MU.CODIGO = CL.CDGMU
        AND LO.CODIGO = CL.CDGLO 
        AND COL.CODIGO = CL.CDGCOL
        AND EF.CODIGO = MU.CDGEF 
        AND EF.CODIGO = LO.CDGEF
        AND EF.CODIGO = COL.CDGEF
        AND MU.CODIGO = LO.CDGMU 
        AND MU.CODIGO = COL.CDGMU 
        AND LO.CODIGO = COL.CDGLO
sql;
        $query3=<<<sql
         SELECT
        CONCATENA_NOMBRE(CL.NOMBRE1,CL.NOMBRE2,CL.PRIMAPE,CL.SEGAPE) NOMBRE,
        CL.NACIMIENTO,
        TRUNC(MONTHS_BETWEEN(SYSDATE, CL.NACIMIENTO) / 12) EDAD,
        CL.SEXO,
        EDO_CIVIL(CL.EDOCIVIL) EDO_CIVIL,
        CL.TELEFONO,
        EF.NOMBRE ESTADO,
        MU.NOMBRE MUNICIPIO,
        LO.NOMBRE LOCALIDAD,
        COL.NOMBRE COLONIA,
        COL.CDGPOSTAL CP,
         CL.CALLE
    FROM
        CL,
        EF,
        MU,
        LO,
        COL
    WHERE
        CL.CODIGO = '$id_aval'
        AND EF.CODIGO = CL.CDGEF
        AND MU.CODIGO = CL.CDGMU
        AND LO.CODIGO = CL.CDGLO 
        AND COL.CODIGO = CL.CDGCOL
        AND EF.CODIGO = MU.CDGEF 
        AND EF.CODIGO = LO.CDGEF
        AND EF.CODIGO = COL.CDGEF
        AND MU.CODIGO = LO.CDGMU 
        AND MU.CODIGO = COL.CDGMU 
        AND LO.CODIGO = COL.CDGLO
sql;

        $cliente = $mysqli->queryOne($query2);
        $aval = $mysqli->queryOne($query3);

        return [$credito_, $cliente, $aval];

    }

}
