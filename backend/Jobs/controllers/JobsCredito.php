<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . '/../Core/Job.php';
include_once dirname(__DIR__) . '/models/JobsCredito.php';

use Core\Job;
use Jobs\models\JobsCredito as JobsDao;

class JobsCredito extends Job
{
    public function __construct()
    {
        parent::__construct('JobsCredito');
    }

    public function JobCheques()
    {
        self::SaveLog('Iniciando Job Cheques');
        $resumen = [];
        $creditos = JobsDao::CreditosAutorizados();
        if (!$creditos['success']) return self::SaveLog($creditos['mensaje'] . ': ' . $creditos['error']);
        if (count($creditos['datos']) == 0) return self::SaveLog('No hay créditos autorizados');

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
        self::SaveLog('Finalizando Job Cheques');

        echo 'Job Cheques finalizado';
    }

    public function ReInserta($archivo)
    {
        self::SaveLog('Iniciando ReInserta');
        $resumen = [];
        $creditos = json_decode(file_get_contents($archivo), true);

        if (!is_array($creditos)) $creditos = [$creditos];

        foreach ($creditos as $key => $credito) {
            $datos = [
                //Datos para MP, JP y MPC
                'prmCDGCLNS' => $credito['datos']['prmCDGCLNS'],
                'prmCICLO' => $credito['datos']['prmCICLO'],
                'prmINICIO' => $credito['datos']['prmINICIO'],
                'vINTERES' => $credito['datos']['vINTERES'],
                'vCLIENTE' => $credito['datos']['vCLIENTE']
            ];

            $resumen[] = [
                'fecha' => date('Y-m-d H:i:s'),
                'datos' => $datos,
                'RES_MP_INSERT' => JobsDao::InsertarMP($datos),
                'RES_JP_INSERT' => JobsDao::InsertarJP($datos),
                'RES_MPC_INSERT' => JobsDao::InsertarMPC($datos),
            ];
        }

        self::SaveLog(json_encode($resumen, JSON_PRETTY_PRINT));
        self::SaveLog('Finalizando ReInserta');

        echo 'ReInserta finalizado';
    }
}

$jobs = new JobsCredito();
$jobs->JobCheques();
// $jobs->ReInserta('C:\Users\Alberto\Desktop\prueba.json');
