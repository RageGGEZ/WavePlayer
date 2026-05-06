<?php
header('Content-Type: application/json');
require_once('conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$id_playlist = $_POST['id_playlist'] ?? 0;
$id_cancion = $_POST['id_cancion'] ?? 0;

if (!$id_playlist || !$id_cancion) {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

$check = "SELECT * FROM playlist_canciones WHERE id_playlist = $id_playlist AND id_cancion = $id_cancion";
$result = $conexion->query($check);

if ($result->num_rows > 0) {
    echo json_encode(['error' => 'La canción ya está en la playlist']);
    exit;
}

$sql = "INSERT INTO playlist_canciones (id_playlist, id_cancion) VALUES ($id_playlist, $id_cancion)";

if ($conexion->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => $conexion->error]);
}
?>