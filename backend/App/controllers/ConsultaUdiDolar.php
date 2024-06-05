<?php

namespace App\controllers;

include 'C:/xampp/htdocs/mcm/backend/App/models/ConsultaUdiDolar.php';

use \App\models\ConsultaUdiDolar as ConsultaUdiDolarDao;
use DateTime;
use DateTimeZone;

$validaHV = new DateTime('now', new DateTimeZone('America/Mexico_City'));
if ($validaHV->format('I')) date_default_timezone_set('America/Mazatlan');
else date_default_timezone_set('America/Mexico_City');

$dolar_udi = new ConsultaUdiDolar();

$dolar_udi->GetUDI_Dolar();

class ConsultaUdiDolar
{
    // llave de consulta, que expira cada mes (( URL de consulta www.
    const API_KEY = '722947a253b50ca6aab1e6a7e82cd36c8a8d7b7fb5b97a9f836ee47cf373c8e7';

    /**
     * Guarda un registro en el archivo de log.
     *
     * @param string $tdatos Datos a guardar en el log.
     * @return void
     */
    public function SaveLog($tdatos)
    {
        $archivo = "C:/xampp/UDI_Dolar.log";

        clearstatcache();
        if (file_exists($archivo) && filesize($archivo) > 10 * 1024 * 1024) { // 10 MB
            $nuevoNombre = "C:/xampp/UDI_Dolar" . date('Ymd') . ".log";
            rename($archivo, $nuevoNombre);
        }

        $log = fopen($archivo, "a");

        $infoReg = date("Y-m-d H:i:s") . " - job_fnc: " . debug_backtrace()[1]['function'] . " -> " . $tdatos;

        fwrite($log, $infoReg . PHP_EOL);
        fclose($log);
    }

    /**
     * Consulta el valor del dólar y del UDI para una fecha específica y los guarda en la base de datos.
     *
     * @return void
     */
    public function GetUDI_Dolar()
    {
        $fecha = date('Y-m-d');

        // Obtener el valor del dólar y del UDI para una fecha específica
        $valorDolar = $this->obtenerValorPorFecha("SF63528", "$fecha");
        $valorUDI = $this->obtenerValorPorFecha("SP68257", "$fecha");

        // Guardar los valores en la base de datos
        $resultado =  ($valorDolar != 0 || $valorUDI != 0) ?
            ConsultaUdiDolarDao::AddUdiDolar($fecha, $valorDolar, $valorUDI) :
            "No se pudieron obtener los valores.";

        // Guardar el resultado en el log
        self::SaveLog(json_encode($resultado, JSON_PRETTY_PRINT));
    }

    /**
     * Obtiene el valor de una serie para una fecha específica.
     *
     * @param string $serie Serie a consultar.
     * @param string $fecha Fecha para la que se desea obtener el valor.
     * @return string Valor de la serie para la fecha especificada.
     */
    private function obtenerValorPorFecha($serie, $fecha)
    {
        // Formatear la fecha en el formato requerido por la API (YYYY-MM-DD)
        $fechaFormateada = date('Y-m-d', strtotime($fecha));

        // URL de la API del Banco de México para obtener el valor para una fecha específica
        $url = "https://www.banxico.org.mx/SieAPIRest/service/v1/series/$serie/datos/$fechaFormateada/$fechaFormateada?token=" . self::API_KEY;

        // Inicializar cURL
        $ch = curl_init();

        // Configurar cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Ejecutar cURL
        $response = curl_exec($ch);

        // Cerrar cURL
        curl_close($ch);

        // Decodificar la respuesta JSON
        $data = json_decode($response, true);
        // Verificar si la respuesta contiene los datos esperados
        if (!isset($data['bmx']['series'][0]['datos'][0]['dato'])) return "0";
        return $data['bmx']['series'][0]['datos'][0]['dato'];
    }
}
