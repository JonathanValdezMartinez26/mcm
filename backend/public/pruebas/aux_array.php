<?php

$array = array(
	array('data'=>1,
		'busqueda'=>array(1,2,3,4,5)
	),
	array('data'=>2,
                'busqueda'=>array(1,2,3)
        ),
	array('data'=>3,
                'busqueda'=>array(1,2,3,4,5,6,7)
        ),
	array('data'=>4,
                'busqueda'=>array(1,2)
        )
);

$variableAux = array('key'=>-1, 'count'=>0);
$countGeneral = count($array);
foreach($array AS $key => $value){

    $count = count($value['busqueda']);
    if($variableAux['key'] == -1){
	$variableAux['key'] = $key;
	$variableAux['count'] = $count;
    }

    $next = $key + 1;
    if($next>($countGeneral -1))
	$next = $key;

    $busquedaSiguiente = $array[$next];
    $busquedaSiguienteCount = count($busquedaSiguiente['busqueda']);


    echo "comparacion: {$variableAux['count']} --- $busquedaSiguienteCount --- \n";

    if($busquedaSiguienteCount >= $variableAux['count']){
	$variableAux['key'] = $next;
	$variableAux['count'] = $busquedaSiguienteCount;
    }
}

echo "***RESULTADO*****\n";
print_r($variableAux);

?>
