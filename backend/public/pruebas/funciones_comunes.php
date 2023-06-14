<?php

function armaTabla($result, $thead){

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

    return $tabla;
}


?>
