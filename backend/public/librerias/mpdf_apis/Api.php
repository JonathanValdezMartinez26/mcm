#!/usr/bin/php
<?php
    //print_r($_SERVER['argc']);
    //print_r($_SERVER['argv']);
    $nombre_controller = $_SERVER['argv'][1];
    $ids = json_decode($_SERVER['argv'][2]);
    
/*
    if($ids!=''){
      print_r($ids);
    }else{
      echo "esta vacio";
    }
    exit;

    */
		define("PATH", "/home/granja/backend/");
    define("PATH_WEB","http://52.32.114.10:8070/".$nombre_controller."/");
    include(PATH."public/librerias/mpdf/mpdf.php");
    $mpdf=new mPDF('c');
    $mpdf->defaultPageNumStyle = 'I';
    $mpdf->h2toc = array('H5'=>0,'H6'=>1);
    $style =<<<html
    <style>
      .imagen{
        width:100%;
        height: 150px;
        background: url(/img/ag_logo.png) no-repeat center center fixed;
        background-size: cover;
        -moz-background-size: cover;
        -webkit-background-size: cover
        -o-background-size: cover;
      }

      .titulo{
        margin-top: 30px;
        color: #F5AA3C;
      }
    </style>
html;
    $header = utf8_decode(utf8_encode(file_get_contents(PATH_WEB."pdf/".json_encode($ids))));

    $mpdf->WriteHTML(file_get_contents(PATH."public/librerias/mpdf/mpdf.css"),1);
    $mpdf->WriteHTML($style,1);
    $mpdf->WriteHTML($header,2);

    $nombre_archivo = "MPDF_".uniqid().".pdf";/* se genera un nombre unico para el archivo pdf*/
	  $mpdf->Output(PATH."public/PDF/".$nombre_archivo);/* se genera el pdf en la ruta especificada*/
	  echo $nombre_archivo;/* se imprime el nombre del archivo para poder retornarlo a CrmCatalogo/index */
	exit;
?>
