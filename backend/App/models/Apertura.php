<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Apertura
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = array(
            "success" => $respuesta,
            "mensaje" => $mensaje
        );

        if ($datos != null) $res['datos'] = $datos;
        if ($error != null) $res['error'] = $error;

        return json_encode($res);
    }

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
            (:contrato, :cliente, :fecha_apertura, '1', 'A', :beneficiario1, :parentesco1, :beneficiario2, :parentesco2)
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

    public static function AddPagoApertura($datos)
    {
        $error = null;

        if ($datos['deposito_inicial'] == 0) return self::Responde(false, "El monto de apertura no puede ser de 0");
        if ($datos['saldo_inicial'] < $datos['sma']) return self::Responde(false, "El saldo inicial no puede ser menor a " . $datos['sma']);

        $qryTicket = <<<sql
        INSERT INTO TICKETS_AHORRO
            (CODIGO, FECHA, CDG_CONTRATO, MONTO, CDGPE)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM TICKETS_AHORRO) + 1, SYSDATE, :contrato, :monto, :ejecutivo)
        sql;

        $datosTicket = [
            'contrato' => $datos['contrato'],
            'monto' => $datos['deposito_inicial'],
            'ejecutivo' => $datos['ejecutivo']
        ];

        try {
            $mysqli = Database::getInstance();
            $ticket = $mysqli->insertar($qryTicket, $datosTicket, true);
            if (!$ticket) return self::Responde(false, "Ocurrió un error al crear el ticket de ahorro", $datos, $datosTicket);

            $qryPago = <<<sql
            INSERT INTO MOVIMIENTOS_AHORRO
                (CODIGO, FECHA_MOV, CDG_TIPO_PAGO, CDG_CONTRATO, MONTO, MOVIMIENTO, DESCRIPCION, CDG_TICKET)
            VALUES
                ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM MOVIMIENTOS_AHORRO) + 1, SYSDATE, :tipo_pago, :contrato, :monto, :movimiento, 'ALGUNA_DESCRIPCION', (SELECT MAX(TO_NUMBER(CODIGO)) FROM TICKETS_AHORRO))
            sql;

            $registros = [
                [
                    'tipo_pago' => '1',
                    'contrato' => $datos['contrato'],
                    'monto' => $datos['inscripcion'],
                    'movimiento' => '0'
                ],
                [
                    'tipo_pago' => '2',
                    'contrato' => $datos['contrato'],
                    'monto' => $datos['saldo_inicial'],
                    'movimiento' => '1'
                ]
            ];

            try {
                $mysqli = Database::getInstance();
                $res = $mysqli->insertaMultiple($qryPago, $registros);
                if ($res === true) return self::Responde(true, "Deposito por apertura de cuenta de ahorro registrado correctamente.");
                $error = array('datos' => $datos, 'registros' => $registros, 'res' => $res);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            $queryReverso = <<<sql
            DELETE FROM TICKETS_AHORRO WHERE CODIGO = (SELECT MAX(TO_NUMBER(CODIGO)) FROM TICKETS_AHORRO);
            sql;
            $mysqli->eliminar($queryReverso);
            return self::Responde(false, "Ocurrió un error al registrar los pagos de apertura.", null, $error);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al procesar los registros de apertura", null, $e->getMessage());
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////
}
