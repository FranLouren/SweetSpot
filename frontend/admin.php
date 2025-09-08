<?php
// 🔹 Verifica que el usuario sea admin antes de mostrar el panel
require_once __DIR__ . '/../backend/admin_check.php';

// 🔹 Obtener todos los usuarios con sus roles para la tabla y el selector del formulario
$stmt = $conn->prepare("SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Obtener todas las reservas para mostrar en la tabla
$stmt = $conn->prepare("
    SELECT r.id, r.fecha, r.hora_inicio, ADDTIME(r.hora_inicio, '01:30:00') AS hora_fin,
           u.nombre AS usuario, p.nombre AS pista
    FROM reservas r
    JOIN usuarios u ON r.usuario_id = u.id
    JOIN pistas p ON r.pista_id = p.id
    ORDER BY r.fecha, r.hora_inicio
");
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Obtener usuarios y pistas para el formulario de añadir reservas
$stmt = $conn->prepare("SELECT id, nombre FROM usuarios ORDER BY nombre ASC");
$stmt->execute();
$listaUsuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, nombre FROM pistas ORDER BY nombre ASC");
$stmt->execute();
$listaPistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
</head>
<body>
    <h1>Panel de Administración</h1>

    <!-- 🔹 Botón para cerrar sesión -->
    <form action="logout.php" method="POST" style="margin-bottom:16px;">
        <button type="submit">Cerrar sesión</button>
    </form>

    <!-- 🔹 Mensajes de éxito/error al añadir reservas -->
    <?php if (isset($_GET['success'])): ?>
        <p style="color:green;">Reserva añadida correctamente ✅</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p style="color:red;">
            <?php 
            if ($_GET['error'] === 'ocupada') echo "La pista ya está reservada en ese horario.";
            elseif ($_GET['error'] === 'datos') echo "Faltan datos en el formulario.";
            elseif ($_GET['error'] === 'pasado') echo "No se puede añadir una reserva en el pasado.";
            else echo "Error al añadir la reserva.";
            ?>
        </p>
    <?php endif; ?>

    <!-- 🔹 Formulario para añadir reservas a usuarios -->
    <h2>Añadir reserva a un usuario</h2>
    <form method="POST" action="../backend/admin_add_reserva.php">
        <label>Usuario:<br>
            <select name="usuario_id" required>
                <option value="">-- Selecciona usuario --</option>
                <?php foreach ($listaUsuarios as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Pista:<br>
            <select name="pista_id" required>
                <option value="">-- Selecciona pista --</option>
                <?php foreach ($listaPistas as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Fecha:<br>
            <!-- 🔹 No permitir fechas pasadas -->
            <input type="date" name="fecha" required min="<?= date('Y-m-d') ?>">
        </label><br><br>

        <label>Hora inicio:<br>
            <input type="time" name="hora" required>
        </label><br><br>

        <button type="submit">Añadir reserva</button>
    </form>

    <!-- 🔹 Tabla de usuarios -->
    <h2>Usuarios</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['rol'] ?></td>
            <td>
                <a href="../backend/admin_delete_user.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este usuario y todas sus reservas?');">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- 🔹 Tabla de reservas -->
    <h2>Reservas</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Usuario</th><th>Pista</th><th>Fecha</th><th>Inicio</th><th>Fin</th><th>Acciones</th>
        </tr>
        <?php foreach ($reservas as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['usuario']) ?></td>
            <td><?= htmlspecialchars($r['pista']) ?></td>
            <td><?= $r['fecha'] ?></td>
            <td><?= $r['hora_inicio'] ?></td>
            <td><?= $r['hora_fin'] ?></td>
            <td>
                <a href="../backend/admin_delete_reserva.php?id=<?= $r['id'] ?>" onclick="return confirm('¿Seguro que quieres cancelar esta reserva?');">Cancelar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- 🔹 Enlace rápido al portal principal -->
    <p><a href="index.php">Volver al inicio</a></p>
    
</body>
</html>
