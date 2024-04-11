<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Jobs
{
    public static function CreditosAutorizados($fecha)
    {
        $datoCredito = [
            "fecha" => $datos["INICIO"],
            "usuario" => $usuario,
            "cdgcb" => $datos["CDGCB"],
            "cdgcl" => $datos["CDGCL"],
            "cdgclns" => $datos["CDGNS"],
            "ciclo" => $datos["CICLO"]
        ];

        $datosChequera = [
            "fecha" => $datos["INICIO"],
            "usuario" => $usuario,
            "cdgcb" => $datos["CDGCB"],
            "cdgns" => $datos["CDGNS"],
            "ciclo" => $datos["CICLO"]
        ];
        $qry = <<<sql
        SELECT
            PRC.CDGCL,
            PRN.CDGNS,
            PRN.CICLO,
            PRN.INICIO,
            PRN.CDGCO,
            PRN.CANTAUTOR,
            PRN.FEXP,
            CHEQ.CDGCB,
            CHEQ.CDGCO,
            CHEQ.CODIGO,
            CHEQ.CHEQUEINICIAL,
            CHEQ.CHEQUEFINAL
        FROM
            PRN
        INNER JOIN
            PRC ON PRC.CDGNS = PRN.CDGNS
        LEFT JOIN
            CHEQUERA CHEQ ON CHEQ.CDGCO = PRN.CDGCO
        WHERE
            PRN.INICIO = :fecha
            AND PRN.SITUACION = 'T'
            AND (
                SELECT
                    COUNT(*)
                FROM
                    PRN P
                WHERE
                    PRN.SITUACION = 'E'
                    AND P.CDGNS = PRN.CDGNS
            ) = 0
            AND CHEQ.CODIGO = (
                SELECT
                    MAX(TO_NUMBER(CODIGO))
                FROM
                    CHEQUERA
                WHERE
                    CDGCO = PRN.CDGCO
            )
            AND CHEQ.CDGCO = PRN.CDGCO
            AND PRC.NOCHEQUE IS NULL
        sql;

        $db = Database::getInstance();
        return $db->queryAll($qry, ["fecha" => $fecha]);
    }

    public static function ActualizaCheques($datos)
    {
        $qryCredito = <<<sql
        UPDATE PRC
        SET
            NOCHEQUE = (SELECT FNSIGCHEQUE('EMPFIN', :cdgcb) FROM DUAL),
            FEXP = :fecha,
            ACTUALIZACHPE = :usuario,
            SITUACION = 'T',
            CDGCB = :cdgcb,
            REPORTE = '   C',
            FEXPCHEQUE = SYSDATE
        WHERE
            CDGCL = :cdgcl
            AND CDGCLNS = :cdgclns
            AND CICLO = :ciclo
        sql;

        $qryChequera = <<<sql
        UPDATE PRN
        SET
            REPORTE = '   C',
            FEXP = :fecha,
            ACTUALIZACHPE= :usuario,
            SITUACION = 'T',
            CDGCB = :cdgcb,
        WHERE
            CDGNS = :cdgns
            AND CICLO = :ciclo
        sql;

        $usuario = $_SESSION["usuario"] ?? "AMGM";
        $datoCredito = [
            "fecha" => $datos["INICIO"],
            "usuario" => $usuario,
            "cdgcb" => $datos["CDGCB"],
            "cdgcl" => $datos["CDGCL"],
            "cdgclns" => $datos["CDGNS"],
            "ciclo" => $datos["CICLO"]
        ];

        $datosChequera = [
            "fecha" => $datos["INICIO"],
            "usuario" => $usuario,
            "cdgcb" => $datos["CDGCB"],
            "cdgns" => $datos["CDGNS"],
            "ciclo" => $datos["CICLO"]
        ];

        $qrys = [$qryCredito, $qryChequera];
        $datos = [$datoCredito, $datosChequera];
        $db = Database::getInstance();
        $db->insertaMultiple($qrys, $datos);
    }
}
