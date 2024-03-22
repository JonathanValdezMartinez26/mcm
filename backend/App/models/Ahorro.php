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
            SELECT CODIGO, CDGTICKET_AHORRO, FREGISTRO, FREIMPRESION, MOTIVO, ESTATUS, CDGPE_SOLICITA, CDGPE_AUTORIZA, AUTORIZA, DESCRIPCION_MOTIVO, AUTORIZA_CLIENTE
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME

            
sql;
        }
        else{
            $query=<<<sql
            SELECT CODIGO, CDGTICKET_AHORRO, FREGISTRO, FREIMPRESION, MOTIVO, ESTATUS, CDGPE_SOLICITA, CDGPE_AUTORIZA, AUTORIZA, DESCRIPCION_MOTIVO, AUTORIZA_CLIENTE
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME
            WHERE CDGPE_SOLICITA = '$usuario' 
            ORDER BY FECHA DESC
sql;
        }


        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }


    public static function insertSolicitudAhorro($solicitud){

        $mysqli = Database::getInstance(1);

        //Agregar un registro
        $query=<<<sql
        INSERT INTO ESIACOM.TICKETS_AHORRO_REIMPRIME
        (CODIGO, CDGTICKET_AHORRO, FREGISTRO, FREIMPRESION, MOTIVO, ESTATUS, CDGPE_SOLICITA, CDGPE_AUTORIZA, AUTORIZA, DESCRIPCION_MOTIVO, FAUTORIZA, AUTORIZA_CLIENTE)
        VALUES('1', '1', TIMESTAMP '2024-03-15 15:21:09.000000', TIMESTAMP '2024-03-15 15:21:09.000000', 'MOTIVO 1', '1', '1', '1', '1', '1', TIMESTAMP '2024-03-15 15:21:09.000000', '1')
sql;

        return $mysqli->insert($query);
    }


}
