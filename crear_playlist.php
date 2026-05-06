<?php
header('Content-Type: application/json');
require_once('conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');

if (empty($nombre)) {
    echo json_encode(['error' => 'El nombre es obligatorio']);
    exit;
}

$nombre_escapado = mysqli_real_escape_string($conexion, $nombre);
$sql = "INSERT INTO playlists (nombre) VALUES ('$nombre_escapado')";

if ($conexion->query($sql)) {
    echo json_encode([
        'success' => true,
        'id' => $conexion->insert_id,
        'nombre' => $nombre
    ]);
} else {
    echo json_encode(['error' => $conexion->error]);
}
?>