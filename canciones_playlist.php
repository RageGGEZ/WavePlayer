<?php
include("conexion.php");

$id = $_GET['id'];

$sql = "SELECT c.* 
        FROM canciones c
        INNER JOIN playlist_canciones pc ON c.id = pc.id_cancion
        WHERE pc.id_playlist = $id";

$result = $conexion->query($sql);

$datos = [];

while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

echo json_encode($datos);
?>