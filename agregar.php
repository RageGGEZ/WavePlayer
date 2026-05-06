<?php
require_once('conexion.php');

$rawInput = file_get_contents('php://input');
$contentType = $_SERVER['CONTENT_TYPE'] ?? 'No definido';

// Si no hay datos POST pero hay rawInput, intentar parsear
if (empty($_POST) && !empty($rawInput) && strpos($contentType, 'multipart/form-data') !== false) {
    // Intentar extraer datos manualmente
    $boundary = substr($contentType, strpos($contentType, 'boundary=') + 9);
    if ($boundary) {
        $parts = explode('--' . $boundary, $rawInput);
        foreach ($parts as $part) {
            if (preg_match('/name="nombre".*?\r\n\r\n(.*?)\r\n/s', $part, $matches)) {
                $_POST['nombre'] = trim($matches[1]);
            }
            if (preg_match('/name="artista".*?\r\n\r\n(.*?)\r\n/s', $part, $matches)) {
                $_POST['artista'] = trim($matches[1]);
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso inválido");
}

$nombre = trim($_POST['nombre'] ?? '');
$artista = trim($_POST['artista'] ?? '');

if (empty($nombre) || empty($artista)) {
    echo "=== ERROR DE DIAGNÓSTICO ===<br>";
    echo "Método: " . $_SERVER['REQUEST_METHOD'] . "<br>";
    echo "Content-Type: $contentType<br>";
    echo "POST: ";
    var_dump($_POST);
    echo "<br>FILES: ";
    var_dump($_FILES);
    echo "<br>Raw input (primeros 200 chars): " . htmlspecialchars(substr($rawInput, 0, 200));
    echo "<br><br>SUGERENCIA: Verifica que 'sng' y 'img' tengan permisos de escritura";
    exit;
}


if (!isset($_FILES['cancion']) || $_FILES['cancion']['error'] !== UPLOAD_ERR_OK) {
    die("Error: No se recibió el archivo de audio o hubo un error en la subida");
}
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    die("Error: No se recibió la imagen o hubo un error en la subida");
}

if (!file_exists('sng')) {
    mkdir('sng', 0777, true);
}
if (!file_exists('img')) {
    mkdir('img', 0777, true);
}

if (!is_writable('sng') || !is_writable('img')) {
    die("Error: Los directorios 'sng' y 'img' deben tener permisos de escritura");
}

$nombreLimpio = preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ_-]/u', '_', $nombre);
$nombreLimpio = str_replace(' ', '_', $nombreLimpio);

$cancionExt = strtolower(pathinfo($_FILES['cancion']['name'], PATHINFO_EXTENSION));
$imagenExt = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

$audioPermitidos = ['mp3', 'wav', 'ogg', 'm4a'];
$imagenPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($cancionExt, $audioPermitidos)) {
    die("Error: Formato de audio no permitido. Use: " . implode(', ', $audioPermitidos));
}
if (!in_array($imagenExt, $imagenPermitidos)) {
    die("Error: Formato de imagen no permitido. Use: " . implode(', ', $imagenPermitidos));
}

$timestamp = time();
$cancionFilename = $nombreLimpio . '_' . $timestamp . '.' . $cancionExt;
$imagenFilename = $nombreLimpio . '_' . $timestamp . '.' . $imagenExt;

$cancionURL = 'sng/' . $cancionFilename;
$imgURL = 'img/' . $imagenFilename;

// Insertar en BD
$nombre_escapado = mysqli_real_escape_string($conexion, $nombre);
$artista_escapado = mysqli_real_escape_string($conexion, $artista);

$sql = "INSERT INTO canciones (nombre, artista, audio, portada) VALUES ('$nombre_escapado', '$artista_escapado', '$cancionURL', '$imgURL')";

if (mysqli_query($conexion, $sql)) {
    if (move_uploaded_file($_FILES['cancion']['tmp_name'], $cancionURL)) {
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imgURL)) {
            header('Location: index.html');
            exit;
        } else {
            echo "Error al mover la imagen";
        }
    } else {
        echo "Error al mover el audio";
    }
} else {
    echo "Error BD: " . mysqli_error($conexion);
}
?>