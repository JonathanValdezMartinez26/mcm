<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class CallCenter
{


    public static function getAllDescription($credito, $ciclo, $fec)
    {

        $date = str_replace('/', '-', $fec);
        $newDate = date("Y-m-d H:i:s", strtotime($date));


        $mysqli = new Database();
        $query = <<<sql
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
        $id_aval = $credito_['ID_AVAL'];

        $query2 = <<<sql
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
        CL.CALLE, PI.NOMBRE ACT_ECO, 
        CL.CLABE
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
        ORDER BY PI.ACTUALIZA DESC
sql;

        $cliente = $mysqli->queryOne($query2);

        $id_cliente_recomendado = $cliente['CLABE'];
        if ($id_cliente_recomendado != '') {
            $query_recomendado = <<<sql
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
            CL.CODIGO = '$id_cliente_recomendado'
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
            ORDER BY PI.ACTUALIZA DESC
sql;

            $res_recomendado = $mysqli->queryOne($query_recomendado);
        } else {
            $res_recomendado = '';
        }

        //var_dump($query2);
        $query3 = <<<sql
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
        ORDER BY PI.ACTUALIZA DESC
sql;


        $desbloqueo_cl = <<<sql
                SELECT COUNT(ID_SCALL) as LLAMADA_UNO, 
                       CASE WHEN (DIA_LLAMADA_1_CL IS NOT NULL)  THEN (DIA_LLAMADA_1_CL ||' '|| TO_CHAR(HORA_LLAMADA_1_CL ,'HH24:MI:SS') || ' (' || TIPO_LLAM_1_CL ||')' ) ELSE '-' END AS HORA_LLAMADA_UNO, 
                       CASE WHEN (DIA_LLAMADA_2_CL IS NOT NULL)  THEN (DIA_LLAMADA_2_CL ||' '|| TO_CHAR(HORA_LLAMADA_2_CL ,'HH24:MI:SS') || ' (' || TIPO_LLAM_2_CL ||')' )  ELSE '-' END AS HORA_LLAMADA_DOS, 
                       
                NUMERO_INTENTOS_CL, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, VOBO_GERENTE_REGIONAL,
                FIN_CL AS FINALIZADA, COMENTARIO_PRORROGA, PRORROGA, REACTIVACION 
                FROM SOL_CALL_CENTER 
                WHERE CICLO ='$ciclo' AND CDGCL_CL = '$id_cliente' AND (CICLO != 'R1') AND (FECHA_SOL = TIMESTAMP '$newDate.000')
                GROUP BY ID_SCALL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, PRG_UNO_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, NUMERO_INTENTOS_CL, COMENTARIO_INICIAL, COMENTARIO_FINAL, FIN_CL, COMENTARIO_PRORROGA, PRORROGA, REACTIVACION, ESTATUS, VOBO_GERENTE_REGIONAL, TIPO_LLAM_1_CL, TIPO_LLAM_2_CL          
                
sql;

        $desbloqueo_aval = <<<sql
                select COUNT(ID_SCALL) as LLAMADA_UNO, 
                       DIA_LLAMADA_1_AV AS NUM_LLAM, 
                       CASE WHEN (DIA_LLAMADA_1_AV IS NOT NULL)  THEN (DIA_LLAMADA_1_AV ||' '|| TO_CHAR(HORA_LLAMADA_1_AV ,'HH24:MI:SS') || ' (' || TIPO_LLAM_1_AV ||')' ) ELSE '-' END AS HORA_LLAMADA_UNO, 
                       CASE WHEN (DIA_LLAMADA_2_AV IS NOT NULL)  THEN (DIA_LLAMADA_2_AV ||' '|| TO_CHAR(HORA_LLAMADA_2_AV ,'HH24:MI:SS') || ' (' || TIPO_LLAM_2_AV ||')' )  ELSE '-' END AS HORA_LLAMADA_DOS, 
                       
                       
                       PRG_UNO_AV, NUMERO_INTENTOS_AV, FIN_AV AS FINALIZADA
                from SOL_CALL_CENTER 
                WHERE CICLO ='$ciclo' AND CDGCL_CL = '$id_cliente' AND (CICLO != 'R1')  AND (FECHA_SOL = TIMESTAMP '$newDate.000')
                GROUP BY ID_SCALL, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, PRG_UNO_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, NUMERO_INTENTOS_AV, TIPO_LLAM_1_AV, TIPO_LLAM_2_AV,
                FIN_AV
sql;

        //var_dump($desbloqueo_cl);
        $llamada_cl = $mysqli->queryOne($desbloqueo_cl);
        $llamada_av = $mysqli->queryOne($desbloqueo_aval);
        $cliente = $mysqli->queryOne($query2);
        $aval = $mysqli->queryOne($query3);





        return [$credito_, $cliente, $aval, $llamada_cl, $llamada_av, $res_recomendado];
    }

    public static function getComboSucursales($CDGPE)
    {

        $mysqli = new Database();
        $query = <<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM ASIGNACION_SUC_A
           INNER JOIN CO ON CO.CODIGO = ASIGNACION_SUC_A.CDGCO 
           WHERE ASIGNACION_SUC_A.CDGPE = '$CDGPE'
           AND CO.CODIGO = ASIGNACION_SUC_A.CDGCO 
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getComboSucursalesAllCDGCO()
    {

        $mysqli = new Database();
        $query = <<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM ASIGNACION_SUC_A
           INNER JOIN CO ON CO.CODIGO = ASIGNACION_SUC_A.CDGCO 
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getComboSucursalesGlobales()
    {

        $mysqli = new Database();
        $query = <<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM CO
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getComboSucursalesHorario()
    {

        $mysqli = new Database();
        $query = <<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM CO
           WHERE NOT EXISTS(SELECT CDGCO FROM CIERRE_HORARIO WHERE CIERRE_HORARIO.CDGCO = CO.CODIGO)
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesHistorico($fecha_inicio, $fecha_fin, $cdgco, $cdgpe, $perfil, $suc)
    {




        if ($suc == '000') {
            $string_from_array = implode(', ', $cdgco);
            $con_array_cdgco = '';
        } else {
            $string_from_array = $suc;
            $con_array_cdgco = 'AND SPR.CDGCO IN(' . $string_from_array . ')';
        }

        //var_dump($string_from_array);
        if ($string_from_array != '') {
            $mysqli = new Database();
            $query = <<<sql
                 SELECT * FROM (SELECT DISTINCT ID_SCALL, CDGNS, CICLO, FECHA_SOL, INICIO, SPR.CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, PE.NOMBRE1, PRIMAPE, SEGAPE,  ESTATUS_CL, ESTATUS_AV, SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL,DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL,PRG_DOS_CL,PRG_TRES_CL, PRG_CUATRO_CL, '' AS PRG_CINCO_CL, '' AS PRG_SEIS_CL, '' AS PRG_SIETE_CL, '' AS PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL,  PRG_DOCE_CL, CDGCL_AV,  TEL_AV,  FECHA_TRABAJO_AV, TIPO_LLAM_1_AV,  DIA_LLAMADA_1_AV,  HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV,PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV,  PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA,LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL,FIN_AV, COMENTARIO_PRORROGA
                 FROM SOLICITUDES_PROCESADAS SPR
                 INNER JOIN PE ON PE.CODIGO = SPR.CDGPE
                 WHERE SPR.CDGPE = '$cdgpe' AND SPR.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000'
                 AND SEMAFORO = '1' $con_array_cdgco
                 
                 UNION
                 
                 SELECT DISTINCT ID_SCALL, CDGNS, 
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN 'CANCELADA POR LA ADMINISTRADORA' ELSE 'NO PROCESADA AÚN | CICLO:' || CICLO END AS CICLO,
                 FECHA_SOL, INICIO, CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, 
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN '-' ELSE 'PENDIENTE DE VALIDAR' END AS NOMBRE1,
                 
                 '' AS PRIMAPE, '' AS SEGAPE, 
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN '-' ELSE ESTATUS_CL END AS ESTATUS_CL,
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN '-' ELSE ESTATUS_AV END AS ESTATUS_AV, 
                
                 SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA, LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV, COMENTARIO_PRORROGA
                 FROM SOLICITUDES_PENDIENTES SP
                 WHERE SP.CODIGO_SUCURSAL IN($string_from_array) AND SP.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000')
                

sql;
            //var_dump($query);
            return $mysqli->queryAll($query);
        } else {
            if ($perfil == 'ADMIN' || $perfil == 'ACALL' || $cdgpe == 'ESMM') {
                $mysqli = new Database();
                $query = <<<sql
                 SELECT * FROM (SELECT DISTINCT ID_SCALL, CDGNS, CICLO, FECHA_SOL, INICIO, SPR.CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, PE.NOMBRE1, PRIMAPE, SEGAPE,  ESTATUS_CL, ESTATUS_AV, SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL,DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL,PRG_DOS_CL,PRG_TRES_CL, PRG_CUATRO_CL, '' AS PRG_CINCO_CL, '' AS PRG_SEIS_CL, '' AS PRG_SIETE_CL, '' AS PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL,  PRG_DOCE_CL, CDGCL_AV,  TEL_AV,  FECHA_TRABAJO_AV, TIPO_LLAM_1_AV,  DIA_LLAMADA_1_AV,  HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV,PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV,  PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA,LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL,FIN_AV, COMENTARIO_PRORROGA, RECOMENDADO
                 FROM SOLICITUDES_PROCESADAS SPR
                 INNER JOIN PE ON PE.CODIGO = SPR.CDGPE
                 WHERE SPR.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000'
                 AND SEMAFORO = '1'
                 
                 UNION
                 
                 SELECT DISTINCT ID_SCALL, CDGNS, 
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN 'CANCELADA ADMINISTRADORA' ELSE 'NO PROCESADA AÚN | CICLO:' || CICLO END AS CICLO,
                
                 FECHA_SOL, INICIO, CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, 'PENDIENTE DE VALIDAR' AS NOMBRE1, '' AS PRIMAPE, '' AS SEGAPE,  ESTATUS_CL, ESTATUS_AV, SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA, LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV, COMENTARIO_PRORROGA, RECOMENDADO
                 FROM SOLICITUDES_PENDIENTES SP
                                 WHERE SP.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000')
                ORDER BY FECHA_SOL DESC
sql;
                //var_dump($query);
                return $mysqli->queryAll($query);
            } else {
                return false;
            }
        }
    }

    /////////////////////////////////////////////

    public static function getAllSolicitudesHistoricoExcel($fecha_inicio, $fecha_fin, $cdgco, $usuario, $suc)
    {



        $string_from_array = implode(', ', $cdgco);
        if ($suc == '000') {
            $var = 'SPR.CDGCO IN(' . $string_from_array . ') AND ';
        } else {
            $var = 'SPR.CDGCO IN(' . $suc . ') AND ';
        }


        if ($usuario != '') {
            $var_ = "AND SPR.CDGPE IN('" . $usuario . "')";
        } else {
            $var_ = '';
        }



        $mysqli = new Database();
        $query = <<<sql
                  SELECT * FROM (SELECT DISTINCT (SPR.CDGNS || '-' || SPR.CICLO) AS A, SPR.REGION AS B, SPR.FECHA_TRABAJO AS C,  
                 SPR.FECHA_SOL AS D, '' AS E, SPR.NOMBRE_SUCURSAL AS F, SPR.EJECUTIVO AS G, SPR.CDGNS AS H, (SPR.NOMBRE) AS I,
                 SPR.CICLO AS J, SPR.TEL_CL AS K, SPR.TIPO_LLAM_1_CL AS L, 
                 CASE WHEN SPR.PRG_UNO_CL IS NULL THEN '- *'
                 ELSE SPR.PRG_UNO_CL END AS M,
                  CASE WHEN SPR.PRG_DOS_CL IS NULL THEN '- *'
                 ELSE SPR.PRG_DOS_CL END AS N,
                  CASE WHEN SPR.PRG_TRES_CL IS NULL THEN '-'
                 ELSE SPR.PRG_TRES_CL END AS O,
                  CASE WHEN SPR.PRG_CUATRO_CL IS NULL THEN '-'
                 ELSE SPR.PRG_CUATRO_CL END AS P,
                  CASE WHEN SPR.PRG_CINCO_CL IS NULL THEN '-'
                 ELSE SPR.PRG_CINCO_CL END AS Q,
                  CASE WHEN SPR.PRG_SEIS_CL IS NULL THEN '-'
                 ELSE SPR.PRG_SEIS_CL END AS R,
                  CASE WHEN SPR.PRG_SIETE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_SIETE_CL END AS S,
                  CASE WHEN SPR.PRG_OCHO_CL IS NULL THEN '-'
                 ELSE SPR.PRG_OCHO_CL END AS T,
                  CASE WHEN SPR.PRG_NUEVE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_NUEVE_CL END AS U,
                  CASE WHEN SPR.PRG_DIEZ_CL IS NULL THEN '-'
                 ELSE SPR.PRG_DIEZ_CL END AS V,
                  CASE WHEN SPR.PRG_ONCE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_ONCE_CL END AS W,
                 CASE WHEN SPR.PRG_DOCE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_DOCE_CL END AS X,
                 GET_NOMBRE_CLIENTE(SPR.CDGCL_AV) AS Y,
                 SPR.TEL_AV AS Z, 
                 SPR.TIPO_LLAM_1_AV AS AA, 
                 
                 CASE WHEN SPR.PRG_UNO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_UNO_AV END AS AB,
                  CASE WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                 ELSE SPR.PRG_TRES_AV END AS AC,
                  CASE WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                 ELSE SPR.PRG_TRES_AV END AS AD,
                  CASE WHEN SPR.PRG_CUATRO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_CUATRO_AV END AS AE,
                  CASE WHEN SPR.PRG_CINCO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_CINCO_AV END AS AF,
                  CASE WHEN SPR.PRG_SEIS_AV IS NULL THEN '-'
                 ELSE SPR.PRG_SEIS_AV END AS AG,
                  CASE WHEN SPR.PRG_SIETE_AV IS NULL THEN '-'
                 ELSE SPR.PRG_SIETE_AV END AS AH,
                  CASE WHEN SPR.PRG_OCHO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_OCHO_AV END AS AI,
                  CASE WHEN SPR.PRG_NUEVE_AV IS NULL THEN '-'
                 ELSE SPR.PRG_NUEVE_AV END AS AJ,
                 TO_CHAR(SPR.DIA_LLAMADA_1_CL ,'DD/MM/YYYY HH24:MI:SS') AS AK, 
                 TO_CHAR(SPR.DIA_LLAMADA_2_CL ,'DD/MM/YYYY HH24:MI:SS') AS AL, 
                 TO_CHAR(SPR.DIA_LLAMADA_1_AV ,'DD/MM/YYYY HH24:MI:SS') AS AM, 
                 TO_CHAR(SPR.DIA_LLAMADA_2_AV ,'DD/MM/YYYY HH24:MI:SS') AS AN, 
                 SPR.COMENTARIO_INICIAL AS AO, 
                 SPR.COMENTARIO_FINAL AS AP, 
                 SPR.ESTATUS_FINAL AS AQ, 
                  CASE WHEN SPR.COMENTARIO_PRORROGA IS NULL THEN 'N'
                  ELSE 'S' END AS AR,
                 SPR.VOBO_REG AS ASS,
                 PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE AS ATT,
                 SPR.SEMAFORO AS AU, 
                 '' AS AV, 
                 '' AS AW,
                 '' AS AX, 
                 '' AS AY, 
                 '' AS AZ, 
                 '' AS BA, 
                 '' AS BB, 
                 '' AS BC, 
                 '' AS BD
             
                 FROM  SOLICITUDES_PROCESADAS SPR
                 INNER JOIN PE ON PE.CODIGO = SPR.CDGPE 
                 WHERE $var SPR.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000'
                 AND SEMAFORO = '1' 
                $var_
                 
                 UNION
                 
                 SELECT DISTINCT (SPR.CDGNS || '-' || CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN 'CANCELADA ADMINISTRADORA' ELSE 'NO PROCESADA AÚN | CICLO:' || CICLO END) AS A, SPR.REGION AS B, SPR.FECHA_TRABAJO AS C,  
                 SPR.FECHA_SOL AS D, '' AS E, SPR.NOMBRE_SUCURSAL AS F, SPR.EJECUTIVO AS G, SPR.CDGNS AS H, (SPR.NOMBRE) AS I,
                 CICLO AS J, SPR.TEL_CL AS K, SPR.TIPO_LLAM_1_CL AS L, 
                 CASE WHEN SPR.PRG_UNO_CL IS NULL THEN '- *'
                 ELSE SPR.PRG_UNO_CL END AS M,
                  CASE WHEN SPR.PRG_DOS_CL IS NULL THEN '- *'
                 ELSE SPR.PRG_DOS_CL END AS N,
                  CASE WHEN SPR.PRG_TRES_CL IS NULL THEN '-'
                 ELSE SPR.PRG_TRES_CL END AS O,
                  CASE WHEN SPR.PRG_CUATRO_CL IS NULL THEN '-'
                 ELSE SPR.PRG_CUATRO_CL END AS P,
                  CASE WHEN SPR.PRG_CINCO_CL IS NULL THEN '-'
                 ELSE SPR.PRG_CINCO_CL END AS Q,
                  CASE WHEN SPR.PRG_SEIS_CL IS NULL THEN '-'
                 ELSE SPR.PRG_SEIS_CL END AS R,
                  CASE WHEN SPR.PRG_SIETE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_SIETE_CL END AS S,
                  CASE WHEN SPR.PRG_OCHO_CL IS NULL THEN '-'
                 ELSE SPR.PRG_OCHO_CL END AS T,
                  CASE WHEN SPR.PRG_NUEVE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_NUEVE_CL END AS U,
                  CASE WHEN SPR.PRG_DIEZ_CL IS NULL THEN '-'
                 ELSE SPR.PRG_DIEZ_CL END AS V,
                  CASE WHEN SPR.PRG_ONCE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_ONCE_CL END AS W,
                 CASE WHEN SPR.PRG_DOCE_CL IS NULL THEN '-'
                 ELSE SPR.PRG_DOCE_CL END AS X,
                 GET_NOMBRE_CLIENTE(SPR.CDGCL_AV) AS Y,
                 SPR.TEL_AV AS Z, 
                 SPR.TIPO_LLAM_1_AV AS AA, 
                 
                 CASE WHEN SPR.PRG_UNO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_UNO_AV END AS AB,
                  CASE WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                 ELSE SPR.PRG_TRES_AV END AS AC,
                  CASE WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                 ELSE SPR.PRG_TRES_AV END AS AD,
                  CASE WHEN SPR.PRG_CUATRO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_CUATRO_AV END AS AE,
                  CASE WHEN SPR.PRG_CINCO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_CINCO_AV END AS AF,
                  CASE WHEN SPR.PRG_SEIS_AV IS NULL THEN '-'
                 ELSE SPR.PRG_SEIS_AV END AS AG,
                  CASE WHEN SPR.PRG_SIETE_AV IS NULL THEN '-'
                 ELSE SPR.PRG_SIETE_AV END AS AH,
                  CASE WHEN SPR.PRG_OCHO_AV IS NULL THEN '-'
                 ELSE SPR.PRG_OCHO_AV END AS AI,
                  CASE WHEN SPR.PRG_NUEVE_AV IS NULL THEN '-'
                 ELSE SPR.PRG_NUEVE_AV END AS AJ,
                 TO_CHAR(SPR.DIA_LLAMADA_1_CL ,'DD/MM/YYYY HH24:MI:SS') AS AK, 
                 TO_CHAR(SPR.DIA_LLAMADA_2_CL ,'DD/MM/YYYY HH24:MI:SS') AS AL, 
                 TO_CHAR(SPR.DIA_LLAMADA_1_AV ,'DD/MM/YYYY HH24:MI:SS') AS AM, 
                 TO_CHAR(SPR.DIA_LLAMADA_2_AV ,'DD/MM/YYYY HH24:MI:SS') AS AN, 
                 SPR.COMENTARIO_INICIAL AS AO, 
                 SPR.COMENTARIO_FINAL AS AP, 
                 SPR.ESTATUS_FINAL AS AQ, 
                  CASE WHEN SPR.COMENTARIO_PRORROGA IS NULL THEN 'N'
                  ELSE 'S' END AS AR,
                 SPR.VOBO_REG AS ASS,
                 '' AS ATT,
                 SPR.SEMAFORO AS AU, 
                 '' AS AV, 
                 '' AS AW,
                 '' AS AX, 
                 '' AS AY, 
                 '' AS AZ, 
                 '' AS BA, 
                 '' AS BB, 
                 '' AS BC, 
                 '' AS BD
             
                 FROM  SOLICITUDES_PENDIENTES SPR
                 WHERE $var SPR.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000') ORDER BY D DESC
                 
                 
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudes($cdgco)
    {

        $string_from_array = implode(', ', $cdgco);
        //var_dump($string_from_array);

        if ($string_from_array != '') {
            $in = 'SPE.CDGCO IN(' . $string_from_array . ') AND';
            $in_1 = 'SPR.CDGCO IN(' . $string_from_array . ') AND';
        } else {
            $in = '';
            $in_1 = '';
        }

        $mysqli = new Database();

        $query = <<<sql
         

        SELECT * FROM  
        (
          SELECT DISTINCT * FROM SOLICITUDES_PENDIENTES SPE
	     WHERE $in SPE.SOLICITUD > TIMESTAMP '2023-10-19 00:00:00.000000'
	     AND (SPE.CICLO != 'R1') AND (SPE.CICLO != 'R2') AND (SPE.CICLO != 'R3') AND (SPE.CICLO != 'R4') AND (SPE.CICLO != 'R5')
	     AND (SPE.CICLO != 'D1') AND (SPE.CICLO != 'D2') AND (SPE.CICLO != 'D3') AND (SPE.CICLO != 'D4') AND (SPE.CICLO != 'D5')
	     UNION 
	     SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
	     WHERE $in_1 SPR.SOLICITUD > TIMESTAMP '2023-10-19 00:00:00.000000'
         AND (ESTATUS_FINAL IS NULL OR ESTATUS_FINAL = 'PENDIENTE')
	     AND (SPR.CICLO != 'R1') AND (SPR.CICLO != 'R2') AND (SPR.CICLO != 'R3') AND (SPR.CICLO != 'R4') AND (SPR.CICLO != 'R5')
	     AND (SPR.CICLO != 'D1') AND (SPR.CICLO != 'D2') AND (SPR.CICLO != 'D3') AND (SPR.CICLO != 'D4') AND (SPR.CICLO != 'D5')
        )
        order by SOLICITUD ASC

	     
sql;

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesBusquedaRapida($cdgns)
    {

        $mysqli = new Database();

        $query = <<<sql
	    SELECT * FROM (

        SELECT ID_SCALL, CDGNS, CICLO, FECHA_SOL, INICIO, 
        CDGCO, NOMBRE_SUCURSAL, CODIGO_REGION, 
        REGION, ID_EJECUTIVO, 
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'SIN HISTORIAL' 
        ELSE ESTATUS_CL END AS ESTATUS_CL, 
        
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'SIN HISTORIAL' 
        ELSE ESTATUS_CL END AS ESTATUS_AV, 
            
        FECHA_TRABAJO,
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'SIN HISTORIAL' 
        WHEN CICLO = 'R1'  THEN 'SE CANCELO/RECHAZO 1 VEZ' 
        WHEN CICLO = 'R2'  THEN 'SE CANCELO/RECHAZO 2 VECES' 
        WHEN CICLO = 'R3'  THEN 'SE CANCELO/RECHAZO 3 VECES' 
        WHEN CICLO = 'R4'  THEN 'SE CANCELO/RECHAZO 4 VECES' 
        WHEN CICLO = 'R5'  THEN 'SE CANCELO/RECHAZO 5 VECES' 
        ELSE 'PENDIENTE NO SE HA INICIADO VALIDACIÓN DE CALLCENTER, LA ADMINISTRADORA REGISTRO LA SOLICITUD EL DÍA: ' || FECHA_SOL END AS ESTATUS_GENERAL, 
            
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'NO ESTA EN NINGUNA BANDEJA' 
        ELSE 'BANDEJA PENDIENTES - SUC: ' || NOMBRE_SUCURSAL END AS BANDEJA
        FROM SOLICITUDES_PENDIENTES SPE WHERE CDGNS = '$cdgns'
                                        
	    UNION 
	    
        SELECT ID_SCALL, CDGNS, CICLO, FECHA_SOL, INICIO, 
        SPR.CDGCO, NOMBRE_SUCURSAL, CODIGO_REGION , 
        REGION, ID_EJECUTIVO, ESTATUS_CL, ESTATUS_AV, FECHA_TRABAJO,
        CASE WHEN (ESTATUS_FINAL = 'PENDIENTE' OR ESTATUS_FINAL IS NULL) THEN 'PENDIENTE DE ASIGNAR UN ESTATUS'
        ELSE ESTATUS_FINAL END AS ESTATUS_GENERAL,
            
        CASE WHEN (ESTATUS_FINAL = 'PENDIENTE' OR ESTATUS_FINAL IS NULL) THEN 'BANDEJA PENDIENTES, VALIDANDO EL EJECUTIVO: ' || PE.NOMBRE1 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE
        ELSE 'BANDEJA HISTORICOS - VALIDO EJECUTIVO: ' || PE.NOMBRE1 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE || ' EL DÍA:'|| FECHA_TRABAJO END AS BANDEJA
        FROM SOLICITUDES_PROCESADAS SPR 
        INNER JOIN PE ON PE.CODIGO = SPR.CDGPE 
            WHERE CDGNS = '$cdgns') ORDER BY INICIO DESC
sql;

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesProrroga($cdgco)
    {


        $mysqli = new Database();

        $query = <<<sql
	    SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
	    WHERE SEMAFORO = '1' AND PRORROGA = '1'
sql;

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesReactivar($cdgco)
    {


        $mysqli = new Database();

        $query = <<<sql
	    SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
	    WHERE SEMAFORO = '1' AND REACTIVACION = '1'
sql;

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesConcentrado($Fecha, $Region)
    {

        if ($Region != '') {
            if ($Region != '0') {
                $Region_cond = "AND RG.CODIGO = '$Region'";
            } else {
                $Region_cond = '';
            }
            // $condicional = " AND SOL_CALL_CENTER.FECHA_SOL BETWEEN TO_DATE('$Fecha', 'YY-mm-dd') AND TO_DATE('$Fecha', 'YY-mm-dd') $Region_cond";
            $condicional = " $Region_cond";
        } else {
            $condicional = '';
        }


        $mysqli = new Database();
        $query = <<<sql
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

    public static function getAllAnalistas()
    {

        $mysqli = new Database();

        $query3 = <<<sql
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
sql;
        return $mysqli->queryAll($query3);
    }

    public static function getAllRegiones()
    {
        $mysqli = new Database();

        $query3 = <<<sql
        SELECT RG.NOMBRE AS REGION, CO.CODIGO, CO.NOMBRE FROM CO
        INNER JOIN RG ON RG.CODIGO = CO.CDGRG                                                          
        WHERE NOT EXISTS(SELECT CDGCO FROM ASIGNACION_SUC_A WHERE ASIGNACION_SUC_A.CDGCO = CO.CODIGO)
        ORDER BY CODIGO
sql;
        return $mysqli->queryAll($query3);
        /////
        ///
        ///
        /// SELECT CO.CODIGO, CO.NOMBRE  FROM CO
        //           WHERE NOT EXISTS(SELECT CDGCO FROM CIERRE_HORARIO WHERE CIERRE_HORARIO.CDGCO = CO.CODIGO)

    }

    public static function getAllAnalistasAsignadas()
    {

        $mysqli = new Database();

        $query3 = <<<sql
         SELECT ASIGNACION_SUC_A.CDGPE, ASIGNACION_SUC_A.CDGCO, CO.NOMBRE, ASIGNACION_SUC_A.FECHA_INICIO, 
                ASIGNACION_SUC_A.FECHA_FIN, ASIGNACION_SUC_A.FECHA_ALTA, ASIGNACION_SUC_A.CDGOCPE, 
                PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE AS NOMBRE_EJEC
           
        FROM
           ASIGNACION_SUC_A
        INNER JOIN CO ON CO.CODIGO = ASIGNACION_SUC_A.CDGCO
        INNER JOIN PE ON PE.CODIGO = ASIGNACION_SUC_A.CDGPE
        ORDER BY ASIGNACION_SUC_A.CDGPE ASC
        
               
sql;
        return $mysqli->queryAll($query3);
    }

    public static function UpdateResumen($encuesta)
    {
        $mysqli = new Database();

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET COMENTARIO_INICIAL='$encuesta->_comentarios_iniciales', COMENTARIO_FINAL='$encuesta->_comentarios_finales' , COMENTARIO_PRORROGA='$encuesta->_comentarios_prorroga'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function UpdateProrroga($prorroga)
    {
        $mysqli = new Database();

        if ($prorroga->_prorroga == '2') {
            $q = ", ESTATUS = NULL, SEMAFORO = NULL ";
        } else {
            $q = "";
        }

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET PRORROGA='$prorroga->_prorroga' $q
                WHERE ID_SCALL='$prorroga->_id_call'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function ReactivarSolicitud($reactivar)
    {
        $mysqli = new Database();

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET REACTIVACION = 1 
                WHERE ID_SCALL='$reactivar->_id_call'
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function ReactivarSolicitudAdmin($reactivar)
    {
        $mysqli = new Database();
        if ($reactivar->_opcion == 'SI') {
            $qu = " ,ESTATUS = NULL, SEMAFORO = NULL ";
        } else {
            $qu = "";
        }

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET REACTIVACION = '400' $qu
                WHERE ID_SCALL='$reactivar->_id_call'
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }


    public static function UpdateResumenFinal($encuesta)
    {
        $mysqli = new Database();

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET COMENTARIO_INICIAL='$encuesta->_comentarios_iniciales', COMENTARIO_FINAL='$encuesta->_comentarios_finales', SEMAFORO = '1', ESTATUS = '$encuesta->_estatus_solicitud', VOBO_GERENTE_REGIONAL = '$encuesta->_vobo_gerente', PRORROGA = '4', REACTIVACION = NULL  
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }


    public static function insertEncuestaCL($encuesta)
    {

        //var_dump($encuesta->_cdgpe);
        $mysqli = new Database();

        if ($encuesta->_completo == '1') {
            if ($encuesta->_llamada == '1') {
                //Agregar un registro completo (Bien) lLAMADA 1
                $query = <<<sql
            INSERT INTO SOL_CALL_CENTER
            (ID_SCALL, CDGRG, FECHA_TRA_CL, FECHA_SOL, CDGNS, CDGCO, CDGPE, CDGCL_CL, CICLO, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, INCIDENCIA_COMERCIAL, VOBO_GERENTE_REGIONAL, CDGPE_ANALISTA, SEMAFORO, LLAMADA_POST_VENTA, PRORROGA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV, REACTIVACION, COMENTARIO_PRORROGA)
            VALUES(sol_call_center_id.nextval, '$encuesta->_cdgre', TIMESTAMP '$encuesta->_fecha.000000', TIMESTAMP '$encuesta->_fecha_solicitud.000000', '$encuesta->_cdgns','$encuesta->_cdgco', '$encuesta->_cdgpe', '$encuesta->_cliente', '$encuesta->_ciclo', '$encuesta->_movil', '$encuesta->_tipo_llamada', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, '$encuesta->_uno', '$encuesta->_dos', '$encuesta->_tres', '$encuesta->_cuatro', '$encuesta->_cinco', '$encuesta->_seis', '$encuesta->_siete', '$encuesta->_ocho', '$encuesta->_nueve', '$encuesta->_diez', '$encuesta->_once', '$encuesta->_doce', '$encuesta->_id_aval_cl', '$encuesta->_telefono_aval_cl', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '1', NULL, NULL, NULL)
sql;
            } else { //Agregar un registro completo (Bien) lLAMADA 2
                $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_CL='$encuesta->_tipo_llamada', DIA_LLAMADA_2_CL=CURRENT_TIMESTAMP, HORA_LLAMADA_2_CL=CURRENT_TIMESTAMP, PRG_UNO_CL='$encuesta->_uno', PRG_DOS_CL='$encuesta->_dos', PRG_TRES_CL='$encuesta->_tres', PRG_CUATRO_CL='$encuesta->_cuatro', PRG_CINCO_CL='$encuesta->_cinco', PRG_SEIS_CL='$encuesta->_seis', PRG_SIETE_CL='$encuesta->_siete', PRG_OCHO_CL='$encuesta->_ocho', PRG_NUEVE_CL='$encuesta->_nueve', PRG_DIEZ_CL='$encuesta->_diez', PRG_ONCE_CL='$encuesta->_once', PRG_DOCE_CL='$encuesta->_doce',  CDGCL_AV='$encuesta->_id_aval_cl', TEL_AV='$encuesta->_telefono_aval_cl', FIN_CL='1', 
                NUMERO_INTENTOS_CL ='$encuesta->_llamada'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }
        } else if ($encuesta->_completo == '0') {
            if ($encuesta->_llamada == '1') {
                //Agregar un registro incompleto
                $query = <<<sql
                INSERT INTO SOL_CALL_CENTER
                (ID_SCALL, CDGRG, FECHA_TRA_CL, FECHA_SOL, CDGNS, CDGCO, CDGPE, CDGCL_CL, CICLO, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, INCIDENCIA_COMERCIAL, VOBO_GERENTE_REGIONAL, CDGPE_ANALISTA, SEMAFORO, LLAMADA_POST_VENTA, PRORROGA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV, REACTIVACION, COMENTARIO_PRORROGA)
                VALUES(sol_call_center_id.nextval, '$encuesta->_cdgre', TIMESTAMP '$encuesta->_fecha.000000', TIMESTAMP '$encuesta->_fecha_solicitud.000000', '$encuesta->_cdgns', '$encuesta->_cdgco', '$encuesta->_cdgpe', '$encuesta->_cliente', '$encuesta->_ciclo', '$encuesta->_movil', '$encuesta->_tipo_llamada', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, '$encuesta->_uno', '$encuesta->_dos', '$encuesta->_tres', '$encuesta->_cuatro', '$encuesta->_cinco', '$encuesta->_seis', '$encuesta->_siete', '$encuesta->_ocho', '$encuesta->_nueve', '$encuesta->_diez', '$encuesta->_once', '$encuesta->_doce', '$encuesta->_id_aval_cl', '$encuesta->_telefono_aval_cl', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL)
sql;
            } else {

                $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_CL='$encuesta->_tipo_llamada', DIA_LLAMADA_2_CL=CURRENT_TIMESTAMP, HORA_LLAMADA_2_CL=CURRENT_TIMESTAMP, PRG_UNO_CL='$encuesta->_uno', PRG_DOS_CL='$encuesta->_dos', PRG_TRES_CL='$encuesta->_tres', PRG_CUATRO_CL='$encuesta->_cuatro', PRG_CINCO_CL='$encuesta->_cinco', PRG_SEIS_CL='$encuesta->_seis', PRG_SIETE_CL='$encuesta->_siete', PRG_OCHO_CL='$encuesta->_ocho', PRG_NUEVE_CL='$encuesta->_nueve', PRG_DIEZ_CL='$encuesta->_diez', PRG_ONCE_CL='$encuesta->_once', PRG_DOCE_CL='$encuesta->_doce', CDGCL_AV='$encuesta->_id_aval_cl', TEL_AV='$encuesta->_telefono_aval_cl', FIN_CL=NULL,
                NUMERO_INTENTOS_CL ='$encuesta->_llamada'                                                                                                                                                                                                                                
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }
        }
        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function insertEncuestaAV($encuesta)
    {

        $mysqli = new Database();

        if ($encuesta->_completo == '1') {
            if ($encuesta->_llamada == '1') {
                //Agregar un registro completo (Bien) lLAMADA 1
                $query = <<<sql
            UPDATE SOL_CALL_CENTER
            SET FECHA_TRABAJO_AV= TIMESTAMP '2023-08-22 04:21:40.000000', 
            TIPO_LLAM_1_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_1_AV=CURRENT_TIMESTAMP, HORA_LLAMADA_1_AV=CURRENT_TIMESTAMP, 
            PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
            PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada', FIN_AV = '1'
            WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            } else { //Agregar un registro completo (Bien) lLAMADA 2
                $query = <<<sql
                UPDATE SOL_CALL_CENTER
            SET TIPO_LLAM_2_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_2_AV=CURRENT_TIMESTAMP, HORA_LLAMADA_2_AV=CURRENT_TIMESTAMP, 
            PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
            PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada', FIN_AV = '1'
            WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }
        } else if ($encuesta->_completo == '0') {
            if ($encuesta->_llamada == '1') {
                //Agregar un registro incompleto
                $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET FECHA_TRABAJO_AV= TIMESTAMP '2023-08-22 04:21:40.000000', TIPO_LLAM_1_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_1_AV=CURRENT_TIMESTAMP, HORA_LLAMADA_1_AV=CURRENT_TIMESTAMP, 
                PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
                PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            } else {

                $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET TIPO_LLAM_2_AV='$encuesta->_tipo_llamada', DIA_LLAMADA_2_AV=CURRENT_TIMESTAMP, HORA_LLAMADA_2_AV=CURRENT_TIMESTAMP,
                PRG_UNO_AV='$encuesta->_uno', PRG_DOS_AV='$encuesta->_dos', PRG_TRES_AV='$encuesta->_tres', PRG_CUATRO_AV='$encuesta->_cuatro', PRG_CINCO_AV='$encuesta->_cinco', PRG_SEIS_AV='$encuesta->_seis', 
                PRG_SIETE_AV='$encuesta->_siete', PRG_OCHO_AV='$encuesta->_ocho', PRG_NUEVE_AV='$encuesta->_nueve', NUMERO_INTENTOS_AV ='$encuesta->_llamada'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;
            }
        }
        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function DeleteAsignaSuc($cdgco)
    {

        $mysqli = new Database();
        $query = <<<sql
                DELETE FROM ASIGNACION_SUC_A
                WHERE CDGCO = '$cdgco'
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }




    public static function insertAsignaSucursal($asigna)
    {

        $mysqli = new Database();

        $query = <<<sql
            INSERT INTO ASIGNACION_SUC_A
            (ID_ASIGNACION, CDGEM, CDGPE, CDGCO, FECHA_INICIO, FECHA_FIN, FECHA_ALTA, CDGOCPE)
            VALUES(SUC_SUCURSALES.nextval, 'EMPFIN', '$asigna->_ejecutivo', '$asigna->_region', TIMESTAMP '$asigna->_fecha_registro', TIMESTAMP '$asigna->_fecha_inicio 00:00:00.000000', TIMESTAMP '$asigna->_fecha_fin 00:00:00.000000', 'AMGM')
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }
}
