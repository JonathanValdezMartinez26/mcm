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
            SELECT CODIGO, CDG_CONTRATO,
            TO_CHAR(FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ALTA, MONTO, CDGPE
            FROM TICKETS_AHORRO
            ORDER BY FECHA DESC
sql;
        }
        else{
            $query=<<<sql
            SELECT CODIGO, CDG_CONTRATO,
            TO_CHAR(FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ALTA, MONTO, CDGPE
            FROM TICKETS_AHORRO
            WHERE CDGPE = '$usuario' 
            ORDER BY FECHA DESC
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
