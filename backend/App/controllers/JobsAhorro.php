<?PHP

namespace App\controllers;

include 'C:/xampp/htdocs/mcm/backend/App/models/JobsAhorro.php';

use \App\models\JobsAhorro as JobsDao;

date_default_timezone_set('America/Mexico_City');

$jobs = new JobsAhorro();
$jobs->DevengoInteresAhorroDiario();
$jobs->LiquidaInversion();
$jobs->RechazaSolicitudesSinAtender();
$jobs->SucursalesSinArqueo();
$jobs->CapturaSaldosSucursales();

class JobsAhorro
{
    public function SaveLog($tdatos)
    {
        $archivo = "C:/xampp/JobsAhorro_php.log";

        clearstatcache();
        if (file_exists($archivo) && filesize($archivo) > 10 * 1024 * 1024) { // 10 MB
            $nuevoNombre = "C:/xampp/Jobs_php_" . date('Ymd') . ".log";
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
        $creditos = JobsDao::GetCreditosActivos();
        if (!$creditos["success"]) return self::SaveLog("Error al obtener los créditos activos: " . $creditos["error"]);
        if (count($creditos["datos"]) == 0) return self::SaveLog("No se encontraron créditos activos para aplicar devengo.");

        foreach ($creditos["datos"] as $key => $credito) {
            $saldo = $credito["SALDO"];
            $tasa = $credito["TASA"] / 100;
            $devengo = $saldo * ($tasa / 365);

            $datos = [
                "cliente" => $credito["CLIENTE"],
                "contrato" => $credito["CONTRATO"],
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
}
