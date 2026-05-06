<?php
include("conexion.php");

$sql = "SELECT * FROM playlists";
$result = $conexion->query($sql);

$datos = [];

while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

echo json_encode($datos);
?>