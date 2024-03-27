<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Ahorro
{
    public static function ConsultaTickets($usuario)
    {
        if($usuario == 'AMGM')
        {
            $query=<<<sql
             SELECT TICKETS_AHORRO.CODIGO, TICKETS_AHORRO.CDG_CONTRATO,
            TO_CHAR(TICKETS_AHORRO.FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ALTA, TICKETS_AHORRO.MONTO, TICKETS_AHORRO.CDGPE, (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE ) AS NOMBRE_CLIENTE,
            'CUENTA AHORRO' AS TIPO_AHORRO
            FROM TICKETS_AHORRO
            INNER JOIN ASIGNA_PROD_AHORRO ON ASIGNA_PROD_AHORRO.CONTRATO = TICKETS_AHORRO.CDG_CONTRATO 
            INNER JOIN CL ON CL.CODIGO = ASIGNA_PROD_AHORRO.CDGCL 
            ORDER BY FECHA DESC
sql;
        }
        else{
            $query=<<<sql
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


        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }

    public static function ConsultaSolicitudesTickets($usuario)
    {
        if($usuario == 'AMGM')
        {
            $query=<<<sql
            SELECT TAR.CODIGO, TAR.CDGTICKET_AHORRO, TAR.FREGISTRO, TAR.FREIMPRESION, TAR.MOTIVO, TAR.ESTATUS, TAR.CDGPE_SOLICITA, TAR.CDGPE_AUTORIZA, TAR.AUTORIZA, TAR.DESCRIPCION_MOTIVO, 
            TAR.AUTORIZA_CLIENTE, TA.CDG_CONTRATO 
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME TAR
            INNER JOIN TICKETS_AHORRO TA ON TA.CODIGO = TAR.CDGTICKET_AHORRO 
            
sql;
        }
        else{
            $query=<<<sql
            SELECT TAR.CODIGO, TAR.CDGTICKET_AHORRO, TAR.FREGISTRO, TAR.FREIMPRESION, TAR.MOTIVO, TAR.ESTATUS, TAR.CDGPE_SOLICITA, TAR.CDGPE_AUTORIZA, TAR.AUTORIZA, TAR.DESCRIPCION_MOTIVO, 
            TAR.AUTORIZA_CLIENTE, TA.CDG_CONTRATO 
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME TAR
            INNER JOIN TICKETS_AHORRO TA ON TA.CODIGO = TAR.CDGTICKET_AHORRO 
            WHERE TAR.CDGPE_SOLICITA = '$usuario' 
            ORDER BY TAR.FECHA DESC
sql;
        }


        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }


    public static function insertSolicitudAhorro($solicitud){

        $query_consulta_existe_sol=<<<sql
            SELECT COUNT(*) AS EXISTE
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME
            WHERE CDGPE_SOLICITA = '$solicitud->_cdgpe' 
            AND CDGTICKET_AHORRO = '$solicitud->_folio'
            AND ESTATUS = '0'
            AND AUTORIZA = '0'
sql;


        $mysqli = Database::getInstance(1);
        $res = $mysqli->queryOne($query_consulta_existe_sol);


        if($res['EXISTE'] == 0)
        {
            //Agregar un registro
            $query=<<<sql
        INSERT INTO ESIACOM.TICKETS_AHORRO_REIMPRIME
        (CODIGO, CDGTICKET_AHORRO, FREGISTRO, FREIMPRESION, MOTIVO, ESTATUS, CDGPE_SOLICITA, CDGPE_AUTORIZA, AUTORIZA, DESCRIPCION_MOTIVO, FAUTORIZA, AUTORIZA_CLIENTE)
        VALUES('1', '$solicitud->_folio', TIMESTAMP '2024-03-15 15:21:09.000000', TIMESTAMP '2024-03-15 15:21:09.000000', '$solicitud->_motivo', '1', '$solicitud->_cdgpe', '', '0', '$solicitud->_descripcion', TIMESTAMP '2024-03-15 15:21:09.000000', '0')
sql;

            return $mysqli->insert($query);
        }
        else
        {
            echo "Ya solicitaste la reimpresión de este ticket, espere a su validacion o contacta a tesorería.";
        }





    }


    public static function ConsultaMovimientosDia($fecha)
    {
        if($fecha == 'AMGM')
        {
            $query=<<<sql
            SELECT TAR.CODIGO, TAR.CDGTICKET_AHORRO, TAR.FREGISTRO, TAR.FREIMPRESION, TAR.MOTIVO, TAR.ESTATUS, TAR.CDGPE_SOLICITA, TAR.CDGPE_AUTORIZA, TAR.AUTORIZA, TAR.DESCRIPCION_MOTIVO, 
            TAR.AUTORIZA_CLIENTE, TA.CDG_CONTRATO 
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME TAR
            INNER JOIN TICKETS_AHORRO TA ON TA.CODIGO = TAR.CDGTICKET_AHORRO 
            
sql;
        }
        else{
            $query=<<<sql
            SELECT * FROM MOVIMIENTOS_AHORRO
sql;
        }


        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }


}
