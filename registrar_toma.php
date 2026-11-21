
<?php

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id_toma = filter_input(INPUT_POST, "id_toma", FILTER_VALIDATE_INT);

if (!$id_toma) {
    die("Identificador de toma no válido.");
}

$sql = "UPDATE tomas
        SET estado = 'tomado',
            fecha_confirmacion = NOW()
        WHERE id = ?
        AND estado = 'pendiente'
        AND fecha_toma = CURDATE()";

$consulta = $conexion->prepare($sql);
$consulta->bind_param("i", $id_toma);
$consulta->execute();

$consulta->close();

header("Location: index.php");
exit;

?>