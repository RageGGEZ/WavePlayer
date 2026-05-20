<?php
session_start(); 
header('Content-Type: application/json');
require_once('conexion.php');


if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');

if (empty($nombre)) {
    echo json_encode(['error' => 'El nombre es obligatorio']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre_escapado = mysqli_real_escape_string($conexion, $nombre);


$sql = "INSERT INTO playlists (nombre, usuario_id) VALUES ('$nombre_escapado', $usuario_id)";

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