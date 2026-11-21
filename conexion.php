<?php
$conexion = new mysqli("localhost", "root", "", "toma_tu_medicamento", 33065);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>