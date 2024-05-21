<?PHP

namespace App\controllers;

include 'C:/xampp/htdocs/mcm/backend/App/models/Jobs.php';

use \App\Models\CajaAhorro as CADao;
use \App\models\Jobs as JobsDao;

date_default_timezone_set('America/Mexico_City');

$jobs = new Jobs();
$jobs->DevengoInteresAhorroDiario();
$jobs->LiquidaInversion();

class Jobs
{
    public function SaveLog($tdatos)
    {
        $archivo = "C:/xampp/Jobs_php.log";

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
        if ($creditos["success"]) return self::SaveLog("Error al obtener los créditos activos:" . $creditos["error"]);
        if (count($creditos["datos"]) == 0) return self::SaveLog("No se encontraron créditos activos para aplicar devengo.");

        foreach ($creditos["datos"] as $key => $credito) {
            $saldo = $credito["SALDO"];
            $tasa = $credito["TASA"] / 100;
            $devengo = $saldo * ($tasa / 360);

            $datos = [
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
        if (!$inversiones["success"]) return self::SaveLog("Error al obtener las inversiones:" . $inversiones["error"]);
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
        if (!$solicitudes["success"]) return self::SaveLog("Error al obtener las solicitudes sin atender:" . $solicitudes["error"]);
        if (count($solicitudes["datos"]) == 0) return self::SaveLog("No se encontraron solicitudes sin atender para rechazar.");

        foreach ($solicitudes["datos"] as $key => $solicitud) {
            $datosRetiro = CADao::ResumenEntregaRetiro([
                "idSolicitud" => $solicitud["ID_SOLICITUD"]
            ]);
            $r = json_decode($datosRetiro);
            $d = $r->datos;

            $datosRechazo = [
                "idSolicitud" => $solicitud["ID_SOLICITUD"],
                "estatus" => "R",
                "ejecutivo" => "{$_SESSION['usuario']}",
            ];

            $datosDevolucion = [
                "cliente" => $d["CLIENTE"],
                "contrato" => $d["CONTRATO"],
                "monto" => $d["MONTO"],
                "ejecutivo" => "{$_SESSION['usuario']}",
                "sucursal" => "{$_SESSION['cdgco_ahorro']}",
                "tipo" => $d["TIPO_RETIRO"],
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos_rechazp" => $datosRechazo,
                "RES_RECHAZA_SOLICITUD" => CADao::ActualizaSolicitudRetiro($datosRechazo),
                "datos_devolucion" => $datosDevolucion,
                "RES_DEVUELVE_MONTO" => CADao::DevolucionRetiro($datosDevolucion),
            ];
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizado -> Rechazo de Solicitudes de retiro sin Atender");
    }
}
