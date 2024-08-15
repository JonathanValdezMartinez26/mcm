<?php

namespace App\components;

use App\components\TarjetaDedo;


/**
 * Clase BuscarCliente
 * 
 * Representa el componente para buscar un cliente en el sistema.
 */
class BuscarCliente
{
    private $recordatorio;
    private $dedo;

    /**
     * Constructor de la clase BuscarCliente.
     * 
     * @param string $recordatorio Recordatorio o indicaciones para la cajera.
     */
    public function __construct($recordatorio = "")
    {
        $this->recordatorio = $recordatorio;
        $this->dedo = new TarjetaDedo("izquierda", 1);
    }

    /**
     * Método para mostrar el componente de búsqueda de cliente.
     * 
     * @return string El código HTML del componente.
     */
    public function mostrar()
    {
        return <<<html
        <div class="row" style="height: 100px; display: flex; align-items: flex-start;">
            <div class="col-md-6">
                <p>{$this->recordatorio}</p>
                </div>
            <div class="col-md-3">
                <label for="movil">Código de cliente SICAFIN *</label>
                <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="clienteBuscado" name="clienteBuscado" placeholder="000000" required>
            </div>
            <div class="col-md-3" style="display: flex; align-items: flex-start; justify-content: space-between; height: 100%;">
                <button type="button" class="btn btn-primary" id="btnBskClnt" onclick="buscaCliente()" style="margin-top: 25px;">
                    <i class="fa fa-search"></i> Buscar
                </button>
                <div style="height: 80%; display: flex;" onclick=showHuella()>
                    {$this->dedo->imagen()}
                </div>
            </div>
        </div>
        <hr>
        html;
    }
}
