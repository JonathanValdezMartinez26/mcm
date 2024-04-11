<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Jobs
{
    public static function sp_con_array($parametros)
    {
        $db = Database::getInstance();
        $sp = "CALL ESIACOM.PKG_SPS_CON_ARRAY.SP_INS_CHEQUES_CTE(
            :PRMCDGEM,
            :PRMCDGCLNS,
            :PRMCLNS,
            :PRMCICLO,
            :PRMT_CDGCL,
            :PRMT_NOCHEQUE,
            :PRMFECHA,
            :PRMUSER,
            :PRMCDGCB,
            :VMENSAJE
        )";
        return $db->EjecutaSP($sp, $parametros);
    }

    // CONSULTA QUE DEVUELVE TODOS LOS CREDTOS AUTORIZADOS QUE ESTAN EN TESORERIA Y A LOS CUALES SE LES DEBE ASIGNAR UN CHEQUE
    public static function CreditosAutorizados()
    {
        $qry = <<<sql
        SELECT PRNN.CDGNS, PRNN.CICLO, PRNN.INICIO, PRNN.CDGCO 
        FROM PRN PRNN, PRC
        WHERE PRNN.INICIO=TIMESTAMP '2024-04-11 00:00:00.000000' AND PRNN.SITUACION = 'T'
        AND (SELECT COUNT(*) FROM PRN WHERE PRN.SITUACION = 'E' AND PRN.CDGNS = PRNN.CDGNS) = 0
        AND PRC.CDGNS = PRNN.CDGNS 
        AND PRC.NOCHEQUE IS NULL
sql;


        $db = Database::getInstance();
        return $db->queryAll($qry); //, [":fecha" => $fecha]
    }

    // SELECT CDGCL, CICLO, CANTAUTOR  FROM PRC WHERE CDGNS = '037034' AND CICLO = '01' AND NOCHEQUE IS NULL
    public static function ClientesAutorizados($cdgns, $ciclo)
    {
        $qry = <<<sql
        SELECT CDGCL FROM PRC WHERE CDGNS = :cdgns AND CICLO = :ciclo AND NOCHEQUE IS NULL
sql;
        $db = Database::getInstance();
        return $db->queryOne($qry, ["cdgns" => $cdgns, "ciclo" => $ciclo]);
    }

    // CONSULTAR LA CHEQUERA DE LA QUE SE VA A DESOMBOLSAR EL CREDITO
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

    // AHORA QUE YA SABEMOS QUE CHEQUERA LE CORRESPONDE DEBEMOS CONSULTAR EL CHEQUE CONSECUTIVO DE ESA CUENTA, PARA
    // BUSCAR ESE REGISTRO LO DEBEMOS HACER CON LA SIGUIENTE QUERY, EL PARAMETRO QUE MUESTRA COMO 28 = CDGCB DE LA CONSULTA ANTERIOR
    public static function GetNoCheque($chequera)
    {
        $qry = <<<sql
        SELECT FNSIGCHEQUE('EMPFIN', :chequera) CHQSIG FROM DUAL
sql;

        $db = Database::getInstance();
        return $db->queryOne($qry, ["chequera" => $chequera]);
    }
}
