<?PHP

namespace App\controllers;

include 'C:/xampp/htdocs/mcm/backend/App/models/JobsAhorro.php';

use \App\models\JobsAhorro as JobsDao;
use DateTime;
use DateTimeZone;

$validaHV = new DateTime('now', new DateTimeZone('America/Mexico_City'));
if ($validaHV->format('I')) date_default_timezone_set('America/Mazatlan');
else date_default_timezone_set('America/Mexico_City');

$jobs = new JobsAhorro();

if (isset($argv[1])) {
    switch ($argv[1]) {
        case 'SucursalesSinArqueo':
            // Programar a las 5:20 pm, de lunes a viernes
            $jobs->SucursalesSinArqueo();
            break;
        case 'CapturaSaldosSucursales':
            // Programas a las 5:22 pm, de lunes a viernes
            $jobs->CapturaSaldosSucursales();
            break;
        case 'RechazaSolicitudesSinAtender':
            // Programar a las 5:25 pm, de lunes a viernes
            $jobs->RechazaSolicitudesSinAtender();
            break;
        case 'DevengoInteresAhorroDiario':
            // Programar a las 6:00 pm, todos los días
            $jobs->DevengoInteresAhorroDiario();
            break;
        case 'LiquidaInversion':
            // Programar 11:50 pm, todos los dias
            $jobs->LiquidaInversion();
            break;
        case 'ComprobacionDevengoAhorro':
            $jobs->ComprobacionDevengoAhorro();
            break;
        case 'prueba_horario':
            echo date("Y-m-d H:i:s") . "\n";
            break;
        case 'help':
            echo "Los jobs disponibles son: \n";
            echo "SucursalesSinArqueo: Se recomienda se ejecute a las 5:20 pm, de lunes a viernes.\n";
            echo "CapturaSaldosSucursales: Se recomienda se ejecute a las 5:22 pm, de lunes a viernes.\n";
            echo "DevengoInteresAhorroDiario: Se recomienda se ejecute a las 6:00 pm, todos los días.\n";
            echo "RechazaSolicitudesSinAtender: Se recomienda se ejecute a las 5:25 pm, de lunes a viernes.\n";
            echo "LiquidaInversion: Se recomienda se ejecute a las 11:50 pm, todos los días.\n";
            echo "ComprobacionDevengoAhorro: Se plica de forma manual unicamente cuando se requiera validar los intereses devengados.\n";
            break;
        default:
            echo "No se encontró el job solicitado.\nEjecute 'php JobsAhorro.php help' para ver los jobs disponibles.\n";
            break;
    }
} else echo "Debe especificar el job a ejecutar.\nEjecute 'php JobsAhorro.php help' para ver los jobs disponibles.\n";

class JobsAhorro
{
    public function SaveLog($tdatos)
    {
        $archivo = "C:/xampp/JobsAhorro.log";

        clearstatcache();
        if (file_exists($archivo) && filesize($archivo) > 10 * 1024 * 1024) { // 10 MB
            $nuevoNombre = "C:/xampp/JobsAhorro" . date('Ymd') . ".log";
            rename($archivo, $nuevoNombre);
        }

        $log = fopen($archivo, "a");

        $infoReg = date("Y-m-d H:i:s") . " - job_fnc: " . debug_backtrace()[1]['function'] . " -> " . $tdatos;

        fwrite($log, $infoReg . PHP_EOL);
        fclose($log);
    }

    public function DevengoInteresAhorroDiario()
    {
        self::SaveLog("Inicio -> Devengo Interés Ahorro Diario");
        $resumen = [];
        $cuentas = JobsDao::GetCuentasActivas();
        if (!$cuentas["success"]) return self::SaveLog("Error al obtener las cuentas de ahorro activas: " . $cuentas["error"]);
        if (count($cuentas["datos"]) == 0) return self::SaveLog("No se encontraron cuentas de ahorro activas para aplicar devengo.");

        foreach ($cuentas["datos"] as $key => $cuenta) {
            $saldo = $cuenta["SALDO"];
            $tasa = $cuenta["TASA"] / 100;
            $devengo = $saldo * ($tasa / 365);

            $datos = [
                "cliente" => $cuenta["CLIENTE"],
                "contrato" => $cuenta["CONTRATO"],
                "saldo" => $saldo,
                "devengo" => $devengo,
                "tasa" => $tasa,
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_APLICA_DEVENGO" => JobsDao::AplicaDevengo($datos),
            ];
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizado -> Devengo Interés Ahorro Diario");
    }

    public function LiquidaInversion()
    {
        self::SaveLog("Inicio -> Liquidación de Inversiones");
        $resumen = [];
        $inversiones = JobsDao::GetInversiones();
        if (!$inversiones["success"]) return self::SaveLog("Error al obtener las inversiones: " . $inversiones["error"]);
        if (count($inversiones["datos"]) == 0) return self::SaveLog("No se encontraron inversiones para liquidar.");

        foreach ($inversiones["datos"] as $key => $inversion) {
            $monto = $inversion["MONTO"];
            $tasa = (($inversion["TASA"] / 100) / 12);
            $plazo = $inversion["PLAZO"];
            $rendimiento = $monto * $plazo * $tasa;

            $datos = [
                "contrato" => $inversion["CONTRATO"],
                "rendimiento" => $rendimiento,
                "monto" => $monto,
                "cliente" => $inversion["CLIENTE"],
                "fecha_apertura" => $inversion["FECHA_APERTURA"],
                "fecha_vencimiento" => $inversion["FECHA_VENCIMIENTO"],
                "id_tasa" => $inversion["ID_TASA"],
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_LIQUIDA_INVERSION" => JobsDao::LiquidaInversion($datos),
            ];
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizado -> Liquidación de Inversiones");
    }

    public function RechazaSolicitudesSinAtender()
    {
        self::SaveLog("Inicio -> Rechazo de Solicitudes de retiro sin Atender");
        $resumen = [];
        $solicitudes = JobsDao::GetSolicitudesRetiro();
        if (!$solicitudes["success"]) return self::SaveLog("Error al obtener las solicitudes sin atender: " . $solicitudes["error"]);
        if (count($solicitudes["datos"]) == 0) return self::SaveLog("No se encontraron solicitudes sin atender para rechazar.");

        foreach ($solicitudes["datos"] as $key => $solicitud) {
            $datosRechazo = [
                "idSolicitud" => $solicitud["ID"]
            ];

            $datosDevolucion = [
                "contrato" => $solicitud["CONTRATO"],
                "monto" => $solicitud["MONTO"],
                "cliente" => $solicitud["CLIENTE"],
                "tipo" => $solicitud["TIPO_RETIRO"],
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos_rechazo" => $datosRechazo,
                "RES_RECHAZA_SOLICITUD" => JobsDao::CancelaSolicitudRetiro($datosRechazo),
                "datos_devolucion" => $datosDevolucion,
                "RES_DEVUELVE_MONTO" => JobsDao::DevolucionRetiro($datosDevolucion),
            ];
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizado -> Rechazo de Solicitudes de retiro sin Atender");
    }

    public function SucursalesSinArqueo()
    {
        self::SaveLog("Inicio -> Sucursales sin Arqueo");
        $resumen = [];
        $sucursales = JobsDao::GetSucursalesSinArqueo();

        if (!$sucursales["success"]) return self::SaveLog("Error al obtener las sucursales sin arqueo: " . $sucursales["error"]);
        if (count($sucursales["datos"]) == 0) return self::SaveLog("No se encontraron sucursales sin arqueo.");

        foreach ($sucursales["datos"] as $key => $sucursal) {
            $datos = [
                'sucursal' => $sucursal['CDG_SUCURSAL']
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_REGISTRO_ARQUEO" => JobsDao::RegistraArqueoPendiente($datos)
            ];
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizado -> Sucursales sin Arqueo");
    }

    public function CapturaSaldosSucursales()
    {
        self::SaveLog("Inicio -> Captura de Saldos de Sucursales");
        $resumen = [];
        $sucursales = JobsDao::GetSucursales();

        if (!$sucursales["success"]) return self::SaveLog("Error al obtener las sucursales: " . $sucursales["error"]);
        if (count($sucursales["datos"]) == 0) return self::SaveLog("No se encontraron sucursales para capturar saldos.");

        foreach ($sucursales["datos"] as $key => $sucursal) {
            $datos = [
                'codigo' => $sucursal['CODIGO'],
                'saldo' => $sucursal['SALDO']
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_CAPTURA_SALDOS" => JobsDao::CapturaSaldos($datos)
            ];
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizado -> Captura de Saldos de Sucursales");
    }

    public function ComprobacionDevengoAhorro()
    {
        self::SaveLog("Inicio -> Comprobación de Devengo Ahorro");
        $resumen = [];
        $cuentas = JobsDao::GetCuentasAhorroValidacionDevengo();
        if (!$cuentas["success"]) return self::SaveLog("Error al obtener las cuentas de ahorro activas: " . $cuentas["error"]);
        if (count($cuentas["datos"]) == 0) return self::SaveLog("No se encontraron cuentas de ahorro activas para aplicar devengo.");

        foreach ($cuentas["datos"] as $key => $cuenta) {
            $saldo = $cuenta["SALDO"];
            $tasa = $cuenta["TASA"] / 100;
            $devengo = $saldo * ($tasa / 365);

            $datos = [
                "cliente" => $cuenta["CLIENTE"],
                "contrato" => $cuenta["CONTRATO"],
                "fecha" => $cuenta["FECHA"],
                "saldo" => $saldo,
                "devengo" => $devengo,
                "tasa" => $tasa,
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_APLICA_DEVENGO" => JobsDao::AplicaDevengo($datos),
            ];
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizado -> Comprobación de Devengo Ahorro");
    }
}
