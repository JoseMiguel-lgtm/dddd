
<?php

require_once "conexion.php";

// Recibir los datos del formulario
$nombre = trim($_POST["nombre"] ?? "");
$dosis = trim($_POST["dosis"] ?? "");
$fecha_inicio = $_POST["fecha_inicio"] ?? "";
$fecha_fin = $_POST["fecha_fin"] ?? null;
$frecuencia = trim($_POST["frecuencia"] ?? "");
$hora = $_POST["hora"] ?? "";

// Validar los campos obligatorios
if (
    $nombre === "" ||
    $dosis === "" ||
    $fecha_inicio === "" ||
    $frecuencia === "" ||
    $hora === ""
) {
    die("Error: completa todos los campos obligatorios.");
}

// Si no hay fecha final, guardar NULL
if ($fecha_fin === "") {
    $fecha_fin = null;
}

try {

    // Iniciar transacción
    $conexion->begin_transaction();

    // Guardar medicamento
    $sql = "INSERT INTO medicamentos
            (nombre, dosis, fecha_inicio, fecha_fin)
            VALUES (?, ?, ?, ?)";

    $consulta = $conexion->prepare($sql);

    $consulta->bind_param(
        "ssss",
        $nombre,
        $dosis,
        $fecha_inicio,
        $fecha_fin
    );

    $consulta->execute();

    // Obtener el ID del medicamento registrado
    $medicamento_id = $conexion->insert_id;

    $consulta->close();

    // Guardar su horario
    $sql = "INSERT INTO horarios
            (medicamento_id, hora, frecuencia)
            VALUES (?, ?, ?)";

    $consulta = $conexion->prepare($sql);

    $consulta->bind_param(
        "iss",
        $medicamento_id,
        $hora,
        $frecuencia
    );

    $consulta->execute();
    $consulta->close();

    // Confirmar los registros
    $conexion->commit();

    header("Location: index.php?guardado=1");
    exit;

} catch (Throwable $error) {

    // Deshacer cambios si ocurre un error
    $conexion->rollback();

    die("No se pudo guardar el medicamento. Error: "
        . htmlspecialchars($error->getMessage()));
}

?>
