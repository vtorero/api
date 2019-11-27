<html>
<body>
<?php 
$sql="SELECT * FROM adops.skoda where source='skoda_nov2019_publimetro'";

$db = new mysqli("localhost","marife","libido16","adops");

//mysqli_set_charset($db, 'utf8');
if (mysqli_connect_errno()) {
    printf("Conexión fallida: %s\n", mysqli_connect_error());
    exit();
}
echo "<h2>Listado inscritos Skoda</h2>";
echo "<table border='1'>";
echo "<tr><th>Nro</th><th>Nombres</th><th>Apellidos</th><th>RUT</th><th>Telèfono</th><th>Email</th><th>Marca</th><th>Modelo</th><th>Concesionario</th><th>Fecha</th><th>Dispositivo</th></tr>";
$contador=1;
$resultado = $db->query($sql);
while ($fila = $resultado->fetch_array()) {
 echo "<tr><td>".$contador."</td><td>".$fila['nombres']."</td><td>".$fila['apellidos']."</td><td>".$fila['rut']."</td><td>".$fila['telefono']."</td><td>".$fila['correo']."</td><td>".$fila['marca']."</td><td>".$fila['modelo']."</td><td>".$fila['concesionario']."</td><td>".$fila['fecha_registro']."</td><td>".$fila['dispositivo']."</td></tr>";
$contador++;
} 
echo "</table>";
?>
</body>
</html>