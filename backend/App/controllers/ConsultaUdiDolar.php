<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \App\models\ConsultaUdiDolar as ConsultaUdiDolarDao;
use \Core\Controller;

class ConsultaUdiDolar extends Controller
{

    // llave de consulta, que expira cada mes (( URL de consulta www.
    const API_KEY = '722947a253b50ca6aab1e6a7e82cd36c8a8d7b7fb5b97a9f836ee47cf373c8e7';


    public function index()
    {

        //$fecha = date('YYYY-MM-DD');

        $fecha = date('2024-05-27');
        // Obtener el valor del dólar y del UDI para una fecha específica
        $valorDolar = $this->obtenerValorPorFecha("SF63528", "$fecha");
        $valorUDI = $this->obtenerValorPorFecha("SP68257", "$fecha");


        if($valorDolar != 0 || $valorUDI != 0)
        {
            // Crear un array con los valores
            $valores = [
                'fecha' => $fecha,
                'dolar' => $valorDolar,
                'udi' => $valorUDI
            ];

            // Convertir los valores a formato JSON
            $valoresJson = json_encode($valores);

            // Imprimir el código JavaScript para realizar la solicitud AJAX
            echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
            <script>
                $.ajax({
                    type: 'POST', 
                    url: '/ConsultaUdiDolar/AgregarDatosBD/', 
                    data: " . $valoresJson . ", // Los datos a enviar
                    success: function(response) {
                        // Manejar la respuesta del servidor si es necesario
                        console.log(response);
                    },
                    error: function() {
                        console.error('Error al enviar los datos.');
                    }
                });
              </script>";
        }
        else
        {
            // Si alguno de los valores es cero, imprimir un mensaje de error
            echo "No se pudieron obtener los valores.";
        }


    }

    // Método privado para obtener los datos de la API para una fecha específica
    private function obtenerValorPorFecha($serie, $fecha) {
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
        if (isset($data['bmx']['series'][0]['datos'][0]['dato'])) {
            return $data['bmx']['series'][0]['datos'][0]['dato'];
        } else {
            return "0";
        }
    }


    public function AgregarDatosBD()
    {

        $fecha = $_POST['fecha'];
        $dolar = $_POST['dolar'];
        $udi = $_POST['udi'];

        $insert = ConsultaUdiDolarDao::AddUdiDolar($fecha, $dolar, $udi);

        echo $insert;
    }
}