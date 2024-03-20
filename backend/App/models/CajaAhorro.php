<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;
use DateTime;

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
        $queryValidacion = <<<sql
        SELECT
            *
        FROM
            ASIGNA_PROD_AHORRO
        WHERE
            CDGCL = :cliente
        sql;

        $datos = [
            'cliente' => $cliente
        ];

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($queryValidacion, $datos);
            if ($res) return self::Responde(false, "El cliente ya cuenta con un contrato de ahorro");

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
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al validar si el cliente ya cuenta con un contrato de ahorro", null, $e->getMessage());
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
        $queryValidacion = <<<sql
        SELECT
            *
        FROM
            ASIGNA_PROD_AHORRO
        WHERE
            CDGCL = :cliente
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($queryValidacion, ['cliente' => $datos['credito']]);
            if ($res) return self::Responde(false, "El cliente ya cuenta con un contrato de ahorro");

            $noContrato = $datos['credito'] . date_format(date_create($datos['fecha']), 'Ymd');

            $query = <<<sql
            INSERT INTO ASIGNA_PROD_AHORRO
                (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, SALDO)
            VALUES
                (:contrato, :cliente, :fecha_apertura, '1', 'A', 0)
            sql;

            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha']);
            $fecha = $fecha !== false && $fecha->format('Y-m-d') === $datos['fecha'] ? $fecha->format('d-m-Y') : $datos['fecha'];

            $datos = [
                'contrato' => $noContrato,
                'cliente' => $datos['credito'],
                'fecha_apertura' => $fecha
            ];

            try {
                $mysqli = Database::getInstance();
                $res = $mysqli->insertar($query, $datos);
                if (!$res) return self::Responde(true, "Contrato de ahorro registrado correctamente", ['contrato' => $noContrato]);
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro");
            } catch (Exception $e) {
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro", null, $e->getMessage());
            }
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al validar si el cliente ya cuenta con un contrato de ahorro", null, $e->getMessage());
        }
    }

    public static function AddPagoApertura($datos)
    {
        if ($datos['deposito_inicial'] == 0) return self::Responde(false, "El monto de apertura no puede ser de 0");
        if ($datos['saldo_inicial'] < $datos['sma']) return self::Responde(false, "El saldo inicial no puede ser menor a " . $datos['sma']);

        $query = [
            self::GetQueryTiecket(),
            self::GetQueryMovimientoAhorro(),
            self::GetQueryMovimientoAhorro()
        ];

        $validacion = [
            'query' => self::GetQueryValidaAhorro(),
            'datos' => ['contrato' => $datos['contrato']],
            'funcion' => [CajaAhorro::class, 'ValidaMovimientoAhorro']
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
                'monto' => $datos['deposito'],
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
            $res = $mysqli->insertaMultiple($query, $datos, $validacion);
            if ($res) return self::Responde(true, "Pago de apertura registrado correctamente");
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura", null, $e->getMessage());
        }
    }

    public static function RegistraOperacion($datos)
    {
        $query = [
            self::GetQueryTiecket(),
            self::GetQueryMovimientoAhorro()
        ];

        $esDeposito = $datos['esDeposito'] === true || $datos['esDeposito'] === 'true';

        $datos = [
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['montoOperacion'],
                'ejecutivo' => $datos['ejecutivo']
            ],
            [
                'tipo_pago' => $esDeposito ? '3' : '4',
                'contrato' => $datos['contrato'],
                'monto' => $datos['montoOperacion'],
                'movimiento' => $esDeposito ? '1' : '0'
            ]
        ];

        $tipoMov = $esDeposito ? "depósito" : "retiro";

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->insertaMultiple($query, $datos);
            if ($res) return self::Responde(true, "El " . $tipoMov . " fue registrado correctamente");
            return self::Responde(false, "Ocurrió un error al registrar el " . $tipoMov);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el " . $tipoMov, null, $e->getMessage());
        }
    }

    public static function ValidaMovimientoAhorro($validar)
    {
        $resultado = [
            'success' => true,
            'mensaje' => ""
        ];

        if (count($validar) > 0) return $resultado;

        $resultado['success'] = false;
        $resultado['mensaje'] = "Se detecto diferencia entre el registro del ticket y los movimiento de ahorro";
        return $resultado;
    }

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

    public static function GetQueryValidaAhorro()
    {
        return <<<sql
        SELECT
            *
        FROM
            (
            SELECT
                T.CODIGO AS TC,
                T.MONTO AS TM,
                MA.CODIGO AS MC,
                MA.MONTO AS MM,
                NVL(T.MONTO,0) - NVL(MA.MONTO,0) AS DIFERENCIA
            FROM
                TICKETS_AHORRO T
            FULL JOIN
                (
                SELECT
                    M.CDG_TICKET AS CODIGO,
                    SUM(CASE M.MOVIMIENTO
                        WHEN '1' THEN M.MONTO
                        ELSE -M.MONTO
                    END) AS MONTO
                FROM
                    MOVIMIENTOS_AHORRO M
                GROUP BY
                    M.CDG_TICKET
                ) MA
            ON T.CODIGO = MA.CODIGO
            WHERE
                T.CDG_CONTRATO = :contrato
            )
        WHERE
            DIFERENCIA != 0
        sql;
    }

    ////////////////////////////////////////////////////////////////////////////////////////
}
