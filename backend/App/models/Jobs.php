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
}
