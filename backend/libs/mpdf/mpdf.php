<?php

require __DIR__ . '/vendor/autoload.php';

class mPDF extends \Mpdf\Mpdf
{
    public function __construct($configuracion = null)
    {
        parent::__construct($configuracion);
    }
}
