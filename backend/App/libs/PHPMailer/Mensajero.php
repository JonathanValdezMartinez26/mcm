<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mensajero
{
    static private $host_SMTP = 'mail.masconmenos.com.mx';
    static private $puerto_SMTP = 465;
    static private $nombre = 'Notificaciones';
    static private $user = 'alberto.s@masconmenos.com.mx';
    static private $pass = '$lb3t02024$$D3s';

    public static function EnviarCorreo($destinatarios, $asunto, $mensaje, $adjuntos = [])
    {

        $mensajero = new PHPMailer(true);

        try {
            $mensajero->setLanguage('es', __DIR__ . '\vendor\phpmailer\phpmailer\language');
            $mensajero->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mensajero->isSMTP();
            $mensajero->Host = self::$host_SMTP;
            $mensajero->Port = self::$puerto_SMTP;
            $mensajero->SMTPAuth = true;
            $mensajero->Username = self::$user;
            $mensajero->Password = self::$pass;

            if (!is_array($destinatarios)) $destinatarios = [$destinatarios];
            $mensajero->setFrom(self::$user, self::$nombre);
            foreach ($destinatarios as $destinatario) {
                $mensajero->addBCC($destinatario);
            }

            $mensajero->isHTML(true);
            $mensajero->Subject = $asunto;
            $mensajero->Body = $mensaje;
            // $mensajero->AltBody = strip_tags($mensaje);
            $mensajero->CharSet = 'UTF-8';

            if (!is_array($adjuntos)) $adjuntos = [$adjuntos];
            if (count($adjuntos) > 0) {
                foreach ($adjuntos as $adjunto) {
                    $mensajero->addAttachment($adjunto);
                }
            }

            $mensajero->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function Configurar($host, $puerto, $nombre, $user, $pass)
    {
        self::$host_SMTP = $host;
        self::$puerto_SMTP = $puerto;
        self::$nombre = $nombre;
        self::$user = $user;
        self::$pass = $pass;
    }
}
