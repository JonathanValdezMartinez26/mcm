<?php

namespace App\models;

include 'C:/xampp/htdocs/mcm/backend/Core/Database.php';

use \Core\Database;

class JobsCredito
{
    public static function CreditosAutorizados()
    {
        $qry = <<<sql
        SELECT
            PRC.CDGCL, PRNN.CDGNS, PRNN.CICLO, TO_CHAR(PRNN.INICIO, 'YYYY-MM-DD') AS INICIO, PRNN.CDGCO, PRNN.CANTAUTOR, TRUNC(SYSDATE) AS FEXP,
            (APagarInteresPrN('EMPFIN',PRNN.CDGNS,PRNN.CICLO, nvl(PRNN.CANTENTRE , PRNN.CANTAUTOR), PRNN.Tasa, PRNN.PLAZO, PRNN.PERIODICIDAD , PRNN.CDGMCI , 
            PRNN.INICIO, PRNN.DIAJUNTA , PRNN.MULTPER , PRNN.PERIGRCAP , PRNN.PERIGRINT ,  PRNN.DESFASEPAGO ,   PRNN.CDGTI) * -1) AS INTERES, 
            (APagarInteresPrN('EMPFIN',PRNN.CDGNS,PRNN.CICLO, nvl(PRNN.CANTENTRE , PRNN.CANTAUTOR), PRNN.Tasa, PRNN.PLAZO, PRNN.PERIODICIDAD , PRNN.CDGMCI , 
            PRNN.INICIO, PRNN.DIAJUNTA , PRNN.MULTPER , PRNN.PERIGRCAP , PRNN.PERIGRINT ,  PRNN.DESFASEPAGO ,   PRNN.CDGTI) * -1) AS PAGADOINT
        FROM
            PRN PRNN, PRC
        WHERE 
            PRNN.INICIO>TIMESTAMP '2024-04-11 00:00:00.000000' AND PRNN.SITUACION = 'T'
            AND (SELECT COUNT(*) FROM PRN WHERE PRN.SITUACION = 'E' AND PRN.CDGNS = PRNN.CDGNS) = 0
            AND PRC.CDGNS = PRNN.CDGNS 
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
            FEXP = SYSDATE,
            ACTUALIZACHPE = 'AMGM',
            SITUACION = 'E',
            CDGCB = :cdgcb,
            REPORTE = '   C',
            FEXPCHEQUE = SYSDATE,
            CANTENTRE = :cantautor,
            ENTRREAL = :cantautor
        WHERE
            CDGCL = :cdgcl
            AND CDGCLNS = :cdgns
            AND CICLO = :ciclo
sql;

        $parametros = [
            "cheque" => $datos["cheque"],
            "cdgcb" => $datos["cdgcb"],
            "cantautor" => $datos["cantautor"],
            "cdgcl" => $datos["cdgcl"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"],
        ];

        $db = Database::getInstance();
        return $db->insertar($qry, $parametros);
    }

    public static function ActualizaPRN($datos)
    {
        $qry = <<<sql
        UPDATE PRN SET
            REPORTE = '   C',
            FEXP = SYSDATE,
            ACTUALIZACHPE= 'AMGM',
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

        $parametros = [
            "cdgcb" => $datos["cdgcb"],
            "cantautor" => $datos["cantautor"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"]
        ];

        $db = Database::getInstance();
        return $db->insertar($qry, $parametros);
    }

    public static function LimpiarMPC($datos)
    {
        $qry = <<<sql
        DELETE FROM
            MPC
        WHERE
            CDGEM = 'EMPFIN'
            AND CDGCLNS = :prmCDGCLNS
            AND CLNS = 'G'
            AND CICLO = :prmCICLO
            AND FECHA = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
            AND TIPO in ('IN', 'GR', 'Co', 'GA')
            AND PERIODO = '00'
sql;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
    }

    public static function LimpiarJP($datos)
    {
        $qry = <<<sql
        DELETE FROM
            JP
        WHERE
            CDGEM = 'EMPFIN'
            AND CDGCLNS = :prmCDGCLNS
            AND CLNS = 'G'
            AND CICLO = :prmCICLO
            AND FECHA = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
            AND PERIODO = '00'
            AND TIPO in ('IN', 'GR', 'Co', 'GA')
sql;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
    }

    public static function LimpiarMP($datos)
    {
        $qry = <<<sql
        DELETE FROM
            MP
        WHERE
            CDGEM = 'EMPFIN'
            AND cdgclns = :prmCDGCLNS
            AND CLNS = 'G'
            AND ciclo = :prmCICLO
            AND frealdep = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
            AND TIPO IN ('IN', 'GR', 'Co', 'GA')
sql;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
    }

    public static function InsertarMP($datos)
    {
        $qry = <<<sql
        INSERT INTO
            MP (
                CDGEM,
                CDGCLNS,
                CLNS,
                CDGNS,
                CICLO,
                PERIODO,
                SECUENCIA,
                REFERENCIA,
                REFCIE,
                TIPO,
                FREALDEP,
                FDEPOSITO,
                CANTIDAD,
                MODO,
                CONCILIADO,
                ESTATUS,
                ACTUALIZARPE,
                PAGADOCAP,
                PAGADOINT,
                PAGADOREC
            )
        VALUES
            (
                'EMPFIN',
                :prmCDGCLNS,
                'G',
                :prmCDGNS,
                :prmCICLO,
                '0',
                '01',
                'Interés total del préstamo',
                'Interés total del préstamo',
                'IN',
                TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                :vINTERES,
                'G',
                'D',
                'B',
                'AMGM',
                0,
                :vINTERES,
                0
            )
sql;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCDGNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"]
        ];

        $db = Database::getInstance();
        return $db->insertCheques($qry, $parametros);
    }

    public static function InsertarJP($datos)
    {
        $qry = <<<sql
        INSERT INTO
            JP (
                CDGEM,
                CDGCLNS,
                CICLO,
                CLNS,
                FECHA,
                PERIODO,
                PAGOINFORME,
                PAGOFICHA,
                AHORRO,
                RETIRO,
                TIPO,
                CDGNS,
                TEXTO,
                CONCILIADO,
                ACTUALIZARPE,
                CONCBANINF,
                CONCBANFI,
                COINCIDEPAG
            )
        VALUES
            (
                'EMPFIN',
                :prmCDGCLNS,
                :prmCICLO,
                'G',
                TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                '00',
                :vINTERES,
                :vINTERES,
                0,
                0,
                'IN',
                :prmCDGCLNS,
                'Interés total del préstamo',
                'C',
                'AMGM',
                'S',
                'S',
                'S'
            )
sql;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"],
        ];

        $db = Database::getInstance();
        return $db->insertCheques($qry, $parametros);
    }

    public static function InsertarMPC($datos)
    {
        $qry = <<<sql
        INSERT INTO
            MPC (
                CDGEM,
                CDGCL,
                CICLO,
                CLNS,
                FECHA,
                TIPO,
                PERIODO,
                CDGCLNS,
                CDGNS,
                CANTIDAD
            )
        VALUES
            (
                'EMPFIN',
                :vCLIENTE,
                :prmCICLO,
                'G',
                TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                'IN',
                '00',
                :prmCDGCLNS,
                :prmCDGCLNS,
                :vINTERES
            )
sql;

        $parametros = [
            "vCLIENTE" => $datos["vCLIENTE"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "vINTERES" => $datos["vINTERES"]
        ];

        $db = Database::getInstance();
        return $db->insertCheques($qry, $parametros);
    }
}
