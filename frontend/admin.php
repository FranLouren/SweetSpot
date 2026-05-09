<?php
// Importaciones de archivos
require_once __DIR__ . '/../backend/admin_check.php';
require_once __DIR__ . '/lang/lang.php';

// Obtener todos los usuarios con sus roles para la tabla y el selector del formulario
$stmt = $conn->prepare("SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las reservas para mostrar en la tabla
$stmt = $conn->prepare("
    SELECT r.id, r.fecha, r.hora_inicio, ADDTIME(r.hora_inicio, '01:30:00') AS hora_fin,
           u.nombre AS usuario, p.nombre AS pista
    FROM reservas r
    JOIN usuarios u ON r.usuario_id = u.id
    JOIN pistas p ON r.pista_id = p.id
    ORDER BY r.fecha DESC, r.hora_inicio DESC
");
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios para el formulario de añadir reservas
$stmt = $conn->prepare("SELECT id, nombre FROM usuarios ORDER BY nombre ASC");
$stmt->execute();
$listaUsuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener pistas para el formulario de añadir reservas
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
    <!-- Flag Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
</head>

<body>
    <!-- Selector de idioma -->
    <div class="lang-switcher">
        <a href="?lang=es" class="lang-btn <?= $_SESSION['lang'] === 'es' ? 'active' : '' ?>" title="Español"><span
                class="fi fi-es"></span></a>
        <a href="?lang=en" class="lang-btn <?= $_SESSION['lang'] === 'en' ? 'active' : '' ?>" title="English"><span
                class="fi fi-gb"></span></a>
    </div>

    <!-- Cabecera -->
    <header>
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
                <div class="d-flex align-items-center gap-3">
                    <a href="index.php" class="nav-link fw-medium"><?= $lang['nav_view_as_user'] ?></a>
                    <form action="logout.php" method="POST" class="m-0 align-content-center">
                        <button type="submit" class="btn-logout"><?= $lang['nav_logout'] ?></button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenedor principal -->
    <div class="container py-4">
        <!-- Mensajes de éxito/error -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= $lang['admin_res_success'] ?></div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                if ($_GET['error'] === 'ocupada')
                    echo $lang['admin_err_occupied'];
                elseif ($_GET['error'] === 'datos')
                    echo $lang['admin_err_data'];
                elseif ($_GET['error'] === 'pasado')
                    echo $lang['admin_err_past'];
                else
                    echo $lang['admin_err_general'];
                ?>
            </div>
        <?php endif; ?>

        <!-- Card: Añadir reserva -->
        <div class="card card-custom mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white"><?= $lang['admin_add_res'] ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="../backend/admin_add_reserva.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><?= $lang['admin_user'] ?></label>
                        <select name="usuario_id" class="form-select" required>
                            <option value=""><?= $lang['admin_select'] ?></option>
                            <?php foreach ($listaUsuarios as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><?= $lang['admin_court'] ?></label>
                        <select id="admin_pista" name="pista_id" class="form-select" required>
                            <option value=""><?= $lang['admin_select_court'] ?></option>
                            <?php foreach ($listaPistas as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?= $lang['admin_date'] ?></label>
                        <input type="date" id="admin_fecha" name="fecha" class="form-control" required
                            min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><?= $lang['admin_time'] ?></label>
                        <select id="admin_hora" name="hora" class="form-select" required disabled>
                            <option value=""><?= $lang['res_select_first'] ?? 'Elige fecha y pista antes' ?></option>
                            <option value="07:00">07:00</option>
                            <option value="08:30">08:30</option>
                            <option value="10:00">10:00</option>
                            <option value="11:30">11:30</option>
                            <option value="13:00">13:00</option>
                            <option value="14:30">14:30</option>
                            <option value="16:00">16:00</option>
                            <option value="17:30">17:30</option>
                            <option value="19:00">19:00</option>
                            <option value="20:30">20:30</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-orange w-100"><?= $lang['admin_add_btn'] ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card: Tabla de usuarios -->
        <div class="card card-custom mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white"><?= $lang['admin_users_title'] ?></h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?= $lang['admin_id'] ?></th>
                            <th><?= $lang['admin_name'] ?></th>
                            <th><?= $lang['admin_email'] ?></th>
                            <th><?= $lang['admin_role'] ?></th>
                            <th><?= $lang['admin_actions'] ?></th>
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
                                        onclick="return confirm('<?= $lang['admin_confirm_del_user'] ?>');"><?= $lang['admin_delete'] ?></a>
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
                <h5 class="mb-0 text-white"><?= $lang['admin_res_title'] ?></h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?= $lang['admin_id'] ?></th>
                            <th><?= $lang['admin_user'] ?></th>
                            <th><?= $lang['admin_court'] ?></th>
                            <th><?= $lang['admin_date'] ?></th>
                            <th><?= $lang['admin_start'] ?></th>
                            <th><?= $lang['admin_end'] ?></th>
                            <th><?= $lang['admin_actions'] ?></th>
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
                                    <?php
                                    $fecha_reserva = new DateTime($r['fecha'] . ' ' . $r['hora_inicio']);
                                    $ahora = new DateTime();
                                    if ($fecha_reserva > $ahora):
                                        ?>
                                        <a href="../backend/admin_delete_reserva.php?id=<?= $r['id'] ?>"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('<?= $lang['admin_confirm_del_res'] ?>');"><?= $lang['admin_cancel'] ?></a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= $lang['admin_completed'] ?? 'Completada' ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script para validación dinámica en el panel de administrador -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const campoFecha = document.getElementById('admin_fecha');
            const campoPista = document.getElementById('admin_pista');
            const campoHora = document.getElementById('admin_hora');

            function checkDisponibilidadAdmin() {
                const fechaVal = campoFecha.value;
                const pistaVal = campoPista.value;

                if (!fechaVal || !pistaVal) {
                    campoHora.disabled = true;
                    if (campoHora.options[0]) campoHora.options[0].selected = true;
                    return;
                }

                fetch(`http://localhost/SweetSpot/backend/get_horas_ocupadas.php?fecha=${fechaVal}&pista_id=${pistaVal}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'error') {
                            console.error(data.message);
                            return;
                        }

                        campoHora.disabled = false;
                        const ocupadas = data.ocupadas || [];

                        for (let i = 1; i < campoHora.options.length; i++) {
                            const opt = campoHora.options[i];
                            const horaOpt = opt.value;

                            opt.disabled = false;
                            opt.textContent = opt.textContent.replace(' (Ocupada)', '');

                            if (ocupadas.includes(horaOpt)) {
                                opt.disabled = true;
                                opt.textContent += ' (Ocupada)';
                            }
                        }

                        if (campoHora.selectedOptions.length > 0 && campoHora.selectedOptions[0].disabled) {
                            campoHora.options[0].selected = true;
                        }
                    })
                    .catch(err => console.error('Error fetching disponibilidad:', err));
            }

            campoFecha.addEventListener('change', checkDisponibilidadAdmin);
            campoPista.addEventListener('change', checkDisponibilidadAdmin);
        });
    </script>
</body>

</html>