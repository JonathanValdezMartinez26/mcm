<?php include 'proceso.php'; ?>
<html>
<head>  
  <style>
	thead {color:green;}
	tbody {color:blue}

	table, th, td {border: 1px solid black;}
    </style>
</head>
<body>
<h1>Reporte de empleados</h1>
<p> Revisa con cuidado la informacion que se presenta acontinuacion</p>

<br />
<br />
<?php echo muestraEmpleado(); ?>
</body>
</html>
