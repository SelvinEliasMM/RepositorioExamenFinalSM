<?php
require_once 'conexion.php';

if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    $stmtDel = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
    $stmtDel->execute([$idEliminar]);
    header("Location: reservas.php");
    exit();
}

$sql = "SELECT r.*, e.codigo, e.nombre AS equipo_nombre 
        FROM reservas r 
        INNER JOIN equipos e ON r.equipo_id = e.id 
        ORDER BY r.fecha DESC, r.hora_inicio ASC";
$reservas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Reservas</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

    <div class="barra-superior">
        <a href="index.php" class="marca">Reservas de Equipos</a>
        <a href="index.php">Equipos</a>
        <a href="reservas.php">Reservas</a>
        <a href="nueva_reserva.php">Nueva reserva</a>
    </div>

    <div class="contenedor">
        <div class="cabecera-titulo">
            <h2>Reservas registradas</h2>
            <a href="nueva_reserva.php" class="btn btn-oscuro">Nueva reserva</a>
        </div>

        <?php if (isset($_GET['exito'])): ?>
            <div class="alerta-exito">Reserva registrada correctamente.</div>
        <?php endif; ?>

        <table class="tabla">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Equipo</th>
                    <th>Solicitante</th>
                    <th>Carné</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Motivo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservas)): ?>
                    <tr>
                        <td colspan="8">No hay reservas registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['codigo'] . ' - ' . $r['equipo_nombre']) ?></td>
                            <td><?= htmlspecialchars($r['nombre']) ?></td>
                            <td><?= htmlspecialchars($r['carne']) ?></td>
                            <td><?= $r['fecha'] ?></td>
                            <td><?= substr($r['hora_inicio'], 0, 5) ?> a <?= substr($r['hora_fin'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($r['motivo']) ?></td>
                            <td>
                                <a href="reservas.php?eliminar=<?= $r['id'] ?>" class="btn btn-gris btn-pequeno" onclick="return confirm('¿Cancelar esta reserva?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>