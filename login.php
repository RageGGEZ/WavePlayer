<?php
session_start();
include("conexion.php");

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $user = $resultado->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['usuario_id'] = $user['id'];  
        $_SESSION['usuario'] = $usuario;
        header("Location: index.html");
        exit();
    }
}   

header("Location: Inicio Sesion.html");
?>