<?php

namespace App\components;

/**
 * Clase Menu
 * 
 * Representa el componente para el menu del sistema.
 */
class BuscarCliente
{
    /**
     * Perfil del usuario.
     * 
     * @var string
     */
    private $perfil;

    /**
     * Usuario.
     * 
     * @var string
     */
    private $usuario;

    /**
     * Permisos para modulo de Ahorro.
     * 
     * @var string
     */
    private $ahorro;

    /**
     * Constructor de la clase BuscarCliente.
     * 
     * @param string $recordatorio Recordatorio o indicaciones para la cajera.
     */
    public function __construct($_perfil, $_usuario, $_ahorro)
    {
        $this->perfil = $_perfil;
        $this->usuario = $_usuario;
        $this->ahorro = $_ahorro;
    }

    private function Menu()
    {
        return [
            [
                'seccion' => 'General WEB AHORRO',
                'opciones' => [
                    [
                        'titulo' => 'Mi espacio',
                        'icono' => 'glyphicon glyphicon-usd',
                        'url' => [
                            'directorio' => '/Ahorro/CuentaCorriente/',
                            'permisos' => ['AMGM'],
                        ]
                    ],
                    [
                        'titulo' => 'Admin Sucursales',
                        'icono' => 'glyphicon glyphicon-paste',
                        'url' => [
                            '/AdminSucursales/SaldosDiarios/',
                            'permisos' => ['AMGM', 'LGFR', 'PAES', 'PMAB', 'DCRI', 'GUGJ', 'JUSA', 'HEDC', 'PHEE'],
                        ]
                    ]
                ]
            ],
            [
                'seccion' => 'GENERAL',
                'opciones' => [
                    [
                        'titulo' => 'Pagos',
                        'icono' => 'glyphicon glyphicon-usd',
                        'items' => [
                            [
                                'titulo' => 'Administración Pagos',
                                'url' => [
                                    'directorio' => '/Pagos/',
                                    'permisos' => ['ADMIN', 'LGFR', 'MGJC', 'MCDP']
                                ]
                            ],
                            [
                                'titulo' => 'Recepción Pagos App',
                                'url' => [
                                    'directorio' => '/Pagos/CorteEjecutivo/',
                                    'permisos' => ['ADMIN']
                                ]
                            ],
                            [
                                'titulo' => 'Reimprimir Recibos App',
                                'url' => [
                                    'directorio' => '/Pagos/CorteEjecutivoReimprimir/',
                                    'permisos' => ['ADMIN']
                                ]
                            ],
                            [
                                'titulo' => 'Layout Contable',
                                'url' => [
                                    'directorio' => '/Pagos/Layout/',
                                    'permisXos' => ['ADMIN', 'ACALL', 'LAYOU']
                                ]
                            ],
                            [
                                'titulo' => 'Registro de Pagos Caja',
                                'url' => [
                                    'directorio' => '/Pagos/PagosRegistro/',
                                    'permisos' => ['ADMIN', 'CAJA', 'LGFR', 'PLMV', 'PMAB', 'MGJC', 'AVGA', 'FLCR', 'COCS', 'GOIY', 'DAGC', 'COVG', 'TESP']
                                ]
                            ],
                            [
                                'titulo' => 'Consulta de Pagos Cliente',
                                'url' => [
                                    'directorio' => '/Pagos/PagosConsultaUsuarios/',
                                    'permisos' => ['ACALL']
                                ]
                            ],
                            [
                                'titulo' => 'Consultar Pagos',
                                'url' => [
                                    'directorio' => '/Pagos/PagosConsulta/',
                                    'permisos' => ['ADMIN', 'CAJA', 'GTOCA', 'AMOCA', 'OCOF', 'CPAGO', 'ACALL']
                                ]
                            ],
                        ]
                    ],
                    [
                        'titulo' => 'Créditos',
                        'icono' => 'fa fa-users',
                        'items' => [
                            [
                                'titulo' => 'Control de Garantías',
                                'url' => [
                                    'directorio' => '/Creditos/ControlGarantias/',
                                    'permisos' => ['control_garantias']
                                ]
                            ],
                            [
                                'titulo' => 'Calculo Descuento Telaraña',
                                'url' => [
                                    'directorio' => '/Promociones/Telarana/',
                                    'permisos' => ['calculo_telarana']
                                ]
                            ],
                            [
                                'titulo' => 'Registro Telaraña',
                                'url' => [
                                    'directorio' => '/Validaciones/RegistroTelarana/',
                                    'permisos' => ['registro_telarana']
                                ]
                            ],
                            [
                                'titulo' => 'Actualización de Créditos',
                                'url' => [
                                    'directorio' => '/Creditos/ActualizaCredito/',
                                    'permisos' => ['actualiza_creditos']
                                ]
                            ],
                            [
                                'titulo' => 'Cambio de Sucursal',
                                'url' => [
                                    'directorio' => '/Creditos/CambioSucursal/',
                                    'permisos' => ['cambio_sucursal']
                                ]
                            ],
                            [
                                'titulo' => 'Cancelación de Ref',
                                'url' => [
                                    'directorio' => '/Creditos/CancelaRef/',
                                    'permisos' => ['cancela_ref']
                                ]
                            ],
                            [
                                'titulo' => 'Corrección Mov Ajustes',
                                'url' => [
                                    'directorio' => '/Creditos/CorreccionMovAjustes/',
                                    'permisos' => ['correccion_mov_ajustes']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Call Center',
                        'icono' => 'glyphicon glyphicon-phone-alt',
                        'items' => [
                            [
                                'titulo' => 'Asignar Sucursales',
                                'url' => [
                                    'directorio' => '/CallCenter/Administracion/',
                                    'permisos' => ['asignar_sucursales']
                                ]
                            ],
                            [
                                'titulo' => 'Solicitudes de Prorroga',
                                'url' => [
                                    'directorio' => '/CallCenter/Prorroga/',
                                    'permisos' => ['solicitudes_prorroga']
                                ]
                            ],
                            [
                                'titulo' => 'Reactivar Solicitudes',
                                'url' => [
                                    'directorio' => '/CallCenter/Reactivar/',
                                    'permisos' => ['reactivar_solicitudes']
                                ]
                            ],
                            [
                                'titulo' => 'Búsqueda Rápida',
                                'url' => [
                                    'directorio' => '/CallCenter/Busqueda/',
                                    'permisos' => ['busqueda_rapida']
                                ]
                            ],
                            [
                                'titulo' => 'Pendientes (Analistas)',
                                'url' => [
                                    'directorio' => '/CallCenter/Pendientes/',
                                    'permisos' => ['pendientes_analistas']
                                ]
                            ],
                            [
                                'titulo' => 'Históricos (Analistas)',
                                'url' => [
                                    'directorio' => '/CallCenter/Historico/',
                                    'permisos' => ['historicos_analistas']
                                ]
                            ],
                            [
                                'titulo' => 'Postventa',
                                'url' => [
                                    'directorio' => '/CallCenter/EncuestaPostventa/',
                                    'permisos' => ['postventa']
                                ]
                            ],
                            [
                                'titulo' => 'Reporte Postventa',
                                'url' => [
                                    'directorio' => '/CallCenter/ReporteEncuestaPostventa/',
                                    'permisos' => ['reporte_postventa']
                                ]
                            ],
                            [
                                'titulo' => 'Supervisión Postventa',
                                'url' => [
                                    'directorio' => '/CallCenter/SupervisionEncuestaPostventa/',
                                    'permisos' => ['supervision_postventa']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Cultiva',
                        'icono' => 'glyphicon glyphicon-globe',
                        'items' => [
                            [
                                'titulo' => 'Consulta Clientes Solicitudes',
                                'url' => [
                                    'directorio' => '/Cultiva/',
                                    'permisos' => ['consulta_clientes']
                                ]
                            ],
                            [
                                'titulo' => 'Reingresar Clientes a Grupo',
                                'url' => [
                                    'directorio' => '/Cultiva/ReingresarClientesCredito/',
                                    'permisos' => ['reingresar_clientes']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Incidencias MCM',
                        'icono' => 'glyphicon glyphicon-cog',
                        'url' => '',
                        'permisos' => ['incidencias_module'],
                        'items' => [
                            [
                                'titulo' => 'Error Autorizar y/o Rechazar Solicitud',
                                'url' => [
                                    'directorio' => '/Incidencias/AutorizaRechazaSolicitud/',
                                    'permisos' => ['error_autoriza_rechaza']
                                ]
                            ],
                            [
                                'titulo' => 'Calculo de Devengos',
                                'url' => [
                                    'directorio' => '/Incidencias/CalculoDevengo/',
                                    'permisos' => ['calculo_devengos']
                                ]
                            ],
                            [
                                'titulo' => 'Cancelar Refinanciamiento',
                                'url' => [
                                    'directorio' => '/Incidencias/CancelarRefinanciamiento/',
                                    'permisos' => ['cancelar_refinanciamiento']
                                ]
                            ],
                            [
                                'titulo' => 'Cambio de Fecha para Pagos No conciliados del día',
                                'url' => [
                                    'directorio' => '/Incidencias/ActualizarFechaPagosNoConciliados/',
                                    'permisos' => ['cambio_fecha_pagos']
                                ]
                            ],
                            [
                                'titulo' => 'Telaraña agregar referencias',
                                'url' => [
                                    'directorio' => '/Incidencias/ActualizarFechaPagosNoConciliados/',
                                    'permisos' => ['telarana_referencias']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Administrar Caja',
                        'icono' => 'glyphicon glyphicon-cog',
                        'items' => [
                            [
                                'titulo' => 'Ajustar Hora de Cierre',
                                'url' => [
                                    'directorio' => '/Pagos/AjusteHoraCierre/',
                                    'permisos' => ['ajuste_hora_cierre']
                                ]
                            ],
                            [
                                'titulo' => 'Asignación Días Festivos',
                                'url' => [
                                    'directorio' => '/Pagos/DiasFestivos/',
                                    'permisos' => ['dias_festivos']
                                ]
                            ],
                            [
                                'titulo' => 'Reporte Usuarios SICAFIN MCM',
                                'url' => [
                                    'directorio' => '/Reportes/UsuariosMCM/',
                                    'permisos' => ['reporte_usuarios_mcm']
                                ]
                            ],
                            [
                                'titulo' => 'Reporte Usuarios SICAFIN Cultiva',
                                'url' => [
                                    'directorio' => '/Reportes/UsuariosCultiva/',
                                    'permisos' => ['reporte_usuarios_cultiva']
                                ]
                            ],
                            [
                                'titulo' => 'Situación Cartera',
                                'url' => [
                                    'directorio' => '/Creditos/cierreDiario',
                                    'permisos' => ['situacion_cartera']
                                ]
                            ],
                            [
                                'titulo' => 'Administración de Correos',
                                'url' => [
                                    'directorio' => '/Creditos/AdminCorreos',
                                    'permisos' => ['admin_correos']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Indicadores',
                        'icono' => 'glyphicon glyphicon-cog',
                        'items' => [
                            [
                                'titulo' => 'Productividad Operaciones',
                                'url' => [
                                    'directorio' => '/Indicadores/ProductividadOP/',
                                    'permisos' => ['productividad_operaciones']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private function mostrar()
    {
        $menu = $this->Menu();
        $html = <<<HTML
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                <div class="menu_section" style="overflow: auto">
        HTML;

        foreach ($menu as $seccion) {
            $html .= $this->mostrarSeccion($seccion);
        }

        $html .= <<<HTML
                </div>
            </div>
        HTML;

        return $html;
    }

    private function mostrarSeccion($seccion)
    {
        $html = '';
        foreach ($seccion['opciones'] as $opcion) {
            $html .= $this->mostrarOpcion($opcion);
        }

        if ($html !== '') {
            return <<<HTML
                <h3>{$seccion['seccion']}</h3>
                <ul class="nav side-menu">
                    {$seccion}
                </ul>
            HTML;
        }
    }

    private function mostrarOpcion($opcion)
    {
        if (isset($opcion['items'])) {
            $html = '';
            foreach ($opcion['items'] as $item) {
                $html .= $this->mostrarOpcion($item);
            }

            if ($html !== '') {
                return <<<HTML
                    <li>
                        <a>
                            <span class="{$opcion['icono']}">&nbsp;</span>{$opcion['titulo']}<span class="fa fa-chevron-down"></span>
                        </a>
                        <ul class="nav child_menu">
                            {$html}
                        </ul>
                    </li>
                HTML;
            }
        } else {
            if ($this->ValidaPermisos($opcion['url']['permisos'])) {
                return <<<HTML
                    <li>
                        <a href="{$opcion['url']['directorio']}">
                            <span class="{$opcion['icono']}">&nbsp;</span>{$opcion['titulo']}
                        </a>
                    </li>
                HTML;
            }
        }
    }

    private function ValidaPermisos($permisos)
    {
        return in_array($this->perfil, $permisos) || in_array($this->usuario, $permisos);
    }

}