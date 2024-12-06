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

    public static function GetDestinatarios($modulo)
    {
        $qry = 'SELECT CORREO FROM CORREO_DESTINATARIOS WHERE GRUPO = :modulo';

        $prm = [
            'modulo' => $modulo
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Destinatarios obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener destinatarios', null, $e->getMessage());
        }
    }
}
