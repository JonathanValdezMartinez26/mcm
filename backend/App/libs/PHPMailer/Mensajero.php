<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mensajero
{
    static private $SMTP_SERVER = '';
    static private $SMTP_PORT = 0;
    static private $SMTP_USER = '';
    static private $SMTP_PASS = '';
    static private $SMTP_FROM = '';

    /**
     * Configura los parámetros del servidor SMTP para el envío de correos electrónicos.
     *
     * @param string|null $server Dirección del servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param int|null $port Puerto del servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param string|null $user Nombre de usuario para autenticarse en el servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param string|null $pass Contraseña para autenticarse en el servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param string|null $from Dirección de correo electrónico del remitente. Si es null, se toma del archivo de configuración.
     * @return void
     */
    public static function configura($server = null, $port = null, $user = null, $pass = null, $from = null)
    {
        $config = parse_ini_file(dirname(__DIR__) . '/../config/configuracion.ini');
        self::$SMTP_SERVER = $server ?? $config['SMTP_SERVER'];
        self::$SMTP_PORT = $port ?? $config['SMTP_PORT'];
        self::$SMTP_USER = $user ?? $config['SMTP_USER'];
        self::$SMTP_PASS = $pass ?? $config['SMTP_PASS'];
        self::$SMTP_FROM = $from ?? $config['SMTP_FROM'];
    }

    /**
     * Envía un correo electrónico utilizando PHPMailer.
     *
     * @param array|string $destinatarios Lista de destinatarios del correo. Puede ser un array o una cadena con un solo destinatario.
     * @param string $asunto Asunto del correo.
     * @param string $mensaje Cuerpo del mensaje del correo. Puede contener HTML.
     * @param array|string $adjuntos (Opcional) Lista de archivos adjuntos. Puede ser un array o una cadena con un solo archivo.
     * @return bool Devuelve true si el correo se envió correctamente, false en caso contrario.
     */
    public static function EnviarCorreo($destinatarios, $asunto, $mensaje, $adjuntos = [])
    {

        $mensajero = new PHPMailer(true);
        self::configura();

        try {
            $mensajero->setLanguage('es', __DIR__ . '\vendor\phpmailer\phpmailer\language');
            $mensajero->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mensajero->isSMTP();
            $mensajero->Host = self::$SMTP_SERVER;
            $mensajero->Port = self::$SMTP_PORT;
            $mensajero->SMTPAuth = true;
            $mensajero->Username = self::$SMTP_USER;
            $mensajero->Password = self::$SMTP_PASS;
            $mensajero->isHTML(true);
            $mensajero->Subject = $asunto;
            $mensajero->Body = $mensaje;
            $mensajero->AltBody = strip_tags($mensaje);
            $mensajero->CharSet = 'UTF-8';
            $mensajero->setFrom(self::$SMTP_USER, self::$SMTP_FROM);

            if (!is_array($adjuntos)) $adjuntos = [$adjuntos];
            if (count($adjuntos) > 0) {
                foreach ($adjuntos as $adjunto) {
                    $mensajero->addAttachment($adjunto);
                }
            }

            if (!is_array($destinatarios)) $destinatarios = [$destinatarios];
            foreach ($destinatarios as $destinatario) {
                $mensajero->clearAddresses();
                $mensajero->addAddress($destinatario);
                $mensajero->send();
            }
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo: {$e->getMessage()}");
            // mostrar la pila de errores
            error_log($e->getTraceAsString());
            return false;
        }
    }
}
