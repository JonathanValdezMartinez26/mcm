<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Ahorro
{

    public static function ConcultaClientes($cliente)
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

    public static function AgregaContrato($datos)
    {
        $noContrato = $datos['credito'] . date_format(date_create($datos['fecha']), 'Ymd');

        $qtyTasa = <<<sql
        INSERT INTO TASA_AN_AHORRO
            (CODIGO, DESCRIPCION, TASA, REGISTRO, ESTATUS)
        VALUES
            ((SELECT MAX(CODIGO) FROM TASA_AN_AHORRO) + 1, 'Tasa Ahorro Beneficiario 1', '{$datos['tasa']}', '{$datos['fecha']}', 'A')
        sql;

        $qryPrioritario = <<<sql
        INSERT INTO PR_PRIORITARIO
            (CODIGO, DESCRIPCION, COSTO_INSCRIPCION, COSTO_GASTOS_ADMIN, ESTATUS, CDGTAAH)
        VALUES
            ((SELECT MAX(CODIGO) FROM PR_PRIORITARIO) + 1, 'PRODUCTO PRIORITARIO 1', '0', '0', 'A', (SELECT MAX(CODIGO) FROM TASA_AN_AHORRO))
        sql;

        $qrySecundario = <<<sql
        INSERT INTO PR_SECUNDARIO
            (CODIGO, DESCRIPCION, COSTO_INSCRIPCION, COSTO_GASTOS_ADMIN, ESTATUS)
        VALUES
            ((SELECT MAX(CODIGO) FROM PR_SECUNDARIO) + 1, 'PRODUCTO SECUNDARIO 1', '0', '0', 'A')
        sql;

        $qryAhorro = <<<sql
        INSERT INTO ASIGNA_PROD_AHORRO
            (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, BENEFICIARIO_1, CDGCT_PARENTESCO_1, BENEFICIARIO_2, CDGCT_PARENTESCO_2)
        VALUES
            ('$noContrato', (SELECT CODIGO FROM PRC WHERE CDGN = '{$datos['credito']}'), '{$datos['fecha']}', (SELECT MAX(CODIGO) FROM PR_SECUNDARIO), 'A', '{$datos['beneficiario_1']}', '{$datos['parentesco_1']}', '{$datos['beneficiario_2']}', '{$datos['parentesco_1']}')
        sql;

        $qryProducto = <<<sql
        INSERT INTO ASIGNA_SUB_PRODUCTO
            (CDGCONTRATO, CDGPR_SECUNDARIO, FECHA_APERTURA, ESTATUS)
        VALUES
            ('$noContrato', (SELECT MAX(CODIGO) FROM PR_SECUNDARIO), '{$datos['fecha']}', 'A')
        sql;

        $resDemo = [
            'contrato' => $noContrato,
            'tasa' => $qtyTasa,
            'prioritario' => $qryPrioritario,
            'secundario' => $qrySecundario,
            'ahorro' => $qryAhorro,
            'producto' => $qryProducto
        ];
        return json_encode($resDemo);
    }


    ////////////////////////////////////////////////////////////////////////////////////////
}
