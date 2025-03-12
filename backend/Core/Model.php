<?php

namespace Core;

class Model
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = [
            'success' => $respuesta,
            'mensaje' => $mensaje
        ];

        if ($datos !== null) $res['datos'] = $datos;
        if ($error !== null) $res['error'] = $error;

        return $res;
    }

    public static function GetDestinatarios($aplicacion)
    {
        $qry = <<<SQL
            SELECT
                CD.CORREO
            FROM
                CORREO_APLICACION_GRUPO CAG
                JOIN CORREO_DIRECTORIO_GRUPO CDG ON CAG.ID_GRUPO = CDG.ID_GRUPO
                JOIN CORREO_DIRECTORIO CD ON CD.ID = CDG.ID_CORREO
            WHERE
                CAG.ID_APLICACION = :aplicacion
        SQL;

        $prm = [
            'aplicacion' => $aplicacion
        ];

        try {
            $db = new Database('SERVIDOR-AWS');
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Destinatarios obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener destinatarios', null, $e->getMessage());
        }
    }
}
