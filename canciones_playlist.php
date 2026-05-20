<?php
session_start(); 
include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit();
}

$id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];


$verificar = "SELECT id FROM playlists WHERE id = $id AND usuario_id = $usuario_id";
$check = $conexion->query($verificar);

if ($check->num_rows == 0) {
    echo json_encode([]);
    exit();
}

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