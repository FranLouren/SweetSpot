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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - SweetSpot</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="img/logo.jpg">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Personalizado -->
    <link href="css/custom.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar Admin -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/logo.jpg" alt="Logo" class="logo-icon">
                <span class="brand-text">
                    <span class="text-sweet">Sweet</span><span class="text-spot">Spot</span>
                </span>
                <span class="badge bg-danger">Admin</span>
            </a>
            <div>
                <a href="index.php" class="btn btn-outline-info btn-sm me-2">Ver como usuario</a>
                <form action="logout.php" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="container py-4">
        <!-- Mensajes de éxito/error -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Reserva añadida correctamente</div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                if ($_GET['error'] === 'ocupada')
                    echo "La pista ya está reservada en ese horario.";
                elseif ($_GET['error'] === 'datos')
                    echo "Faltan datos en el formulario.";
                elseif ($_GET['error'] === 'pasado')
                    echo "No se puede añadir una reserva en el pasado.";
                else
                    echo "Error al añadir la reserva.";
                ?>
            </div>
        <?php endif; ?>

        <!-- Card: Añadir reserva -->
        <div class="card card-custom mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white">Añadir reserva a un usuario</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="../backend/admin_add_reserva.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Usuario</label>
                        <select name="usuario_id" class="form-select" required>
                            <option value="">-- Selecciona --</option>
                            <?php foreach ($listaUsuarios as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Pista</label>
                        <select name="pista_id" class="form-select" required>
                            <option value="">-- Pista --</option>
                            <?php foreach ($listaPistas as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hora</label>
                        <input type="time" name="hora" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Añadir</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card: Tabla de usuarios -->
        <div class="card card-custom mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white">Usuarios Registrados</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span
                                        class="badge <?= $u['rol'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>"><?= $u['rol'] ?></span>
                                </td>
                                <td>
                                    <a href="../backend/admin_delete_user.php?id=<?= $u['id'] ?>"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('¿Eliminar usuario y sus reservas?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card: Tabla de reservas -->
        <div class="card card-custom">
            <div class="card-header">
                <h5 class="mb-0 text-white">Todas las Reservas</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Pista</th>
                            <th>Fecha</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $r): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><?= htmlspecialchars($r['usuario']) ?></td>
                                <td><?= htmlspecialchars($r['pista']) ?></td>
                                <td><?= $r['fecha'] ?></td>
                                <td><?= $r['hora_inicio'] ?></td>
                                <td><?= $r['hora_fin'] ?></td>
                                <td>
                                    <a href="../backend/admin_delete_reserva.php?id=<?= $r['id'] ?>"
                                        class="btn btn-outline-warning btn-sm"
                                        onclick="return confirm('¿Cancelar esta reserva?');">Cancelar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>