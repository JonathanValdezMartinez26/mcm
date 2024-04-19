<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use \Core\Database_cultiva;
use Exception;

class AdminSucursales
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

    public static function GetSucursalesActivas()
    {
        $query = <<<sql
            SELECT
                TO_CHAR(FECHA_REGISTRO, 'DD/MM/YYYY') FECHA_REGISTRO,
                SEA.CDG_SUCURSAL,
                CO.NOMBRE,
                SCA.CDG_USUARIO,
                (
                    SELECT
                        CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE)
                    FROM 
                        PE
                    WHERE
                        PE.CODIGO = SCA.CDG_USUARIO
                ) NOMBRE_CAJERA,
                TO_CHAR(TO_DATE(SCA.HORA_APERTURA, 'HH24:MI:SS'), 'HH:MI AM') HORA_APERTURA,
                TO_CHAR(TO_DATE(SCA.HORA_CIERRE, 'HH24:MI:SS'), 'HH:MI AM') HORA_CIERRE,
                TO_CHAR(TO_NUMBER(SEA.SALDO_MINIMO), 'FM$999,999,999.00') SALDO_MINIMO,
                TO_CHAR(TO_NUMBER(SEA.SALDO_MAXIMO), 'FM$999,999,999.00') SALDO_MAXIMO,
                NULL ACCIONES
            FROM
                SUC_ESTADO_AHORRO SEA
            JOIN
                CO ON CO.CODIGO = SEA.CDG_SUCURSAL
            RIGHT JOIN
                SUC_CAJERA_AHORRO SCA ON SCA.CDG_ESTADO_AHORRO = SEA.CODIGO
            WHERE
                SEA.ESTATUS = 'A'
            ORDER BY
                CO.NOMBRE
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function GetSucursales()
    {
        $query = <<<sql
            SELECT
                CO.CODIGO,
                CO.NOMBRE
            FROM
                CO
            WHERE
                CO.CODIGO NOT IN (
                    SELECT
                        CDG_SUCURSAL
                    FROM
                        SUC_ESTADO_AHORRO
                    WHERE
                        ESTATUS = 'A'
                    )
            ORDER BY
                CO.NOMBRE
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function GetMontoSucursal($sucursal)
    {
        $query = <<<sql
            SELECT
                SALDO_MINIMO,
                SALDO_MAXIMO
            FROM
                SUC_ESTADO_AHORRO
            WHERE
                CDG_SUCURSAL = '$sucursal'
                AND ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($query);
            if ($res) return self::Responde(true, "Monto de sucursal encontrado", $res);
            return self::Responde(false, "No se encontró monto de sucursal");
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar monto de sucursal", null, $e->getMessage());
        }
    }

    public static function GetCajeras($sucursal)
    {
        $qry = <<<sql
        SELECT * FROM (
            SELECT DISTINCT 
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) NOMBRE, PE.CODIGO
            FROM
                PE,
                UT
            WHERE
            PE.CODIGO = UT.CDGPE
            AND PE.ACTIVO = 'S'
            AND (PE.BLOQUEO = 'N' OR PE.BLOQUEO IS NULL) 
            AND (PE.CDGCO = '$sucursal' OR (PE.CODIGO = 'LGFR' AND UT.CDGTUS = 'CAJA' ))
            ) 
        sql;


        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qry);
            return self::Responde(true, "Cajeras encontradas", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar cajeras", null, $e->getMessage());
        }
    }

    public static function GetHorarioCajera($cajera)
    {
        $qry = <<<sql
        SELECT
            HORA_APERTURA,
            HORA_CIERRE
        FROM
            SUC_CAJERA_AHORRO
        WHERE
            CDG_USUARIO = '$cajera'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qry);
            if ($res) return self::Responde(true, "Horario de cajera encontrado", $res);
            else return self::Responde(false, "No se encontró horario de cajera");
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar horario de cajera", null, $e->getMessage());
        }
    }

    public static function ActivarSucursal($datos)
    {
        $qrySuc = <<<sql
        INSERT INTO SUC_ESTADO_AHORRO
            (CODIGO, CDG_SUCURSAL, FECHA_REGISTRO, ESTATUS, SALDO_MINIMO, SALDO_MAXIMO)
        VALUES
            (
                (SELECT NVL(MAX(TO_NUMBER(CODIGO)), 0) FROM SUC_ESTADO_AHORRO) + 1,
                :sucursal,
                SYSDATE,
                'A',
                :minimo,
                :maximo
            )
        sql;

        $qryCaj = <<<sql
        INSERT INTO SUC_CAJERA_AHORRO
            (CDG_ESTADO_AHORRO, CDG_USUARIO, HORA_APERTURA, HORA_CIERRE)
        VALUES
            (
                (SELECT MAX(TO_NUMBER(CODIGO)) FROM SUC_ESTADO_AHORRO),
                :cajera,
                :apertura,
                :cierre
            )
        sql;

        $qrys = [
            $qrySuc,
            $qryCaj
        ];

        $params = [
            [
                "sucursal" => $datos['sucursal'],
                "minimo" => $datos['montoMin'],
                "maximo" => $datos['montoMax']
            ],
            [
                "cajera" => $datos['cajera'],
                "apertura" => $datos['horaA'],
                "cierre" => $datos['horaC']
            ]
        ];

        try {
            $ora = Database::getInstance();
            $res = $ora->insertaMultiple($qrys, $params);
            if ($res) return self::Responde(true, "Sucursal activada correctamente");
            else return self::Responde(false, "Error al activar sucursal");
        } catch (Exception $e) {
            return self::Responde(false, "Error al activar sucursal", null, $e->getMessage());
        }
    }

    public static function GetDatosFondeo($datos)
    {
        $qry = <<<sql
        SELECT
            SEA.CODIGO,
            SEA.CDG_SUCURSAL AS CODIGO_SUCURSAL,
            (
                SELECT
                    NOMBRE
                FROM
                    CO
                WHERE
                    CODIGO = SEA.CDG_SUCURSAL
            ) AS NOMBRE_SUCURSAL,
            SCA.CDG_USUARIO AS CODIGO_CAJERA,
            (
                SELECT
                    CONCATENA_NOMBRE(NOMBRE1, NOMBRE2, PRIMAPE, SEGAPE)
                FROM
                    PE
                WHERE
                    CODIGO = SCA.CDG_USUARIO
            ) AS NOMBRE_CAJERA,
            NULL AS FECHA_CIERRE,
            SEA.SALDO_MINIMO AS MONTO_MIN,
            SEA.SALDO_MAXIMO AS MONTO_MAX,
            NVL(SEA.SALDO, 0) AS SALDO
        FROM
            SUC_ESTADO_AHORRO SEA
        RIGHT JOIN
            SUC_CAJERA_AHORRO SCA ON SCA.CDG_ESTADO_AHORRO = SEA.CODIGO
        WHERE
            SEA.CDG_SUCURSAL = '{$datos["sucursal"]}'
            AND SEA.ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qry);
            if ($res) return self::Responde(true, "Datos encontrados.", $res);
            else return self::Responde(false, "No se encontraron datos para la sucursal " . $datos["sucursal"] . ".");
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar información de la sucursal.", null, $e->getMessage());
        }
    }

    public static function AplicarFondeo($datos)
    {
        $qry = <<<sql
        INSERT INTO SUC_MOVIMIENTOS_AHORRO
            (CODIGO, CDG_ESTADO_AHORRO, FECHA, MONTO, MOVIMIENTO, CDG_USUARIO)
        VALUES
            (
                (SELECT NVL(MAX(TO_NUMBER(CODIGO)), 0) FROM SUC_MOVIMIENTOS_AHORRO) + 1,
                :codigo,
                SYSDATE,
                :monto,
                '1',
                :usuario
            )
        sql;

        $params = [
            "codigo" => $datos["codigoSEA"],
            "monto" => $datos["montoOperacion"],
            "usuario" => $datos["usuario"]
        ];

        try {
            $mysqli = Database::getInstance();
            $mysqli->insertar($qry, $params);
            return self::Responde(true, "Fondeo realizado correctamente.");
        } catch (Exception $e) {
            return self::Responde(false, "Error al realizar fondeo.", null, $e->getMessage());
        }
    }
}
