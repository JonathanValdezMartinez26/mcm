<?php

namespace Core;

class Model
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = [
            "success" => $respuesta,
            "mensaje" => $mensaje
        ];

        if ($datos !== null) $res['datos'] = $datos;
        if ($error !== null) $res['error'] = $error;

        return $res;
    }
}
