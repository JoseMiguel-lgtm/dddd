<?php
require_once "conexion.php";

// Consultar medicamentos y sus horarios
$sql = "SELECT
            m.id,
            m.nombre,
            m.dosis,
            m.fecha_inicio,
            m.fecha_fin,
            h.hora,
            h.frecuencia
        FROM medicamentos m
        LEFT JOIN horarios h
        ON m.id = h.medicamento_id
        WHERE m.activo = 1
        ORDER BY h.hora ASC";

$resultadoMedicamentos = $conexion->query($sql);

$totalMedicamentos = $resultadoMedicamentos->num_rows;

// Consultar las tomas programadas para hoy
date_default_timezone_set("America/Mexico_City");

$fecha_hoy = date("Y-m-d");

$sqlTomas = "SELECT
                t.id AS toma_id,
                m.nombre,
                m.dosis,
                t.hora_programada,
                t.estado
            FROM tomas t
            INNER JOIN medicamentos m
            ON t.medicamento_id = m.id
            WHERE t.fecha_toma = ?
            ORDER BY t.hora_programada ASC";

$consultaTomas = $conexion->prepare($sqlTomas);
$consultaTomas->bind_param("s", $fecha_hoy);
$consultaTomas->execute();

$resultadoTomas = $consultaTomas->get_result();
// Contar las tomas programadas para hoy
$totalTomas = $resultadoTomas->num_rows;

// Contar las tomas pendientes
$sqlPendientes = "SELECT COUNT(*) AS total
                  FROM tomas
                  WHERE fecha_toma = ?
                  AND estado = 'pendiente'";

$consultaPendientes = $conexion->prepare($sqlPendientes);
$consultaPendientes->bind_param("s", $fecha_hoy);
$consultaPendientes->execute();

$resultadoPendientes = $consultaPendientes->get_result();
$filaPendientes = $resultadoPendientes->fetch_assoc();

$totalPendientes = $filaPendientes['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma tu medicamento a tiempo</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>

    <div class="contenedor">

        <!-- Menú lateral -->
        <aside class="sidebar">
            <h2>💊 MedTime</h2>
            <p class="subtitulo">Tu salud, organizada</p>

            <nav>
                <a href="#" class="activo">🏠 Inicio</a>
                <a href="#">💊 Mis medicamentos</a>
                <a href="#">⏰ Horarios</a>
                <a href="#">📋 Historial</a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="contenido">

            <header class="encabezado">
                <div>
                    <h1>TOMA TU MEDICAMENTO A TIEMPO</h1>
                    <p>Organiza tus tratamientos y no olvides tus tomas.</p>
                </div>
            </header>

            <!-- Resumen -->
            <section class="resumen">

                <div class="tarjeta">
                    <h3>Medicamentos registrados</h3>
                   <p class="numero"><?php echo $totalMedicamentos; ?></p>  
                </div>

                <div class="tarjeta">
                    <h3>Tomas de hoy</h3>
                    <p class="numero"><?php echo $totalTomas; ?></p>
                </div>

                <div class="tarjeta">
                    <h3>Pendientes</h3>
                    <p class="numero"><?php echo $totalPendientes; ?></p>
                </div>

            </section>

            
<section class="seccion">
    <h2>Mis medicamentos registrados</h2>

    <div class="lista-medicamentos">

        <?php if ($resultadoMedicamentos->num_rows > 0): ?>

            <?php while ($med = $resultadoMedicamentos->fetch_assoc()): ?>

                <div class="medicamento-card">

                    <div class="medicamento-icono">💊</div>

                    <div class="medicamento-info">

                        <h3>
                            <?php echo htmlspecialchars($med['nombre']); ?>
                        </h3>

                        <p>
                            <strong>Dosis:</strong>
                            <?php echo htmlspecialchars($med['dosis']); ?>
                        </p>

                        <p>
                            <strong>Frecuencia:</strong>
                            <?php echo htmlspecialchars($med['frecuencia'] ?? 'No registrada'); ?>
                        </p>

                        <p>
                            <strong>Hora:</strong>
                            <?php echo date("h:i A", strtotime($med['hora'] ?? '00:00:00')); ?>
                        </p>

                        <p>
                            <strong>Inicio:</strong>
                            <?php echo htmlspecialchars($med['fecha_inicio']); ?>
                        </p>

                        <p>
                            <strong>Finalización:</strong>
                            <?php echo $med['fecha_fin']
                                ? htmlspecialchars($med['fecha_fin'])
                                : 'Sin fecha definida'; ?>
                        </p>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="mensaje-vacio">
                <span>💊</span>
                <p>Aún no tienes medicamentos registrados.</p>
                <p>Agrega tu primer medicamento para comenzar.</p>
            </div>

        <?php endif; ?>

    </div>
</section>

<section class="seccion">
    <h2>⏰ Tomas de hoy</h2>

    <div class="lista-medicamentos">

        <?php if ($resultadoTomas->num_rows > 0): ?>

            <?php while ($toma = $resultadoTomas->fetch_assoc()): ?>

                <div class="medicamento-card">

                    <div class="medicamento-icono">💊</div>

                    <div class="medicamento-info">

                        <h3>
                            <?php echo htmlspecialchars($toma['nombre']); ?>
                        </h3>

                        <p>
                            <strong>Dosis:</strong>
                            <?php echo htmlspecialchars($toma['dosis']); ?>
                        </p>

                        <p>
                            <strong>Hora:</strong>
                            <?php echo date("h:i A", strtotime($toma['hora_programada'])); ?>
                        </p>

                        <p>
                            <strong>Estado:</strong>
                            <?php echo htmlspecialchars($toma['estado']); ?>
                        </p>

                        <?php if ($toma['estado'] === 'pendiente'): ?>

                            <form method="POST" action="registrar_toma.php">

                                <input
                                    type="hidden"
                                    name="id_toma"
                                    value="<?php echo (int)$toma['toma_id']; ?>"
                                >

                                <button type="submit" class="btn-principal">
                                    ✓ Marcar como tomado
                                </button>

                            </form>

                        <?php else: ?>

                            <p>✓ Toma confirmada</p>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="mensaje-vacio">
                <span>⏰</span>
                <p>No tienes tomas programadas para hoy.</p>
            </div>

        <?php endif; ?>

    </div>
</section>

            <!-- Botón -->
            <button class="btn-agregar" type="button">
                + Agregar medicamento
            </button>

        </main>

    </div>

    <script src="script.js"></script>
<!-- Ventana emergente para agregar medicamento -->
<div id="modalMedicamento" class="modal">
    <div class="modal-contenido">

        <h2>Agregar medicamento</h2>
        <p>Registra los datos indicados por tu médico.</p>

      <form id="formMedicamento"
      method="POST"
      action="guardar_medicamento.php">

            <label>Nombre del medicamento</label>
            <input type="text" name="nombre" required>

            <label>Dosis indicada</label>
            <input type="text" name="dosis" placeholder="Ej. Según receta médica" required>

            <label>Fecha de inicio</label>
            <input type="date" name="fecha_inicio" required>

            <label>Fecha de finalización</label>
            <input type="date" name="fecha_fin">

            <label>Frecuencia</label>
            <input type="text" name="frecuencia" placeholder="Ej. Cada 8 horas" required>

            <label>Hora de la toma</label>
            <input type="time" name="hora" required>

            <div class="modal-botones">
                <button type="button" id="cerrarModal">Cancelar</button>
                <button type="submit">Guardar medicamento</button>
            </div>

        </form>
    </div>
</div>
</body>
</html>
