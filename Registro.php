<?php
include("conexion.php");

$usuario = $_POST['usuario'];
$password = $_POST['password'];

// encriptar contraseña
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// verificar si ya existe
$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo "El usuario ya existe";
    exit();
}

// insertar usuario
$sql = "INSERT INTO usuarios (usuario, password) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $usuario, $passwordHash);

if ($stmt->execute()) {
    header("Location: Inicio Sesion.html");
} else {
    echo "Error al registrar";
}
?>