<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Ahorro
{

    public static function ConsultaClientes($cliente)
    {

        $query_valida_es_cliente_ahorro = <<<sql
            SELECT * FROM CL WHERE CODIGO = '$cliente'
        sql;

        $query_busca_cliente = <<<sql
            SELECT (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE) AS NOMBRE, CL.CURP, TO_CHAR(CL.REGISTRO ,'DD-MM-YYYY')AS REGISTRO, 
            TRUNC(MONTHS_BETWEEN(TO_DATE(SYSDATE,'dd-mm-yy'),CL.NACIMIENTO)/12)AS EDAD,  UPPER((CL.CALLE || ', ' || COL.NOMBRE|| ', ' || LO.NOMBRE || ', ' || MU.NOMBRE  || ', ' || EF.NOMBRE)) AS DIRECCION   
            FROM CL, COL, LO, MU,EF 
            WHERE EF.CODIGO = CL.CDGEF
            AND MU.CODIGO = CL.CDGMU
            AND LO.CODIGO = CL.CDGLO 
            AND COL.CODIGO = CL.CDGCOL
            AND EF.CODIGO = MU.CDGEF 
            AND EF.CODIGO = LO.CDGEF
            AND EF.CODIGO = COL.CDGEF
            AND MU.CODIGO = LO.CDGMU 
            AND MU.CODIGO = COL.CDGMU 
            AND LO.CODIGO = COL.CDGLO 
            AND CL.CODIGO = '$cliente'
        sql;


        $query_tiene_creditos = <<<sql
            SELECT * FROM CL WHERE CODIGO = '$cliente'
        sql;

        $query_es_aval = <<<sql
            SELECT * FROM CL WHERE CODIGO = '$cliente'
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query_busca_cliente);
        } catch (Exception $e) {
            return "";
        }
    }

    public static function GetTasaAnual()
    {
        $query_tasa = <<<sql
        SELECT
            CODIGO,
            TASA
        FROM
            TASA_AN_AHORRO
        WHERE
            ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query_tasa);
        } catch (Exception $e) {
            return "";
        }
    }

    public static function AgregaContratoAhorro($datos)
    {
        $noContrato = $datos['credito'] . date_format(date_create($datos['fecha']), 'Ymd');

        $qryAhorro = <<<sql
        INSERT INTO ASIGNA_PROD_AHORRO
            (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, BENEFICIARIO_1, CDGCT_PARENTESCO_1, BENEFICIARIO_2, CDGCT_PARENTESCO_2)
        VALUES
            ('$noContrato', (SELECT CODIGO FROM PRC WHERE CDGN = '{$datos['credito']}'), '{$datos['fecha']}', (SELECT MAX(CODIGO) FROM PR_SECUNDARIO), 'A', '{$datos['beneficiario_1']}', '{$datos['parentesco_1']}', '{$datos['beneficiario_2']}', '{$datos['parentesco_1']}')
        sql;

        $resDemo = [
            'contrato' => $noContrato,
            'ahorro' => $qryAhorro,
        ];
        return json_encode($resDemo);
    }

    public static function AgregaContratoAhorroKids($datos)
    {
        $noContrato = $datos['credito'] . date_format(date_create($datos['fecha']), 'Ymd');

        $qryProducto = <<<sql
        INSERT INTO ASIGNA_SUB_PRODUCTO
            (CDGCONTRATO, CDGPR_SECUNDARIO, FECHA_APERTURA, ESTATUS)
        VALUES
            ('$noContrato', (SELECT MAX(CODIGO) FROM PR_SECUNDARIO), '{$datos['fecha']}', 'A')
        sql;

        $resDemo = [
            'contrato' => '',
            'producto' => $qryProducto
        ];
        return json_encode($resDemo);
    }


    ////////////////////////////////////////////////////////////////////////////////////////
}
