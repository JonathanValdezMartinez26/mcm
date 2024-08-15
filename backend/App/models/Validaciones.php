<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;

class Validaciones
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

    public static function ConsultaClienteInvitado()
    {
        $query = <<<sql
        SELECT
            clpt.CDGNS_INVITA,
            clpt.CICLO_INVITACION,
            clpt.CL_INVITA,
            (SELECT CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) FROM CL WHERE CL.CODIGO = clpt.CL_INVITA) AS NOMBRE_INVITA,
            clpt.CL_INVITADO,
            (SELECT CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) FROM CL WHERE CL.CODIGO = clpt.CL_INVITADO) AS NOMBRE_INVITADO,
            clpt.FECHA_REGISTRO
        FROM
            CL_PROMO_TELARANA clpt
        sql;
        try {
            $mysqli = new Database();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return "";
        }
    }

    public static function VinculaInvitado($datosCliente)
    {
        $query = <<<sql
        INSERT INTO
            CL_PROMO_TELARANA
                (ID_CL_PROMO_TELARANA, CL_INVITA, CDGNS_INVITA, CICLO_INVITACION, CL_INVITADO, FECHA_REGISTRO)
            VALUES
                ((SELECT NVL(MAX(ID_CL_PROMO_TELARANA),0) FROM CL_PROMO_TELARANA)+1, :CL_INVITA, (SELECT UNIQUE CDGNS FROM PRC WHERE CDGCL = :CL_INVITA), (SELECT MAX(CICLO) FROM PRC WHERE CDGCL = :CL_INVITA), :CL_INVITADO, TO_DATE(:FECHA_REGISTRO, 'YYYY-MM-DD'))
        sql;

        $datos = [
            "CL_INVITA" => $datosCliente['anfitrion'],
            "CL_INVITADO" => $datosCliente['invitado'],
            "FECHA_REGISTRO" => $datosCliente['fecha']
        ];

        try {
            $mysqli = new Database();
            if ($mysqli->insertar($query, $datos)) return self::Responde(true, "Cliente invitado registrado exitosamente.");
            return self::Responde(false, "No se pudo registrar el vinculo entre anfitrión e invitado.");
        } catch (Exception $e) {
            return self::Responde(false, "Error interno al vincular al cliente invitado.");
        }
    }

    public static function ValidaEstatusCredito($codigo)
    {
        $query = <<<sql
        SELECT
            MAX(CICLO) AS ULTIMO_CICLO
        FROM (
            SELECT
                PRC.CDGCL, PRC.CDGNS, PRN.CICLO, PRC.CANTENTRE
            FROM
                PRN, PRC
            WHERE
                PRC.CDGNS = PRN.CDGNS
                AND PRC.CICLO = PRN.CICLO
                AND PRC.CANTENTRE < 90000
                AND PRC.CDGCL =  '{$codigo}'
            )
        sql;

        try {
            $mysqli = new Database();
            $resultado = $mysqli->queryOne($query);
            if ($resultado == null) return 0;

            return $resultado['ULTIMO_CICLO'];
        } catch (Exception $e) {
            return -1;
        }
    }

    public static function ValidaEstatusTelarana($invitado)
    {
        $query = <<<sql
            SELECT
                clpt.CL_INVITA AS CODIGO_ANFITRION,
                (
                    SELECT CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE)
                    FROM CL
                    WHERE CL.CODIGO = clpt.CL_INVITA
                ) AS NOMBRE_ANFITRION, 
                clpt.CL_INVITADO AS CODIGO_INVITADO,
                (
                    SELECT CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE)
                    FROM CL
                    WHERE CL.CODIGO = clpt.CL_INVITADO           
                ) AS NOMBRE_INVITADO,
                clpt.FECHA_REGISTRO
            FROM
                CL_PROMO_TELARANA clpt
            WHERE
                clpt.CL_INVITADO = '{$invitado}'
        sql;

        try {
            $mysqli = new Database();
            $resultado = $mysqli->queryAll($query);
            if ($resultado == null) return array();
            return $resultado;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function BuscaCredito($credito)
    {
        $query = <<<sql
        SELECT UNIQUE
            PRC.CDGCL AS CODIGO
        FROM
            PRC
        WHERE
            PRC.CDGNS = '{$credito}'
        sql;

        try {
            $mysqli = new Database();
            $resultado = $mysqli->queryOne($query);
            if ($resultado == null) return null;
            return $resultado['CODIGO'];
        } catch (Exception $e) {
            return null;
        }
    }

    public static function BuscaCliente($cliente)
    {
        $codigo = $cliente['xCredito'] == 'true' ? self::BuscaCredito($cliente['codigo']) : $cliente['codigo'];
        $param = $cliente['xCredito'] == 'true' ? "crédito" : "cliente";
        $query = <<<sql
        SELECT
            CL.CODIGO,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) as NOMBRE
        FROM
            CL
        WHERE
            CL.CODIGO = '{$codigo}'
        sql;

        try {
            $mysqli = new Database();
            $resultado = $mysqli->queryOne($query);
            if ($resultado == null) return self::Responde(false, "No se encontró el $param {$cliente['codigo']}");
            $nombre = "{$resultado['CODIGO']} - {$resultado['NOMBRE']}";

            if (array_key_exists('anfitrion', $cliente)) {
                $validacionInvitado = self::ValidaEstatusCredito($codigo);
                if ($validacionInvitado > 1) return self::Responde(false, "El invitado se encuentra en el ciclo {$validacionInvitado} y no puede ser registrado.");
                if ($validacionInvitado === -1) return self::Responde(false, "Error al validar al cliente invitado.");

                $validacionTelarana = self::ValidaEstatusTelarana($codigo);
                if (count($validacionTelarana) > 0) return self::Responde(false, "El cliente {$nombre} ya fue invitado por {$validacionTelarana[0]['CODIGO_ANFITRION']} - {$validacionTelarana[0]['NOMBRE_ANFITRION']} el {$validacionTelarana[0]['FECHA_REGISTRO']}.");
                foreach ($validacionTelarana as $key => $value) {
                    if ($value['CL_INVITA'] === $codigo) return self::Responde(false, "El cliente {$nombre} no puede ser anfitrión e invitado a la vez.");
                }
            } else {
                $validacionAnfitrion = self::ValidaEstatusCredito($codigo);
                if ($validacionAnfitrion == 0) return self::Responde(false, "El cliente que invita no tiene créditos activos.");
                if ($validacionAnfitrion == -1) return self::Responde(false, "Error al validar al cliente que invita.");
                if ($validacionAnfitrion < 4) return self::Responde(false, "El cliente que invita se encuentra en el ciclo {$validacionAnfitrion} y no cumple las políticas de la promoción.");
            }

            return self::Responde(true, "Consulta exitosa.", array("nombre" => $nombre));
        } catch (Exception $e) {
            return self::Responde(false, "Error interno al buscar al cliente.");
        }
    }
}
