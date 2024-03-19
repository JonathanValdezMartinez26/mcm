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


    public static function insertSolicitudAhorro($solicitud){

        $mysqli = Database::getInstance(1);

        //Agregar un registro
        $query=<<<sql
        INSERT INTO ESIACOM.TICKETS_AHORRO_REIMPRIME
        (CODIGO, FECHA, CDG_CONTRATO, MONTO, CDGPE)
        VALUES('6', TIMESTAMP '2024-03-15 15:21:09.000000', '00301120240311', 500, 'SOOA');
sql;

        return $mysqli->insert($query);
    }


}
