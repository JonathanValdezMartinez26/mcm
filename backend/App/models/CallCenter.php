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
		SC.CDGPI ID_PROYECTO, 
        TO_CHAR(SN.SOLICITUD ,'YYYY-MM-DD HH24:MI:SS') AS FECHA_SOL
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
        UPPER(MU.NOMBRE) MUNICIPIO,
        LO.NOMBRE LOCALIDAD,
        COL.NOMBRE COLONIA,
        COL.CDGPOSTAL CP,
        CL.CALLE, PI.NOMBRE ACT_ECO
    FROM
        CL,
        EF,
        MU,
        LO,
        COL, 
        PI
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
        AND PI.CDGCL = CL.CODIGO 
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
        UPPER(MU.NOMBRE) MUNICIPIO,
        LO.NOMBRE LOCALIDAD,
        COL.NOMBRE COLONIA,
        COL.CDGPOSTAL CP,
        CL.CALLE, 
        PI.NOMBRE ACT_ECO
    FROM
        CL,
        EF,
        MU,
        LO,
        COL, 
        PI
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
        AND PI.CDGCL = CL.CODIGO 
sql;

        $desbloqueo_cl=<<<sql
        select COUNT(ID_SCALL) as LLAMADA_UNO, (DIA_LLAMADA_1_CL ||' '|| HORA_LLAMADA_1_CL) AS HORA_LLAMADA_UNO, (DIA_LLAMADA_2_CL ||' '|| HORA_LLAMADA_2_CL) AS HORA_LLAMADA_DOS, PRG_UNO_CL  from SOL_CALL_CENTER 
        WHERE CICLO ='$ciclo' and DIA_LLAMADA_1_CL IS NOT NULL AND CDGCL_CL = '$id_cliente' 
        GROUP BY ID_SCALL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, PRG_UNO_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL
sql;

        $desbloqueo_aval=<<<sql
        select COUNT(ID_SCALL) as LLAMADA_UNO, (DIA_LLAMADA_1_AV ||' '|| HORA_LLAMADA_1_AV) AS HORA_LLAMADA_UNO, (DIA_LLAMADA_2_AV ||' '|| HORA_LLAMADA_2_AV) AS HORA_LLAMADA_DOS, PRG_UNO_AV  from SOL_CALL_CENTER 
        WHERE CICLO ='$ciclo' and DIA_LLAMADA_1_AV IS NOT NULL AND CDGCL_CL = '$id_cliente' 
        GROUP BY ID_SCALL, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, PRG_UNO_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV
sql;
        //var_dump($desbloqueo_aval);

        $cliente = $mysqli->queryOne($query2);
        $aval = $mysqli->queryOne($query3);
        $llamada_cl = $mysqli->queryOne($desbloqueo_cl);
        $llamada_av = $mysqli->queryOne($desbloqueo_aval);

        //var_dump($desbloqueo);

        return [$credito_, $cliente, $aval, $llamada_cl, $llamada_av];

    }

    public static function getAllSolicitudes(){

        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT SN.CDGNS, SN.CICLO, TO_CHAR(SN.SOLICITUD ,'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOL, SN.INICIO, 
        SN.CDGCO, SC.CDGCL, CL.NOMBRE1 || ' ' ||  CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE NOMBRE, 
        CO.NOMBRE AS NOMBRE_SUCURSAL,CO.CODIGO AS CODIGO_SUCURSAL, RG.NOMBRE AS REGION, RG.CODIGO AS CODIGO_REGION, CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS EJECUTIVO,
            PE.CODIGO ID_EJECUTIVO, TO_CHAR(SN.SOLICITUD) AS FECHA
        FROM SN 
        INNER JOIN SC ON SN.CDGNS = SC.CDGNS 
        INNER JOIN CL ON CL.CODIGO = SC.CDGCL 
        INNER JOIN CO ON SN.CDGCO = CO.CODIGO 
        INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
        INNER JOIN PE ON PE.CODIGO = SN.CDGOCPE 
        
        
        WHERE SN.CICLO = SC.CICLO 
        AND SN.CDGNS = SC.CDGNS 
        AND CL.CODIGO = SC.CDGCL 
        AND SN.SITUACION = 'S' 
        AND SC.CANTSOLIC != '9999'
        ORDER BY SN.SOLICITUD DESC
sql;

        return $mysqli->queryAll($query);

    }

    public static function getAllAnalistas(){

        $mysqli = Database::getInstance();

        $query3=<<<sql
        SELECT
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) NOMBRE,
            UT.CDGTUS PERFIL, PE.CDGCO, PE.CODIGO AS USUARIO
        FROM
            PE,
            UT
        WHERE
            PE.CODIGO = UT.CDGPE
            AND PE.CDGEM = UT.CDGEM
            AND PE.CDGEM = 'EMPFIN'
            AND PE.ACTIVO = 'S'
            AND (PE.BLOQUEO = 'N' OR PE.BLOQUEO IS NULL)
            AND UT.CDGTUS = 'CALLC'
			AND NOT EXISTS(SELECT CDGPE FROM ASIGNACION_SUC_A WHERE PE.CODIGO = ASIGNACION_SUC_A.CDGPE)
sql;
        return $mysqli->queryAll($query3);


    }

    public static function getAllAnalistasAsignadas(){

        $mysqli = Database::getInstance();

        $query3=<<<sql
         SELECT
           *
        FROM
           ASIGNACION_SUC_A
               
sql;
        return $mysqli->queryAll($query3);


    }

    public static function insertEncuestaCL($encuesta){

        $mysqli = Database::getInstance(1);

        if($encuesta->_completo == '1')
        {
            if($encuesta->_llamada == '1')
            {
                //Agregar un registro completo (Bien) lLAMADA 1
                $query=<<<sql
            INSERT INTO SOL_CALL_CENTER
            (ID_SCALL, CDGRG, FECHA_TRA_CL, FECHA_SOL, CDGCO, CDGPE, CDGCL_CL, CICLO, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, INCIDENCIA_COMERCIAL, VOBO_GERENTE_REGIONAL, CDGPE_ANALISTA, SEMAFORO, LLAMADA_POST_VENTA, RECAPTURADA, CDGPE_ANALISTA_INICIAL)
            VALUES(sol_call_center_id.nextval, '$encuesta->_cdgre', TIMESTAMP '$encuesta->_fecha.000000', TIMESTAMP '$encuesta->_fecha_solicitud.000000', '$encuesta->_cdgco', 'AMGM', '$encuesta->_cliente', '$encuesta->_ciclo', '$encuesta->_movil', '$encuesta->_tipo_llamada', '2023-09-08', '12:32:19.000', NULL, NULL, 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)
sql;
            }
            else
            { //Agregar un registro completo (Bien) lLAMADA 2
                $query=<<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_CL='$encuesta->_tipo_llamada', DIA_LLAMADA_2_CL='2023-09-08', HORA_LLAMADA_2_CL='12:32:19.000', PRG_UNO_CL='S', PRG_DOS_CL='S', PRG_TRES_CL='S', PRG_CUATRO_CL='S', PRG_CINCO_CL='S', PRG_SEIS_CL='S', PRG_SIETE_CL='S', PRG_OCHO_CL='S', PRG_NUEVE_CL='S', PRG_DIEZ_CL='S', PRG_ONCE_CL='S', PRG_DOCE_CL='S', CDGCL_AV=NULL, TEL_AV=NULL, FECHA_TRABAJO_AV=NULL, TIPO_LLAM_1_AV=NULL, DIA_LLAMADA_1_AV=NULL, HORA_LLAMADA_1_AV=NULL, TIPO_LLAM_2_AV=NULL, DIA_LLAMADA_2_AV=NULL, HORA_LLAMADA_2_AV=NULL, PRG_UNO_AV=NULL, PRG_DOS_AV=NULL, PRG_TRES_AV=NULL, PRG_CUATRO_AV=NULL, PRG_CINCO_AV=NULL, PRG_SEIS_AV=NULL, PRG_SIETE_AV=NULL, PRG_OCHO_AV=NULL, PRG_NUEVE_AV=NULL, COMENTARIO_INICIAL=NULL, COMENTARIO_FINAL=NULL, ESTATUS=NULL, INCIDENCIA_COMERCIAL=NULL, VOBO_GERENTE_REGIONAL=NULL, CDGPE_ANALISTA=NULL, SEMAFORO=NULL, LLAMADA_POST_VENTA=NULL, RECAPTURADA=NULL, CDGPE_ANALISTA_INICIAL=NULL
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }

        }
        else if($encuesta->_completo == '0')
        {
            if($encuesta->_llamada == '1')
            {
                //Agregar un registro incompleto
                $query=<<<sql
                INSERT INTO SOL_CALL_CENTER
                (ID_SCALL, CDGRG, FECHA_TRA_CL, FECHA_SOL, CDGCO, CDGPE, CDGCL_CL, CICLO, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, INCIDENCIA_COMERCIAL, VOBO_GERENTE_REGIONAL, CDGPE_ANALISTA, SEMAFORO, LLAMADA_POST_VENTA, RECAPTURADA, CDGPE_ANALISTA_INICIAL)
                VALUES(sol_call_center_id.nextval, '$encuesta->_cdgre', TIMESTAMP '$encuesta->_fecha.000000', TIMESTAMP '$encuesta->_fecha_solicitud.000000', '$encuesta->_cdgco', 'AMGM', '$encuesta->_cliente', '$encuesta->_ciclo', '$encuesta->_movil', '$encuesta->_tipo_llamada', '2023-09-08', '12:32:19.000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)
sql;
            }
            else
            {

                $query=<<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_CL='$encuesta->_tipo_llamada', DIA_LLAMADA_2_CL='2023-09-08', HORA_LLAMADA_2_CL='12:32:19.000', PRG_UNO_CL=NULL, PRG_DOS_CL=NULL, PRG_TRES_CL=NULL, PRG_CUATRO_CL=NULL, PRG_CINCO_CL=NULL, PRG_SEIS_CL=NULL, PRG_SIETE_CL=NULL, PRG_OCHO_CL=NULL, PRG_NUEVE_CL=NULL, PRG_DIEZ_CL=NULL, PRG_ONCE_CL=NULL, PRG_DOCE_CL=NULL, CDGCL_AV=NULL, TEL_AV=NULL, FECHA_TRABAJO_AV=NULL, TIPO_LLAM_1_AV=NULL, DIA_LLAMADA_1_AV=NULL, HORA_LLAMADA_1_AV=NULL, TIPO_LLAM_2_AV=NULL, DIA_LLAMADA_2_AV=NULL, HORA_LLAMADA_2_AV=NULL, PRG_UNO_AV=NULL, PRG_DOS_AV=NULL, PRG_TRES_AV=NULL, PRG_CUATRO_AV=NULL, PRG_CINCO_AV=NULL, PRG_SEIS_AV=NULL, PRG_SIETE_AV=NULL, PRG_OCHO_AV=NULL, PRG_NUEVE_AV=NULL, COMENTARIO_INICIAL=NULL, COMENTARIO_FINAL=NULL, ESTATUS=NULL, INCIDENCIA_COMERCIAL=NULL, VOBO_GERENTE_REGIONAL=NULL, CDGPE_ANALISTA=NULL, SEMAFORO=NULL, LLAMADA_POST_VENTA=NULL, RECAPTURADA=NULL, CDGPE_ANALISTA_INICIAL=NULL
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }

        }

        return $mysqli->insert($query);
    }

}
