<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];


$sql = "SELECT * FROM playlists WHERE usuario_id = $usuario_id";
$result = $conexion->query($sql);

$datos = [];

while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

echo json_encode($datos);
?>