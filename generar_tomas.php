
<?php

require_once "conexion.php";

date_default_timezone_set("America/Mexico_City");

$fecha_actual = date("Y-m-d");

// Consultar medicamentos activos y sus horarios
$sql = "SELECT
            m.id AS medicamento_id,
            m.fecha_inicio,
            m.fecha_fin,
            h.id AS horario_id,
            h.hora
        FROM medicamentos m
        INNER JOIN horarios h
        ON m.id = h.medicamento_id
        WHERE m.activo = 1";

$resultado = $conexion->query($sql);

$generadas = 0;

while ($fila = $resultado->fetch_assoc()) {

    // Comprobar si el tratamiento está vigente
    if ($fecha_actual < $fila['fecha_inicio']) {
        continue;
    }

    if (
        !empty($fila['fecha_fin']) &&
        $fecha_actual > $fila['fecha_fin']
    ) {
        continue;
    }

    // Registrar la toma del día
    $sql_insertar = "INSERT IGNORE INTO tomas
        (medicamento_id, horario_id, fecha_toma,
         hora_programada, estado)
        VALUES (?, ?, ?, ?, 'pendiente')";

    $consulta = $conexion->prepare($sql_insertar);

    $consulta->bind_param(
        "iiss",
        $fila['medicamento_id'],
        $fila['horario_id'],
        $fecha_actual,
        $fila['hora']
    );

    $consulta->execute();

    if ($consulta->affected_rows > 0) {
        $generadas++;
    }

    $consulta->close();
}

echo "Proceso terminado correctamente.";
echo "<br>Tomas nuevas generadas: " . $generadas;

?>
