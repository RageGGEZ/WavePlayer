<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$id_playlist = $_POST['id_playlist'];
$id_cancion = $_POST['id_cancion'];


$verificar = "SELECT id FROM playlists WHERE id = $id_playlist AND usuario_id = $usuario_id";
$result = $conexion->query($verificar);

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'No tienes permiso para modificar esta playlist']);
    exit();
}

// Verificar si ya existe la canción en la playlist
$check = "SELECT id FROM playlist_canciones WHERE id_playlist = $id_playlist AND id_cancion = $id_cancion";
$existe = $conexion->query($check);

if ($existe->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'La canción ya está en la playlist']);
    exit();
}

// Agregar canción a la playlist
$sql = "INSERT INTO playlist_canciones (id_playlist, id_cancion) VALUES ($id_playlist, $id_cancion)";

if ($conexion->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conexion->error]);
}
?>