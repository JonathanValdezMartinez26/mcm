<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class Ahorro
{
    public static function ConsultaTickets($usuario)
    {
        if ($usuario == 'AMGM') {
            $query = <<<sql
             SELECT TICKETS_AHORRO.CODIGO, TICKETS_AHORRO.CDG_CONTRATO,
            TO_CHAR(TICKETS_AHORRO.FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ALTA, TICKETS_AHORRO.MONTO, TICKETS_AHORRO.CDGPE, (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE ) AS NOMBRE_CLIENTE,
            'CUENTA AHORRO' AS TIPO_AHORRO
            FROM TICKETS_AHORRO
            INNER JOIN ASIGNA_PROD_AHORRO ON ASIGNA_PROD_AHORRO.CONTRATO = TICKETS_AHORRO.CDG_CONTRATO 
            INNER JOIN CL ON CL.CODIGO = ASIGNA_PROD_AHORRO.CDGCL 
            ORDER BY FECHA DESC
sql;
        } else {
            $query = <<<sql
             SELECT TICKETS_AHORRO.CODIGO, TICKETS_AHORRO.CDG_CONTRATO,
            TO_CHAR(TICKETS_AHORRO.FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ALTA, TICKETS_AHORRO.MONTO, TICKETS_AHORRO.CDGPE, (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE ) AS NOMBRE_CLIENTE,
              'CUENTA AHORRO' AS TIPO_AHORRO 
            FROM TICKETS_AHORRO
            INNER JOIN ASIGNA_PROD_AHORRO ON ASIGNA_PROD_AHORRO.CONTRATO = TICKETS_AHORRO.CDG_CONTRATO 
            INNER JOIN CL ON CL.CODIGO = ASIGNA_PROD_AHORRO.CDGCL 
            WHERE TICKETS_AHORRO.CDGPE = '$usuario' 
            ORDER BY TICKETS_AHORRO.FECHA DESC
sql;
        }


        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function ConsultaSolicitudesTickets($datos)
    {
        $query = <<<sql
        SELECT TAR.CODIGO, TAR.CDGTICKET_AHORRO, TAR.FREGISTRO, TAR.FREIMPRESION, TAR.MOTIVO, TAR.ESTATUS, TAR.CDGPE_SOLICITA, TAR.CDGPE_AUTORIZA, TAR.AUTORIZA, TAR.DESCRIPCION_MOTIVO, 
        TAR.AUTORIZA_CLIENTE, TA.CDG_CONTRATO 
        FROM ESIACOM.TICKETS_AHORRO_REIMPRIME TAR
        INNER JOIN TICKETS_AHORRO TA ON TA.CODIGO = TAR.CDGTICKET_AHORRO 
        sql;

        $filtros = [];
        if ($datos['usuario'] != 'AMGM') $filtros[] = "TAR.CDGPE_SOLICITA = '{$datos['usuario']}'";
        if ($datos['estatus']) $filtros[] = "TAR.ESTATUS = '{$datos['estatus']}'";
        if ($datos['fechaI'] && $datos['fechaF']) $filtros[] = "TRUNC(TAR.FREGISTRO) BETWEEN TO_DATE('{$datos['fechaI']}', 'YYYY-MM-DD') AND TO_DATE('{$datos['fechaF']}', 'YYYY-MM-DD')";

        if (count($filtros) > 0) $query .= " WHERE " . implode(" AND ", $filtros);
        $query .= " ORDER BY TAR.FREGISTRO DESC";

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function insertSolicitudAhorro($solicitud)
    {

        $query_consulta_existe_sol = <<<sql
            SELECT COUNT(*) AS EXISTE
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME
            WHERE CDGPE_SOLICITA = '$solicitud->_cdgpe' 
            AND CDGTICKET_AHORRO = '$solicitud->_folio'
            AND ESTATUS = '0'
            AND AUTORIZA = '0'
sql;

        //var_dump($query_consulta_existe_sol);

        $mysqli = new Database();
        $res = $mysqli->queryOne($query_consulta_existe_sol);



        if ($res['EXISTE'] == 0) {
            //Agregar un registro
            $query = <<<sql
        INSERT INTO ESIACOM.TICKETS_AHORRO_REIMPRIME
        (CODIGO, CDGTICKET_AHORRO, FREGISTRO, FREIMPRESION, MOTIVO, ESTATUS, CDGPE_SOLICITA, CDGPE_AUTORIZA, AUTORIZA, DESCRIPCION_MOTIVO, FAUTORIZA, AUTORIZA_CLIENTE)
        VALUES(SEC_TICKET_REIMPRIME.NEXTVAL, '$solicitud->_folio', CURRENT_TIMESTAMP, '', '$solicitud->_motivo', '0', '$solicitud->_cdgpe', '', '0', '$solicitud->_descripcion', NULL , '0')
sql;

            return $mysqli->insert($query);
        } else {
            echo "Ya solicito la reimpresión de este ticket, espere a su validacion o contacte a tesorería.";
        }
    }


    public static function ConsultaMovimientosDia($fecha)
    {
        if ($fecha == 'AMGM') {
            $query = <<<sql
            SELECT TAR.CODIGO, TAR.CDGTICKET_AHORRO, TAR.FREGISTRO, TAR.FREIMPRESION, TAR.MOTIVO, TAR.ESTATUS, TAR.CDGPE_SOLICITA, TAR.CDGPE_AUTORIZA, TAR.AUTORIZA, TAR.DESCRIPCION_MOTIVO, 
            TAR.AUTORIZA_CLIENTE, TA.CDG_CONTRATO 
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME TAR
            INNER JOIN TICKETS_AHORRO TA ON TA.CODIGO = TAR.CDGTICKET_AHORRO 
            
sql;
        } else {
            $query = <<<sql
            SELECT * FROM MOVIMIENTOS_AHORRO
sql;
        }


        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }
}
