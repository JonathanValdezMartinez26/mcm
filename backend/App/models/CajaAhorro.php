<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class CajaAhorro
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

    public static function ConsultaClientesProducto($cliente)
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

    public static function BuscaClienteNvoContrato($cliente)
    {
        $query = <<<sql
        SELECT
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CURP,
            TO_CHAR(CL.REGISTRO, 'DD-MM-YYYY') AS FECHA_REGISTRO,
            TRUNC(MONTHS_BETWEEN(TO_DATE(SYSDATE, 'dd-mm-yy'), CL.NACIMIENTO)/12)AS EDAD,
            UPPER((CL.CALLE
                || ', '
                || COL.NOMBRE
                || ', '
                || LO.NOMBRE
                || ', '
                || MU.NOMBRE
                || ', '
                || EF.NOMBRE)) AS DIRECCION
        FROM
            CL,
            COL,
            LO,
            MU,
            EF
        WHERE
            EF.CODIGO = CL.CDGEF
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

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($query);
            if ($res) return self::Responde(true, "Consulta realizada correctamente", $res);
            return self::Responde(false, "No se encontraron datos para el cliente $cliente");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente", null, $e->getMessage());
        }
    }

    public static function BuscaClienteContrato($cliente)
    {
        $query = <<<sql
        SELECT
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CURP,
            (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO) AS CONTRATO,
            (SELECT SALDO FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO)) AS SALDO
        FROM
            CL
        WHERE
            CL.CODIGO = '$cliente'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($query);
            if ($res) return self::Responde(true, "Consulta realizada correctamente", $res);
            return self::Responde(false, "No se encontraron datos para el cliente $cliente");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente", null, $e->getMessage());
        }
    }

    public static function AgregaContratoAhorro($datos)
    {
        $noContrato = $datos['credito'] . date_format(date_create($datos['fecha']), 'Ymd');

        $qryAhorro = <<<sql
        INSERT INTO ASIGNA_PROD_AHORRO
            (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, SALDO)
        VALUES
            (:contrato, :cliente, :fecha_apertura, '1', 'A', 0)
        sql;

        $resDemo = [
            'contrato' => $noContrato,
            'ahorro' => $qryAhorro,
        ];
        return json_encode($resDemo);
    }

    public static function AddPagoApertura($datos)
    {
        $error = null;

        if ($datos['deposito_inicial'] == 0) return self::Responde(false, "El monto de apertura no puede ser de 0");
        if ($datos['saldo_inicial'] < $datos['sma']) return self::Responde(false, "El saldo inicial no puede ser menor a " . $datos['sma']);

        $query = [
            self::GetQueryTiecket(),
            self::GetQueryMovimientoAhorro(),
            self::GetQueryMovimientoAhorro()
        ];


        $datos = [
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['deposito_inicial'],
                'ejecutivo' => $datos['ejecutivo']
            ],
            [
                'tipo_pago' => '1',
                'contrato' => $datos['contrato'],
                'monto' => $datos['saldo_inicial'],
                'movimiento' => '1'
            ],
            [
                'tipo_pago' => '2',
                'contrato' => $datos['contrato'],
                'monto' => $datos['inscripcion'],
                'movimiento' => '0'
            ]
        ];

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->insertaMultiple($query, $datos);
            if ($res) return self::Responde(true, "Pago de apertura registrado correctamente");
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura", null, $e->getMessage());
        }
    }

    // public static function RegistraOperacion($datos)
    // {
    //     $error = null;

    //     $datosTicket = [
    //         'contrato' => $datos['contrato'],
    //         'monto' => $datos['monto'],
    //         'ejecutivo' => $datos['ejecutivo']
    //     ];

    //     if (!$ticket['success']) return self::Responde(false, "Ocurrió un error al registrar el ticket de ahorro", null, $ticket['mensaje']);

    //     $registro = [
    //         'tipo_pago' => $datos['esDeposito'] ? '2' : '3',
    //         'contrato' => $datos['contrato'],
    //         'monto' => $datos['monto'],
    //         'movimiento' => $datos['esDeposito'] ? '1' : '0',
    //         'ticket' => $ticket['ticket']
    //     ];

    //     if (!$res['success']) {
    //         $error = $res['mensaje'];
    //         $elimminaT = self::EliminaTicket($ticket['ticket']);
    //         if (!$elimminaT['success']) $error = $elimminaT;
    //         return self::Responde(false, "Ocurrió un error al registrar el pago.", null, $error);
    //     }

    //     return self::Responde(true, "Pago registrado correctamente");
    // }

    public static function GetQueryTiecket()
    {
        return <<<sql
        INSERT INTO TICKETS_AHORRO
            (CODIGO, FECHA, CDG_CONTRATO, MONTO, CDGPE)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM TICKETS_AHORRO) + 1, SYSDATE, :contrato, :monto, :ejecutivo)
        sql;
    }

    public static function GetQueryMovimientoAhorro()
    {
        return <<<sql
        INSERT INTO MOVIMIENTOS_AHORRO
            (CODIGO, FECHA_MOV, CDG_TIPO_PAGO, CDG_CONTRATO, MONTO, MOVIMIENTO, DESCRIPCION, CDG_TICKET, FECHA_VALOR)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM MOVIMIENTOS_AHORRO) + 1, SYSDATE, :tipo_pago, :contrato, :monto, :movimiento, 'ALGUNA_DESCRIPCION', (SELECT MAX(TO_NUMBER(CODIGO)) AS CODIGO FROM TICKETS_AHORRO WHERE CDG_CONTRATO = :contrato), SYSDATE)
        sql;
    }

    ////////////////////////////////////////////////////////////////////////////////////////
}
