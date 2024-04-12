<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Jobs
{
    public static function CreditosAutorizados()
    {
        $qry = <<<sql
        SELECT PRC.CDGCL, PRNN.CDGNS, PRNN.CICLO, PRNN.INICIO, PRNN.CDGCO, PRNN.CANTAUTOR, TRUNC(SYSDATE) AS FEXP  
        FROM PRN PRNN, PRC
        WHERE PRNN.INICIO > '11/04/2024' AND PRNN.SITUACION = 'T'
        AND (SELECT COUNT(*) FROM PRN WHERE PRN.SITUACION = 'E' AND PRN.CDGNS = PRNN.CDGNS) = 0
        AND PRC.CDGNS = PRNN.CDGNS 
        AND PRC.CICLO = PRNN.CICLO
        AND PRC.NOCHEQUE IS NULL
sql;

        $db = Database::getInstance();
        return $db->queryAll($qry);
    }

    public static function GetNoChequera($cdgco)
    {
        $qry = <<<sql
        SELECT CDGCB, CDGCO, CODIGO, CHEQUEINICIAL, CHEQUEFINAL  
        FROM CHEQUERA
        WHERE TO_NUMBER(CODIGO) = (SELECT MAX(TO_NUMBER(CODIGO)) AS int_column FROM CHEQUERA WHERE CDGCO = :cdgco)
        AND CDGCO = :cdgco
sql;

        $db = Database::getInstance();
        return $db->queryOne($qry, ["cdgco" => $cdgco]);
    }

    public static function GetNoCheque($chequera)
    {
        $qry = <<<sql
        SELECT FNSIGCHEQUE('EMPFIN', :chequera) CHQSIG FROM DUAL
sql;

        $db = Database::getInstance();
        return $db->queryOne($qry, ["chequera" => $chequera]);
    }

    public static function ActualizaPRC($datos)
    {
        $qry = <<<sql
        UPDATE PRC SET
            NOCHEQUE = LPAD(:cheque,7,'0'),
            FEXP = :fexp,
            ACTUALIZACHPE = :usuario,
            SITUACION = 'E',
            CDGCB = :cdgcb,
            REPORTE = '   C',
            FEXPCHEQUE = :fexp,
            CANTENTRE = :cantautor,
            ENTRREAL = :cantautor
        WHERE
            CDGCL = :cdgcl
            AND CDGCLNS = :cdgns
            AND CICLO = :ciclo
sql;

        $db = Database::getInstance();
        return $db->insertar($qry, [
            "cheque" => $datos["cheque"],
            "fexp" => $datos["fexp"],
            "usuario" => $datos["usuario"],
            "cdgcb" => $datos["cdgcb"],
            "cdgcl" => $datos["cdgcl"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"],
            "cantautor" => $datos["cantautor"]
        ]);
    }

    public static function ActualizaPRN($datos)
    {
        $qry = <<<sql
        UPDATE PRN SET
            REPORTE = '   C',
            FEXP = :fexp,
            ACTUALIZACHPE= :usuario,
            SITUACION = 'E',
            CDGCB = :cdgcb,
            CANTENTRE = :cantautor,
            ACTUALIZAENPE = 'AMGM',
            ACTUALIZACPE = 'AMGM',
            FCOMITE = SYSDATE
        WHERE
            CDGNS = :cdgns
            AND CICLO = :ciclo
sql;

        $db = Database::getInstance();
        return $db->insertar($qry, [
            "fexp" => $datos["fexp"],
            "usuario" => $datos["usuario"],
            "cdgcb" => $datos["cdgcb"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"],
            "cantautor" => $datos["cantautor"]
        ]);
    }

    public static function CreditosPendientes()
    {
        $qry = <<<sql
        SELECT
            *
        FROM
            PRN
        LEFT JOIN
            PRC
        ON
            PRN.CDGNS = PRC.CDGNS
            AND PRN.CICLO = PRC.CICLO
        WHERE
            PRN.INICIO > '11/04/2024' AND PRN.ACTUALIZACHPE = 'AMGM'
sql;

        $db = Database::getInstance();
        return $db->queryAll($qry);
    }
}
