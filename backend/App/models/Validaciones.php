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
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return "";
        }
    }

    public static function AddVinculaInvitado($datosCliente)
    {
        // $mysqli = Database::getInstance(1);
        $query = <<<sql
        se ejecutara el insert con los siguientes datos:
        INSERT INTO CL_PROMO_TELARANA (CDGNS_INVITA, CL_INVITADO, FECHA_REGISTRO, CICLO_INVITACION, CL_INVITA)
        VALUES ('{$datosCliente['invita']}', '{$datosCliente['invitado']}', '{$datosCliente['fecha']}')
sql;

        // $insert_folio = $mysqli->insert($query);
        // $update_pk = $mysqli->insert($query_1);

        // return self::Responde(true, "El cliente " . $datosCliente['invitado'] . " ha sido vinculado con exito");
        return self::Responde(true, $query);
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
            $mysqli = Database::getInstance();
            $resultado = $mysqli->queryOne($query);
            if ($resultado == null) return 0;

            return $resultado['ULTIMO_CICLO'];
        } catch (Exception $e) {
            return -1;
        }
    }

    public static function ValidaEstatusTelarana($invita, $invitado)
    {
        $query = <<<sql
        SELECT
            clpt.CL_INVITA,
            clpt.CL_INVITADO
        FROM
            CL_PROMO_TELARANA clpt
        WHERE
            clpt.CL_INVITA = '{$invita}'
            AND clpt.CL_INVITADO = '{$invitado}'
sql;

        try {
            $mysqli = Database::getInstance();
            $resultado = $mysqli->queryAll($query);
            if ($resultado == null) return array();
            return $resultado;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function BuscaCliente($cliente)
    {
        $codigo = $cliente['codigo'];
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
            $mysqli = Database::getInstance();
            $resultado = $mysqli->queryOne($query);
            if ($resultado == null) return self::Responde(false, "No se encontro el cliente {$cliente['codigo']}");
            $nombre = "{$resultado['CODIGO']} - {$resultado['NOMBRE']}";

            if (array_key_exists('anfitrion', $cliente)) {
                $anfitrion = $cliente['anfitrion'];
                $validacionInvitado = self::ValidaEstatusCredito($codigo);
                if ($validacionInvitado > 1) return self::Responde(false, "El invitado se encuentra en el ciclo {$validacionInvitado} y no puede ser registrado.");
                if ($validacionInvitado == -1) return self::Responde(false, "Error al validar al cliente invitado.");

                $validacionTelarana = self::ValidaEstatusTelarana($anfitrion['codigo'], $codigo);
                if (count($validacionTelarana) > 0) return self::Responde(false, "El cliente {$nombre} ya fue invitado por {$anfitrion['codigo']} - {$anfitrion['nombre']}");
                foreach ($validacionTelarana as $key => $value) {
                    if ($value['CL_INVITA'] == $codigo) return self::Responde(false, "El cliente {$nombre} no puede ser anfitrion e invitado a la vez.");
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
