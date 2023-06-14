<?php
include 'funciones_comunes.php';

function muestraEmpleado(){



$resul1 = array('empleado_id'=>1,
                'nombre'=>'juan',
                'apellido'=>'medina',
                'estatus'=>1);

$resul2 = array('empleado_id'=>2,
                'nombre'=>'juan 2',
                'apellido'=>'medina 2',
                'estatus'=>1);

$resul3 = array('empleado_id'=>3,
                'nombre'=>'juan 3',
                'apellido'=>'medina ',
                'estatus'=>0);

$result = array($resul1, $resul2, $resul3);

$thead=array('empleado_id'=>'Empleado','nombre'=>'Nombres',
                'apellido'=>'Apellidos','estatus'=>'Activo/Inactivo');


    return armaTabla($result, $thead);
}

function registroFormulario(){

}

/*
$theadHtml = '';
foreach($thead AS $key=>$value){
    
    $theadHtml.=<<<html
	<th>$value</th>
html;
  
}


$tabla=<<<html
<table>
    <thead>
	$theadHtml
    </thead>
    <tbody>
html;

foreach($result AS $value){
    //echo "+++{$value['nombre']} , {$value['empleado_id']}+++\n}";

    $tabla.= "<tr>";
    foreach($thead AS $k=>$val){
	$tabla .= "<td>{$value[$k]}</td>";
    }
    $tabla.= "</tr>";
}


$tabla.=<<<html
</tbody>
</table>
html;
*/


?>
