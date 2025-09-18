<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class RadarCobranza extends Model
{
    static private $apiBaseUrl = 'http://18.117.29.228:5000';

    static public function Login($datos)
    {
        try {
            $url = self::$apiBaseUrl . '/login';

            $postData = json_encode([
                'usuario' => $datos['usuario'],
                'password' => $datos['password']
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return self::Responde(false, 'Error de conexión: ' . $error);
            }

            if ($httpCode !== 200) {
                return self::Responde(false, 'Error en la autenticación');
            }

            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return self::Responde(false, 'Error al procesar la respuesta del servidor');
            }

            return self::Responde(true, 'Login exitoso', $responseData);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al realizar login: ' . $e->getMessage());
        }
    }

    static public function GetResumenCobranza($token)
    {
        try {
            $url = self::$apiBaseUrl . '/ResumenCobranza';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return self::Responde(false, 'Error de conexión: ' . $error);
            }

            if ($httpCode === 401) {
                return self::Responde(false, 'Token expirado o inválido', null, 'TOKEN_EXPIRED');
            }

            if ($httpCode !== 200) {
                return self::Responde(false, 'Error al obtener resumen de cobranza');
            }

            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return self::Responde(false, 'Error al procesar la respuesta del servidor');
            }

            return self::Responde(true, 'Consulta exitosa', $responseData);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener resumen: ' . $e->getMessage());
        }
    }
}
