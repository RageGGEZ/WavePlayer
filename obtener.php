<?php
header('Content-Type: application/json');
include("conexion.php");

$sql = "SELECT * FROM canciones ORDER BY id DESC";
$result = $conexion->query($sql);

$canciones = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $canciones[] = $row;
    }
}

echo json_encode($canciones);
?>