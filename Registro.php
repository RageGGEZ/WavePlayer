<?php

include("conexion.php");

$usuario = $_POST['usuario'];
$correo = $_POST['correo'];
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

    ?>

    <!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Registro</title>
                <link rel="stylesheet" href="Existente.css">
            </head>
            <body>
                <h1>USUARIO YA EXISTENTE</h1>
                <button onclick="window.location.href='Registro.html'">
                Regresar
                </button>
            </body>    
        </html>

    <?php
    exit();

}

// insertar usuario
$sql = "INSERT INTO usuarios (usuario, correo, password) VALUES (?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("sss", $usuario, $correo, $passwordHash);

if ($stmt->execute()) {

    header("Location: ../index.php");

} else {

    echo "Error al registrar";

}

?>