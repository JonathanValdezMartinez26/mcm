<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class Tesoreria extends Model
{
    static public function GetReportePC($datos)
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(SN.SOLICITUD, 'DD/MM/YYYY') FECHA_SOLICITUD
                , SN.CDGNS CREDITO
                , SN.CICLO
                , SC.CDGCL CLIENTE
                , GET_NOMBRE_CLIENTE(SC.CDGCL) NOMBRE_CLIENTE
                , SC.RFC
                , TO_CHAR(SN.INICIO, 'DD/MM/YYYY') FECHA_INICIO
                , GET_DATOS_TRANSFERENCIA(SN.CDGEM, SN.CDGNS, SN.CICLO, 'MEDIO') TIPO_OPERACION
                , SN.CDGCO || ' - ' || GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL
                , CO.CDGRG || ' - ' || GET_NOMBRE_REGION(CO.CDGRG) REGION
                , SC.CANTSOLIC MONTO
                , GET_DATOS_TRANSFERENCIA(SN.CDGEM, SN.CDGNS, SN.CICLO, 'BANCO') BANCO
                , GET_DATOS_TRANSFERENCIA(SN.CDGEM, SN.CDGNS, SN.CICLO, 'CLABE') CLABE
            FROM
                SN
                JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO AND SC.SOLICITUD = SN.SOLICITUD AND SC.CANTSOLIC <> 9999
                JOIN CL ON CL.CODIGO = SC.CDGCL
                JOIN CO ON CO.CODIGO = SN.CDGCO
            WHERE
                SN.INICIO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                AND SN.SITUACION IN ('S', 'A')
                AND GET_DATOS_TRANSFERENCIA(SN.CDGEM, SN.CDGNS, SN.CICLO, 'ES_TRANSFERENCIA') = 1
        SQL;

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        if (isset($datos['sucursal']) && $datos['sucursal'] != '' && $datos['sucursal'] != '*') {
            $qry .= ' AND SN.CDGCO = :sucursal';
            $prm['sucursal'] = $datos['sucursal'];
        }

        try {
            $db =  new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Consulta exitosa', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al consultar el reporte', null, $e->getMessage());
        }
    }
}
