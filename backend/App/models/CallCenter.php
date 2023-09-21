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
        SELECT COUNT(ID_SCALL) as LLAMADA_UNO, (DIA_LLAMADA_1_CL ||' '|| HORA_LLAMADA_1_CL) AS HORA_LLAMADA_UNO, (DIA_LLAMADA_2_CL ||' '|| HORA_LLAMADA_2_CL) AS HORA_LLAMADA_DOS, NUMERO_INTENTOS_CL, COMENTARIO_INICIAL, COMENTARIO_FINAL, 
        FIN_CL AS FINALIZADA
        FROM SOL_CALL_CENTER 
        WHERE CICLO ='$ciclo' AND CDGCL_CL = '$id_cliente' 
        GROUP BY ID_SCALL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, PRG_UNO_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, NUMERO_INTENTOS_CL, COMENTARIO_INICIAL, COMENTARIO_FINAL, FIN_CL         
        
sql;

        $desbloqueo_aval=<<<sql
        select COUNT(ID_SCALL) as LLAMADA_UNO, (DIA_LLAMADA_1_AV ||' '|| HORA_LLAMADA_1_AV) AS HORA_LLAMADA_UNO, 
               (DIA_LLAMADA_2_AV ||' '|| HORA_LLAMADA_2_AV) AS HORA_LLAMADA_DOS, PRG_UNO_AV, NUMERO_INTENTOS_AV, FIN_AV AS FINALIZADA
        from SOL_CALL_CENTER 
        WHERE CICLO ='$ciclo' AND CDGCL_CL = '$id_cliente' 
        GROUP BY ID_SCALL, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, PRG_UNO_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, NUMERO_INTENTOS_AV,
        FIN_AV
sql;
        //var_dump($desbloqueo_aval);

        $cliente = $mysqli->queryOne($query2);
        $aval = $mysqli->queryOne($query3);
        $llamada_cl = $mysqli->queryOne($desbloqueo_cl);
        $llamada_av = $mysqli->queryOne($desbloqueo_aval);

        //var_dump($desbloqueo_cl);

        return [$credito_, $cliente, $aval, $llamada_cl, $llamada_av];

    }

    public static function getComboSucursales($CDGPE){

        $mysqli = Database::getInstance();
        $query=<<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM ASIGNACION_SUC_A
           INNER JOIN CO ON CO.CODIGO = ASIGNACION_SUC_A.CDGCO 
           WHERE ASIGNACION_SUC_A.CDGPE = 'ADMIN'
           AND CO.CODIGO = ASIGNACION_SUC_A.CDGCO 
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);

    }

    public static function getComboSucursalesGlobales(){

        $mysqli = Database::getInstance();
        $query=<<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM CO
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);

    }

    public static function getComboSucursalesHorario(){

        $mysqli = Database::getInstance();
        $query=<<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM CO
           WHERE NOT EXISTS(SELECT CDGCO FROM CIERRE_HORARIO WHERE CIERRE_HORARIO.CDGCO = CO.CODIGO)
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);

    }

    public static function getAllSolicitudesHistorico($fecha_inicio, $fecha_fin, $cdgco){

        $string_from_array = implode(', ', $cdgco);

        $mysqli = Database::getInstance();
        $query=<<<sql
            
	     SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
	     WHERE SPR.CDGCO IN($string_from_array)
	     AND SPR.FECHA_TRABAJO BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000'
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);

    }

    public static function getAllSolicitudes($cdgco){

        $string_from_array = implode(', ', $cdgco);

        $mysqli = Database::getInstance();

        $query=<<<sql
             
	     SELECT DISTINCT * FROM SOLICITUDES_PENDIENTES SPE
	     WHERE SPE.CDGCO IN($string_from_array)
	     AND SPE.SOLICITUD > TIMESTAMP '2023-09-04 00:00:00.000000'
	     UNION 
	     SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
	     WHERE SPR.CDGCO IN($string_from_array)
	     AND SPR.SOLICITUD > TIMESTAMP '2023-09-04 00:00:00.000000'
         AND (ESTATUS_FINAL IS NULL OR ESTATUS_FINAL = 'PENDIENTE')
sql;


        //var_dump($query);
        return $mysqli->queryAll($query);

    }

    public static function getAllSolicitudesConcentrado($Fecha, $Region){

        if($Region != '')
        {
            if($Region != '0')
            {
                $Region_cond = "AND RG.CODIGO = '$Region'";
            }
            else
            {
                $Region_cond = '';
            }
           // $condicional = " AND SOL_CALL_CENTER.FECHA_SOL BETWEEN TO_DATE('$Fecha', 'YY-mm-dd') AND TO_DATE('$Fecha', 'YY-mm-dd') $Region_cond";
            $condicional = " $Region_cond";
        }
        else
        {
            $condicional= '';
        }


        $mysqli = Database::getInstance();
        $query=<<<sql
          SELECT DISTINCT 
 		SOL_CALL_CENTER.CDGNS || '-' || SOL_CALL_CENTER.CICLO AS CLAVE,
        RG.CODIGO AS CODIGO_REGION,
 		RG.NOMBRE AS REGION,
 		SOL_CALL_CENTER.FECHA_TRA_CL,
 		SOL_CALL_CENTER.FECHA_TRABAJO_AV,
 		TO_CHAR(SOL_CALL_CENTER.FECHA_SOL ,'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOL, 
 		'' AS INICIO, 
 		CO.CODIGO AS CODIGO_SUCURSAL, 
 		CO.NOMBRE AS AGENCIA,
 		CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS EJECUTIVO,
 		SOL_CALL_CENTER.CDGCL_CL AS CLIENTE, 
 		CL.NOMBRE1 || ' ' ||  CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE AS NOMBRE_CLIENTE, 
 		SOL_CALL_CENTER.CICLO, 
 		SOL_CALL_CENTER.TEL_CL, 
 		SOL_CALL_CENTER.TIPO_LLAM_1_CL,
 		SOL_CALL_CENTER.TIPO_LLAM_2_CL,
 		SOL_CALL_CENTER.PRG_UNO_CL,
 		SOL_CALL_CENTER.PRG_DOS_CL,
 		SOL_CALL_CENTER.PRG_TRES_CL,
 		SOL_CALL_CENTER.PRG_CUATRO_CL,
 		SOL_CALL_CENTER.PRG_CINCO_CL,
 		SOL_CALL_CENTER.PRG_SEIS_CL,
 		SOL_CALL_CENTER.PRG_SIETE_CL,
 		SOL_CALL_CENTER.PRG_OCHO_CL,
 		SOL_CALL_CENTER.PRG_NUEVE_CL,
 		SOL_CALL_CENTER.PRG_DIEZ_CL,
 		SOL_CALL_CENTER.PRG_ONCE_CL,
 		SOL_CALL_CENTER.PRG_DOCE_CL,
 		CL.NOMBRE1 || ' ' ||  CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE AS NOMBRE_AVAL,
 		SOL_CALL_CENTER.TEL_AV, 
 		SOL_CALL_CENTER.TIPO_LLAM_1_AV,
 		SOL_CALL_CENTER.TIPO_LLAM_2_AV, 
 		SOL_CALL_CENTER.PRG_UNO_AV,
 		SOL_CALL_CENTER.PRG_DOS_AV,
 		SOL_CALL_CENTER.PRG_TRES_AV,
 		SOL_CALL_CENTER.PRG_CUATRO_AV,
 		SOL_CALL_CENTER.PRG_CINCO_AV,
 		SOL_CALL_CENTER.PRG_SEIS_AV,
 		SOL_CALL_CENTER.PRG_SIETE_AV,
 		SOL_CALL_CENTER.PRG_OCHO_AV,
 		SOL_CALL_CENTER.PRG_NUEVE_AV
            
        FROM SOL_CALL_CENTER 
        
        INNER JOIN CL ON CL.CODIGO = SOL_CALL_CENTER.CDGCL_CL
        INNER JOIN CO ON SOL_CALL_CENTER.CDGCO = CO.CODIGO
        INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
        INNER JOIN PE ON PE.CODIGO = SOL_CALL_CENTER.CDGPE 
        
        WHERE CL.CODIGO = SOL_CALL_CENTER.CDGCL_CL
        $condicional
         
sql;
    //var_dump($query);
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

    public static function getAllRegiones(){
        $mysqli = Database::getInstance();

        $query3=<<<sql
        SELECT RG.NOMBRE AS REGION, CO.CODIGO, CO.NOMBRE FROM CO
        INNER JOIN RG ON RG.CODIGO = CO.CDGRG
        ORDER BY CODIGO
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

    public static function UpdateResumen($encuesta){
        $mysqli = Database::getInstance();

        $query=<<<sql
                UPDATE SOL_CALL_CENTER
                SET COMENTARIO_INICIAL='$encuesta->_comentarios_iniciales', COMENTARIO_FINAL='$encuesta->_comentarios_finales'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function UpdateResumenFinal($encuesta){
        $mysqli = Database::getInstance();

        $query=<<<sql
                UPDATE SOL_CALL_CENTER
                SET COMENTARIO_INICIAL='$encuesta->_comentarios_iniciales', COMENTARIO_FINAL='$encuesta->_comentarios_finales', SEMAFORO = '1', ESTATUS = '$encuesta->_estatus_solicitud', VOBO_GERENTE_REGIONAL = '$encuesta->_vobo_gerente'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
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
            (ID_SCALL, CDGRG, FECHA_TRA_CL, FECHA_SOL, CDGNS, CDGCO, CDGPE, CDGCL_CL, CICLO, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, INCIDENCIA_COMERCIAL, VOBO_GERENTE_REGIONAL, CDGPE_ANALISTA, SEMAFORO, LLAMADA_POST_VENTA, RECAPTURADA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV)
            VALUES(sol_call_center_id.nextval, '$encuesta->_cdgre', TIMESTAMP '$encuesta->_fecha.000000', TIMESTAMP '$encuesta->_fecha_solicitud.000000', '$encuesta->_cdgns','$encuesta->_cdgco', 'AMGM', '$encuesta->_cliente', '$encuesta->_ciclo', '$encuesta->_movil', '$encuesta->_tipo_llamada', '2023-09-08', '12:32:19.000', NULL, NULL, '$encuesta->_uno', '$encuesta->_dos', '$encuesta->_tres', '$encuesta->_cuatro', '$encuesta->_cinco', '$encuesta->_seis', '$encuesta->_siete', '$encuesta->_ocho', '$encuesta->_nueve', '$encuesta->_diez', '$encuesta->_once', '$encuesta->_doce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '1', NULL)
sql;
            }
            else
            { //Agregar un registro completo (Bien) lLAMADA 2
                $query=<<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_CL='$encuesta->_tipo_llamada', DIA_LLAMADA_2_CL='2023-09-08', HORA_LLAMADA_2_CL='12:32:19.000', PRG_UNO_CL='$encuesta->_uno', PRG_DOS_CL='$encuesta->_dos', PRG_TRES_CL='$encuesta->_tres', PRG_CUATRO_CL='$encuesta->_cuatro', PRG_CINCO_CL='$encuesta->_cinco', PRG_SEIS_CL='$encuesta->_seis', PRG_SIETE_CL='$encuesta->_siete', PRG_OCHO_CL='$encuesta->_ocho', PRG_NUEVE_CL='$encuesta->_nueve', PRG_DIEZ_CL='$encuesta->_diez', PRG_ONCE_CL='$encuesta->_once', PRG_DOCE_CL='$encuesta->_doce', CDGCL_AV=NULL, TEL_AV=NULL, FECHA_TRABAJO_AV=NULL, TIPO_LLAM_1_AV=NULL, DIA_LLAMADA_1_AV=NULL, HORA_LLAMADA_1_AV=NULL, TIPO_LLAM_2_AV=NULL, DIA_LLAMADA_2_AV=NULL, HORA_LLAMADA_2_AV=NULL, PRG_UNO_AV=NULL, PRG_DOS_AV=NULL, PRG_TRES_AV=NULL, PRG_CUATRO_AV=NULL, PRG_CINCO_AV=NULL, PRG_SEIS_AV=NULL, PRG_SIETE_AV=NULL, PRG_OCHO_AV=NULL, PRG_NUEVE_AV=NULL, COMENTARIO_INICIAL=NULL, COMENTARIO_FINAL=NULL, ESTATUS=NULL, INCIDENCIA_COMERCIAL=NULL, VOBO_GERENTE_REGIONAL=NULL, CDGPE_ANALISTA=NULL, SEMAFORO=NULL, LLAMADA_POST_VENTA=NULL, RECAPTURADA=NULL, CDGPE_ANALISTA_INICIAL=NULL
                , NUMERO_INTENTOS_CL ='$encuesta->_llamada', FIN_CL = '1' 
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
                (ID_SCALL, CDGRG, FECHA_TRA_CL, FECHA_SOL, CDGNS, CDGCO, CDGPE, CDGCL_CL, CICLO, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, INCIDENCIA_COMERCIAL, VOBO_GERENTE_REGIONAL, CDGPE_ANALISTA, SEMAFORO, LLAMADA_POST_VENTA, RECAPTURADA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV)
                VALUES(sol_call_center_id.nextval, '$encuesta->_cdgre', TIMESTAMP '$encuesta->_fecha.000000', TIMESTAMP '$encuesta->_fecha_solicitud.000000', '$encuesta->_cdgns', '$encuesta->_cdgco', 'AMGM', '$encuesta->_cliente', '$encuesta->_ciclo', '$encuesta->_movil', '$encuesta->_tipo_llamada', '2023-09-08', '12:32:19.000', NULL, NULL, '$encuesta->_uno', '$encuesta->_dos', '$encuesta->_tres', '$encuesta->_cuatro', '$encuesta->_cinco', '$encuesta->_seis', '$encuesta->_siete', '$encuesta->_ocho', '$encuesta->_nueve', '$encuesta->_diez', '$encuesta->_once', '$encuesta->_doce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL)
sql;
            }
            else
            {

                $query=<<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_CL='$encuesta->_tipo_llamada', DIA_LLAMADA_2_CL='2023-09-08', HORA_LLAMADA_2_CL='12:32:19.000', PRG_UNO_CL='$encuesta->_uno', PRG_DOS_CL='$encuesta->_dos', PRG_TRES_CL='$encuesta->_tres', PRG_CUATRO_CL='$encuesta->_cuatro', PRG_CINCO_CL='$encuesta->_cinco', PRG_SEIS_CL='$encuesta->_seis', PRG_SIETE_CL='$encuesta->_siete', PRG_OCHO_CL='$encuesta->_ocho', PRG_NUEVE_CL='$encuesta->_nueve', PRG_DIEZ_CL='$encuesta->_diez', PRG_ONCE_CL='$encuesta->_once', PRG_DOCE_CL='$encuesta->_doce', CDGCL_AV=NULL, TEL_AV=NULL, FECHA_TRABAJO_AV=NULL, TIPO_LLAM_1_AV=NULL, DIA_LLAMADA_1_AV=NULL, HORA_LLAMADA_1_AV=NULL, TIPO_LLAM_2_AV=NULL, DIA_LLAMADA_2_AV=NULL, HORA_LLAMADA_2_AV=NULL, PRG_UNO_AV=NULL, PRG_DOS_AV=NULL, PRG_TRES_AV=NULL, PRG_CUATRO_AV=NULL, PRG_CINCO_AV=NULL, PRG_SEIS_AV=NULL, PRG_SIETE_AV=NULL, PRG_OCHO_AV=NULL, PRG_NUEVE_AV=NULL, COMENTARIO_INICIAL=NULL, COMENTARIO_FINAL=NULL, ESTATUS=NULL, INCIDENCIA_COMERCIAL=NULL, VOBO_GERENTE_REGIONAL=NULL, CDGPE_ANALISTA=NULL, SEMAFORO=NULL, LLAMADA_POST_VENTA=NULL, RECAPTURADA=NULL, CDGPE_ANALISTA_INICIAL=NULL,
                NUMERO_INTENTOS_CL ='$encuesta->_llamada'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }

        }
        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function insertEncuestaAV($encuesta){

        $mysqli = Database::getInstance(1);

        if($encuesta->_completo == '1')
        {
            if($encuesta->_llamada == '1')
            {
                //Agregar un registro completo (Bien) lLAMADA 1
                $query=<<<sql
            UPDATE SOL_CALL_CENTER
            SET CDGCL_AV=NULL, TEL_AV='$encuesta->_movil', FECHA_TRABAJO_AV= TIMESTAMP '2023-08-22 04:21:40.000000', 
            TIPO_LLAM_1_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_1_AV='2023-09-08', HORA_LLAMADA_1_AV='04:21:40', 
            PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
            PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada', FIN_AV = '1'
            WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }
            else
            { //Agregar un registro completo (Bien) lLAMADA 2
                $query=<<<sql
                UPDATE SOL_CALL_CENTER
            SET CDGCL_AV=NULL, TEL_AV='$encuesta->_movil', 
            TIPO_LLAM_2_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_2_AV='2023-09-08', HORA_LLAMADA_2_AV='04:21:40', 
            PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
            PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada', FIN_AV = '1'
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
                UPDATE SOL_CALL_CENTER
                SET FECHA_TRABAJO_AV= TIMESTAMP '2023-08-22 04:21:40.000000', TIPO_LLAM_1_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_1_AV='2023-09-08', HORA_LLAMADA_1_AV='04:21:40', 
                PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
                PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }
            else
            {

                $query=<<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_2_AV='2023-09-08', HORA_LLAMADA_2_AV='04:21:40',
                PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
                PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }

        }
        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function insertAsignaSucursal($asigna){

        $mysqli = Database::getInstance(1);

           $query=<<<sql
            INSERT INTO ASIGNACION_SUC_A
            (ID_ASIGNACION, CDGEM, CDGPE, CDGCO, FECHA_INICIO, FECHA_FIN, FECHA_ALTA, CDGOCPE)
            VALUES(SUC_SUCURSALES.nextval, 'EMPFIN', '$asigna->_ejecutivo', '$asigna->_region', TIMESTAMP '$asigna->_fecha_registro', TIMESTAMP '$asigna->_fecha_inicio 00:00:00.000000', TIMESTAMP '$asigna->_fecha_fin 00:00:00.000000', 'AMGM')
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }

}
