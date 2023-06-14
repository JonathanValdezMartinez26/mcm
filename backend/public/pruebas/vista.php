<html>
<head>
<style type="text/css">
.form label{
    color: #fff;
}
 
 
</style>
</head>
<body>
<p>Insertar Registro</p>
 
<form action="empleado_insert.php" method="post"></p>
    <div class="form"><label class="form" for="nombre">Nombre:</label>&nbsp;&nbsp;<input type="text" name="nombre" placeholder="Ingresa el Nombre"></div>
    <div class="form"><label for="apellidop">Apellido Paterno:</label>&nbsp;&nbsp;&nbsp;<input type="text" name="apellidop" placeholder="Ingresa el Apellido Paterno"></div>
    <div class="form"><label for="apellidom">Apellido Materno:</label>&nbsp;&nbsp;<input type="text" name="apellidom" placeholder="Ingresa el Apellido Materno"></div>
    <div class="form"><label for="estatus">Estatus:</label>&nbsp;&nbsp;<input type="text" name="estatus" placeholder="Estatus">
    <div class="form"><input type="hidden" name="admin" value="1"></div>
    <div class="form"><input type="submit"></div>
 
 
 
</form>
</body>
</html>
