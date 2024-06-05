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
            PRC.CDGCL, PRNN.CDGNS, PRNN.CICLO, PRNN.INICIO, PRNN.CDGCO, PRNN.CANTAUTOR, TRUNC(SYSDATE) AS FEXP  
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

        $parametros = [
            "cheque" => $datos["cheque"],
            "fexp" => $datos["fexp"],
            "usuario" => $datos["usuario"],
            "cdgcb" => $datos["cdgcb"],
            "cdgcl" => $datos["cdgcl"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"],
            "cantautor" => $datos["cantautor"]
        ];

        $db = Database::getInstance();
        return $db->insertar($qry, $parametros);
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

        $parametros = [
            "fexp" => $datos["fexp"],
            "usuario" => $datos["usuario"],
            "cdgcb" => $datos["cdgcb"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"],
            "cantautor" => $datos["cantautor"]
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
            CDGEM = :prmCDGEM
            AND CDGCLNS = :prmCDGCLNS
            AND CLNS = :prmCLNS
            AND CICLO = :prmCICLO
            AND FECHA = :prmINICIO
            AND TIPO in ('IN', 'GR', 'Co', 'GA')
            AND PERIODO = '00'
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCLNS" => $datos["prmCLNS"],
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
            CDGEM = :prmCDGEM
            AND CDGCLNS = :prmCDGCLNS
            AND CLNS = :prmCLNS
            AND CICLO = :prmCICLO
            AND FECHA = :prmINICIO
            AND PERIODO = '00'
            AND TIPO in ('IN', 'GR', 'Co', 'GA')
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCLNS" => $datos["prmCLNS"],
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
            CDGEM = :prmCDGEM
            AND cdgclns = :prmCDGCLNS
            AND CLNS = :prmCLNS
            AND ciclo = :prmCICLO
            AND frealdep = :prmINICIO
            AND TIPO IN ('IN', 'GR', 'Co', 'GA')
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCLNS" => $datos["prmCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
    }

    public static function GET_vINTCTE($datos)
    {
        $qry = <<<sql
        SELECT
            (
                round(
                decode(
                    nvl(PRN.periodicidad, ''),
                    'S',
                    (
                    nvl(PRN.tasa, 0) * nvl(PRN.plazo, 0) * nvl(PRC.cantentre, 0)
                    ) /(4 * 100),
                    'Q',
                    (
                    nvl(PRN.tasa, 0) * nvl(PRN.plazo, 0) * nvl(PRC.cantentre, 0) * 15
                    ) /(30 * 100),
                    'C',
                    (
                    nvl(PRN.tasa, 0) * nvl(PRN.plazo, 0) * nvl(PRC.cantentre, 0)
                    ) /(2 * 100),
                    'M',
                    (
                    nvl(PRN.tasa, 0) * nvl(PRN.plazo, 0) * nvl(PRC.cantentre, 0)
                    ) /(100),
                    '',
                    ''
                ),
                0
                ) * -1
            ) as VINTCTE
        FROM
            PRN,
            PRC
        WHERE
            PRN.CDGEM = PRC.CDGEM
            AND PRN.CDGNS = PRC.CDGNS
            AND PRN.CICLO = PRC.CICLO
            AND PRN.CDGEM = :prmCDGEM
            AND PRN.CDGNS = :prmCDGCLNS
            AND PRN.CICLO = :prmCICLO
            AND PRC.CDGCL = :prmCLNS
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmCLNS" => $datos["prmCLNS"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
    }

    public static function GET_vINTERES($datos)
    {
        $qry = <<<sql
        SELECT
            (
                APagarInteresPrN(
                PrN.CdgEm,
                PrN.CdgNS,
                PrN.Ciclo,
                nvl(PrN.CantEntre, prn.cantautor),
                PrN.Tasa,
                PrN.PLAZO,
                PrN.Periodicidad,
                PrN.CdgMCI,
                PrN.Inicio,
                PrN.DiaJunta,
                PrN.MultPer,
                PrN.PeriGrCap,
                PrN.PeriGrInt,
                PrN.DesfasePago,
                PrN.CdgTI
                ) * -1
            ) as VINTERES
        FROM
            prn
        WHERE
            cdgem = :prmCDGEM
            AND cdgns = :prmCDGCLNS
            AND ciclo = :prmCICLO
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"]
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
                CICLO,
                CLNS,
                FREALDEP,
                FDEPOSITO,
                PERIODO,
                SECUENCIA,
                TIPO,
                CANTIDAD,
                CONCILIADO,
                ESTATUS,
                pagadocap,
                PAGADOINT,
                pagadorec,
                MODO,
                REFERENCIA,
                REFCIE,
                CDGNS,
                ACTUALIZARPE
            )
        VALUES
            (
                :prmCDGEM,
                :prmCDGCLNS,
                :prmCICLO,
                :prmCLNS,
                :prmINICIO,
                :prmINICIO,
                '00',
                '01',
                'IN',
                :vINTERES,
                'D',
                'B',
                0,
                :vINTERES,
                0,
                'G',
                'Interés total del préstamo',
                'Interés total del préstamo',
                :vCDGNS,
                :prmUSUARIO
            )
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCLNS" => $datos["prmCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"],
            "vCDGNS" => $datos["vCDGNS"],
            "prmUSUARIO" => $datos["prmUSUARIO"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
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
                texto,
                CONCILIADO,
                ACTUALIZARPE,
                CONCBANINF,
                CONCBANFI,
                COINCIDEPAG
            )
        VALUES
            (
                :prmCDGEM,
                :prmCDGCLNS,
                :prmCICLO,
                :prmCLNS,
                :prmINICIO,
                '00',
                :vINTERES,
                :vINTERES,
                0,
                0,
                'IN',
                :vCDGNS,
                'Interés total del préstamo',
                'C',
                :prmUSUARIO,
                'S',
                'S',
                'S'
            )
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCLNS" => $datos["prmCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"],
            "vCDGNS" => $datos["vCDGNS"],
            "prmUSUARIO" => $datos["prmUSUARIO"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
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
                :prmCDGEM,
                :vCLIENTE,
                :prmCICLO,
                :prmCLNS,
                :prmINICIO,
                'IN',
                '00',
                :prmCDGCLNS,
                :vCDGNS,
                :vINTCTE
            )
        sql;

        $parametros = [
            "prmCDGEM" => $datos["prmCDGEM"],
            "vCLIENTE" => $datos["vCLIENTE"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmCLNS" => $datos["prmCLNS"],
            "prmINICIO" => $datos["prmINICIO"],
            "vCDGNS" => $datos["vCDGNS"],
            "vINTCTE" => $datos["vINTCTE"]
        ];

        $db = Database::getInstance();
        return $db->queryOne($qry, $parametros);
    }
}
