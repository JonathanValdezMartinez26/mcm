<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . '/../Core/Job.php';
include_once dirname(__DIR__) . '/models/JobsCredito.php';
include_once dirname(__DIR__) . '/../App/libs/PHPMailer/Mensajero.php';
include_once dirname(__DIR__) . '/../App/libs/PhpSpreadsheet/PhpSpreadsheet.php';

use Core\Job;
use Jobs\models\JobsCredito as JobsDao;
use Mensajero;

class JobsCredito extends Job
{
    public function __construct()
    {
        parent::__construct('JobsCredito');
    }

    public function JobCheques()
    {
        self::SaveLog('Inicio');
        $resumen = [];
        $creditos = JobsDao::GetCreditosAutorizados();
        if (!$creditos['success']) return self::SaveLog('Finalizado con error: ' . $creditos['mensaje'] . '->' . $creditos['error']);
        if (count($creditos['datos']) == 0) return self::SaveLog('Finalizado: No hay créditos autorizados');

        foreach ($creditos['datos'] as $key => $credito) {
            $chequera = JobsDao::GetNoChequera($credito['CDGCO']);
            if (!$chequera['success'] || count($chequera['datos']) == 0) {
                $resumen[] = [
                    'credito' => $credito['CDGCO'],
                    'error' => self::SaveLog($chequera['mensaje'] . ': ' . ($chequera['error'] ?? ''))
                ];
                continue;
            }

            $cheque = JobsDao::GetNoCheque($chequera['datos']['CDGCB']);
            if (!$cheque['success'] || count($cheque['datos']) == 0) {
                $resumen[] = [
                    'credito' => $credito['CDGCO'],
                    'chequera' => $chequera['datos']['CDGCB'],
                    'error' => self::SaveLog($cheque['mensaje'] . ': ' . ($cheque['error'] ?? ''))
                ];
                continue;
            }

            $datos = [
                //Datos para actualizar PRC y PRN
                'cheque' => $cheque['datos']['CHQSIG'],
                'cdgcb' => $chequera['datos']['CDGCB'],
                'cdgcl' => $credito['CDGCL'],
                'cdgns' => $credito['CDGNS'],
                'ciclo' => $credito['CICLO'],
                'cantautor' => $credito['CANTAUTOR'],
                //Datos para MP, JP y MPC
                'prmCDGCLNS' => $credito['CDGNS'],
                'prmCICLO' => $credito['CICLO'],
                'prmINICIO' => $credito['INICIO'],
                'vINTERES' => $credito['INTERES'],
                'vCLIENTE' => $credito['CDGCL'],
            ];

            $resumen[] = JobsDao::GeneraCheques($datos);
        }

        self::SaveLog(json_encode($resumen));
        self::SaveLog('Finalizado');
    }

    public function SolicitudesFinalizadas()
    {
        self::SaveLog('Inicio');
        $resumen = [];
        $creditos = JobsDao::GetSolicitudes();

        if (!$creditos['success']) return self::SaveLog('Finalizado con error: ' . $creditos['mensaje'] . '->' . $creditos['error']);
        if (count($creditos['datos']) == 0) return self::SaveLog('Finalizado: No hay solicitudes de crédito por procesar');

        $destAprobadas = [];
        $destRechazadas = [];

        $destA = JobsDao::GetDestinatarios('SolicitudesAprobadas');
        $destR = JobsDao::GetDestinatarios('SolicitudesRechazadas');

        if ($destA['success'] && count($destA['datos']) > 0) {
            foreach ($destA['datos'] as $key => $d) {
                $destAprobadas[] = $d['CORREO'];
            }
        }

        if ($destR['success'] && count($destR['datos']) > 0) {
            foreach ($destR['datos'] as $key => $d) {
                $destRechazadas[] = $d['CORREO'];
            }
        }

        foreach ($creditos['datos'] as $key => $credito) {
            $aprobada = str_starts_with($credito['ESTATUS'], 'LISTA');

            $r = $aprobada ?
                JobsDao::ProcesaSolicitudAprobada($credito) :
                JobsDao::ProcesaSolicitudRechazada($credito);

            $r['datos'] = [
                'credito' => $credito['CDGNS'],
                'ciclo' => $credito['CICLO'],
                'fechaSolicitud' => $credito['SOLICITUD'],
                'concluyo' => $credito['CDGPE']
            ];

            if ($r['success']) {
                $dest = $aprobada ? $destAprobadas : $destRechazadas;
                $plantilla = $this->Plantilla_mail_Solicitud_Finalizada($credito, $aprobada);
                $tipo = $aprobada ? 'Aprobación' : 'Rechazo';

                Mensajero::EnviarCorreo(
                    $dest,
                    $tipo . ' de solicitud de crédito por Call Center',
                    Mensajero::Notificaciones($plantilla)
                );
            }

            $resumen[] = $r;
            //genera solo 1 solicitud para pruebas
            //break;
        }

        self::SaveLog(json_encode($resumen));
        self::SaveLog('Finalizado');
    }

    public function ResumenRechazadas()
    {
        self::SaveLog('Inicio');
        $procesadas = self::ReadLog('SolicitudesRechazadas');

        if (count($procesadas) == 0) return self::SaveLog('Finalizado: No se procesaron rechazos de crédito');

        $filas = [];
        foreach ($procesadas as $key => $procesada) {
            if ($procesada['success']) $filas[] = $procesada['datos'];
        }

        if (count($filas) == 0) return self::SaveLog('Finalizado: Los rechazos de crédito no se procesaron correctamente');

        $columnas = [
            ['letra' => 'A', 'titulo' => 'Crédito', 'campo' => 'credito', 'estilo' => []],
            ['letra' => 'B', 'titulo' => 'Ciclo', 'campo' => 'ciclo', 'estilo' => []],
            ['letra' => 'C', 'titulo' => 'Solicitud', 'campo' => 'solicitud', 'estilo' => []],
            ['letra' => 'D', 'titulo' => 'Concluyó', 'campo' => 'concluyo', 'estilo' => []],
        ];


        self::SaveLog('Finalizado');
    }

    private function Plantilla_mail_Solicitud_Finalizada($credito, $aprobada)
    {
        $pasosFinalesA = <<<HTML
            <p style="text-align: center">
                Para completar el proceso imprima la documentación legal correspondiente, si tiene alguna duda o inconveniente, comuníquese con {$credito['NOMBRE_PE']} ({$credito['CDGPE']}) o con el gerente de call center.
            </p>
            <p style="text-align: center">
                <b>Asegúrese de seguir todos los protocolos establecidos para la correcta gestión y archivo de los documentos.</b>
            </p>
        HTML;

        $pasosFinalesR = <<<HTML
            <p style="text-align: center">
                Si tiene alguna duda o inconveniente referente al rechazo de la solicitud, comuníquese con {$credito['NOMBRE_PE']} ({$credito['CDGPE']}) o con el gerente de call center.
            </p>
        HTML;

        $titulo = $aprobada ? '✅ Solicitud de crédito APROBADA.' : '❌ Solicitud de crédito RECHAZADA.';
        $pasosFinales = $aprobada ? $pasosFinalesA : $pasosFinalesR;

        return <<<HTML
            <!-- Encabezado -->
            <h2 style="text-align: center">$titulo</h2>
            <!-- Información General -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    📄 Información General
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>Cliente:</b> {$credito['CL']} - {$credito['NOMBRE_CL']}</li>
                    <li>🔸<b>Crédito:</b> {$credito['CDGNS']}</li>
                    <li>🔸<b>Ciclo:</b> {$credito['CICLO']}</li>
                    <li>🔸<b>Fecha de captura:</b> {$credito['SOLICITUD']}</li>
                    <li>🔸<b>Región:</b> {$credito['RG']} - {$credito['NOMBRE_RG']}</li>
                    <li>🔸<b>Agencia:</b> {$credito['CO']} - {$credito['NOMBRE_CO']}</li>
                    <li>🔸<b>Estatus final:</b> {$credito['ESTATUS']}</li>
                </ul>
            </div>

            <!-- Detalle de llamadas realizadas -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    ☎️ Detalle de llamadas realizadas
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>Total de llamadas:</b> {$credito['NO_LLAMADAS']}</li>
                    <li>🔸<b>Intentos realizados:</b> {$credito['INTENTOS']}</li>
                    <li>🔸<b>Fecha primera llamada:</b> {$credito['PRIMERA_LLAMADA']}</li>
                    <li>🔸<b>Fecha última llamada:</b> {$credito['ULTIMA_LLAMADA']}</li>
                </ul>
            </div>

            <!-- Comentarios del Call Center -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    📝 Comentarios del Call Center
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>Comentario inicial:</b> {$credito['COMENTARIO_INICIAL']}</li>
                    <li>🔸<b>Comentario final:</b> {$credito['COMENTARIO_FINAL']}</li>
                </ul>
            </div>

            <!-- Próximos pasos -->
            <div style="padding-top: 14px">
                $pasosFinales
            </div>
        HTML;
    }
}

if (isset($argv[1])) {
    $jobs = new JobsCredito();

    switch ($argv[1]) {
        case 'JobCheques':
            $jobs->JobCheques();
            break;
        case 'SolicitudesFinalizadas':
            $jobs->SolicitudesFinalizadas();
            break;
        case 'help':
            echo 'JobCheques: Actualiza los cheques de los créditos autorizados\n';
            echo 'SolicitudesCredito: Actualiza las solicitudes de crédito\n';
            break;
        default:
            echo 'No se encontró el job solicitado.\nEjecute "php JobsAhorro.php help" para ver los jobs disponibles.\n';
            break;
    }
} else echo 'Debe especificar el job a ejecutar.\nEjecute "php JobsAhorro.php help" para ver los jobs disponibles.\n';
