<?php

namespace App\components;

use App\components\TarjetaDedo;

/**
 * Clase TarjetaMano
 * 
 *  Representa un componente que muestra una tarjeta de mano con dedos para la captura de huellas.
 */
class TarjetaMano
{
    private $mano;
    private $dedos;
    private $dedosReqeridos;
    private $ladoBorde;

    /**
     * Constructor de la clase TarjetaMano.
     * 
     * @param string $mano La mano a la que pertenece la tarjeta ('derecha' o 'izquierda').
     * @param int $dedosReqeridos (Opcional) El número de dedos requeridos en la tarjeta. Por defecto 2.
     */
    public function __construct($mano, $dedosReqeridos = 2)
    {
        $this->mano = $mano;
        $this->dedosReqeridos = $dedosReqeridos;
        $this->ladoBorde = $mano == 'derecha' ? 'left' : 'right';

        for ($i = 1; $i <= $this->dedosReqeridos; $i++) {
            $this->dedos[] = new TarjetaDedo($mano, $i);
        }
    }

    /**
     * Método para mostrar la tarjeta de la mano con los dedos a capturar.
     * 
     * @return string El código HTML del componente.
     */
    public function mostrar()
    {
        $dedos = '';
        foreach ($this->dedos as $dedo) {
            $dedos .= $dedo->mostrar();
        }

        return '<div class="col-md-6" style="text-align: center; border-' . $this->ladoBorde . ': 1px solid #000000;">
                    <h3>Mano ' . $this->mano . '</h3>
                    <div id="mano' . $this->mano . '"  class="form-group" style="margin: 0; display: flex; justify-content: space-around;">
                        ' . $dedos . '
                    </div>
                </div>';
    }
}
