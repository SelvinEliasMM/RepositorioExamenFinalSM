<?php
require_once 'conexion.php';

$error = '';
$equipoSeleccionado = $_GET['equipo_id'] ?? '';
$nombre = '';
$carne = '';
$fecha = '';
$hora_inicio = '';
$hora_fin = '';
$motivo = '';


$stmtEq = $pdo->query("SELECT * FROM equipos WHERE estado = 'Disponible' ORDER BY codigo ASC");
$equiposDisponibles = $stmtEq->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $carne = trim($_POST['carne'] ?? '');
    $equipoSeleccionado = intval($_POST['equipo_id'] ?? 0);
    $fecha = $_POST['fecha'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $motivo = trim($_POST['motivo'] ?? '');

    if ($hora_fin <= $hora_inicio) {
        $error = "Error: la hora de fin debe ser mayor que la hora de inicio.";
    } else {
        // verificacion de equipos
        $sqlTraslape = "SELECT * FROM reservas 
                        WHERE equipo_id = ? 
                        AND fecha = ? 
                        AND (? < hora_fin AND ? > hora_inicio)
                        LIMIT 1";
        $stmtCheck = $pdo->prepare($sqlTraslape);
        $stmtCheck->execute([$equipoSeleccionado, $fecha, $hora_inicio, $hora_fin]);
        $reservaExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($reservaExistente) {
            $hi = substr($reservaExistente['hora_inicio'], 0, 5);
            $hf = substr($reservaExistente['hora_fin'], 0, 5);
            $error = "Error: el equipo ya está reservado el {$reservaExistente['fecha']} de $hi a $hf.";
        } else {
            $sqlInsert = "INSERT INTO reservas (equipo_id, nombre, carne, fecha, hora_inicio, hora_fin, motivo) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtIns = $pdo->prepare($sqlInsert);
            $stmtIns->execute([$equipoSeleccionado, $nombre, $carne, $fecha, $hora_inicio, $hora_fin, $motivo]);

            header("Location: reservas.php?exito=1");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar reserva</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

    <div class="barra-superior">
        <a href="index.php" class="marca">Reservas de Equipos</a>
        <a href="index.php">Equipos</a>
        <a href="reservas.php">Reservas</a>
        <a href="nueva_reserva.php">Nueva reserva</a>
    </div>

    <div class="contenedor-formulario">
        <h3>Registrar reserva</h3>

        <?php if ($error !== ''): ?>
            <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="nueva_reserva.php">
            <div class="grupo-campo">
                <label>Nombre completo</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
            </div>

            <div class="grupo-campo">
                <label>Carné</label>
                <input type="text" name="carne" value="<?= htmlspecialchars($carne) ?>" placeholder="2023-0145" required>
                <div class="texto-ayuda">Ingrese un carnet que sea valido</div>
            </div>

            <div class="grupo-campo">
                <label>Equipo</label>
                <select name="equipo_id" required>
                    <option value="">-- Seleccione un equipo --</option>
                    <?php foreach ($equiposDisponibles as $eq): ?>
                        <option value="<?= $eq['id'] ?>" <?= ($equipoSeleccionado == $eq['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($eq['codigo'] . ' - ' . $eq['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="fila-tres">
                <div class="grupo-campo">
                    <label>Fecha</label>
                    <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Hora inicio</label>
                    <input type="time" name="hora_inicio" value="<?= htmlspecialchars($hora_inicio) ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Hora fin</label>
                    <input type="time" name="hora_fin" value="<?= htmlspecialchars($hora_fin) ?>" required>
                </div>
            </div>

            <div class="grupo-campo">
                <label>Motivo </label>
                <input type="text" name="motivo" value="<?= htmlspecialchars($motivo) ?>">
            </div>

            <button type="submit" class="btn btn-oscuro">Guardar</button>
            <a href="index.php" class="btn btn-gris">Cancelar</a>
        </form>
    </div>

</body>

</html>