<?php
require_once 'conexion.php';

$buscar = trim($_GET['buscar'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');

$sql = "SELECT * FROM equipos WHERE 1=1";
$parametros = [];

if ($buscar !== '') {
    $sql .= " AND (nombre LIKE ? OR codigo LIKE ?)";
    $parametros[] = "%$buscar%";
    $parametros[] = "%$buscar%";
}

if ($tipo !== '') {
    $sql .= " AND tipo = ?";
    $parametros[] = $tipo;
}

$sql .= " ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de equipos</title>
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
            <h2>Listado de equipos</h2>
            <a href="nueva_reserva.php" class="btn btn-oscuro">Nueva reserva</a>
        </div>

        <form method="GET" action="index.php" class="filtros">
            <input type="text" name="buscar" placeholder="Buscar nombre..." value="<?= htmlspecialchars($buscar) ?>">
            <select name="tipo">
                <option value="">Todos los tipos</option>
                <option value="Laptop" <?= $tipo === 'Laptop' ? 'selected' : '' ?>>Laptop</option>
                <option value="Proyector" <?= $tipo === 'Proyector' ? 'selected' : '' ?>>Proyector</option>
                <option value="Tablet" <?= $tipo === 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                <option value="Cámara" <?= $tipo === 'Cámara' ? 'selected' : '' ?>>Cámara</option>
                <option value="Laboratorio" <?= $tipo === 'Laboratorio' ? 'selected' : '' ?>>Laboratorio</option>
            </select>
            <button type="submit" class="btn btn-borde">Filtrar</button>
        </form>

        <table class="tabla">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $eq): ?>
                    <tr>
                        <td><?= $eq['id'] ?></td>
                        <td><?= htmlspecialchars($eq['codigo']) ?></td>
                        <td><?= htmlspecialchars($eq['nombre']) ?></td>
                        <td><?= htmlspecialchars($eq['tipo']) ?></td>
                        <td><?= htmlspecialchars($eq['ubicacion']) ?></td>
                        <td>
                            <?php if ($eq['estado'] === 'Disponible'): ?>
                                <span class="badge badge-disponible">Disponible</span>
                            <?php else: ?>
                                <span class="badge badge-mantenimiento">Mantenimiento</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($eq['estado'] === 'Disponible'): ?>
                                <a href="nueva_reserva.php?equipo_id=<?= $eq['id'] ?>" class="btn btn-oscuro btn-pequeno">Reservar</a>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>