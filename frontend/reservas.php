<?php
// Cargar middleware que gestiona sesión, inactividad e idiomas centralizados
require_once __DIR__ . '/../backend/middleware/Auth_middleware.php';
require_auth_web();
require_once __DIR__ . '/../backend/config/db.php';

// Denegación por defecto
$es_admin = false;
try {
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && isset($user['rol']) && $user['rol'] === 'admin') {
        $es_admin = true;
    }
} catch (PDOException $e) {
    // Error silencioso
}
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - SweetSpot</title>
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
        <!-- Navbar con navegación -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="img/logo.jpg" alt="Logo" class="logo-icon">
                    <span class="brand-text">
                        <span class="text-sweet">Sweet</span><span class="text-spot">Spot</span>
                    </span>
                </a>

                <!-- Menú de navegación a la derecha -->
                <ul class="navbar-nav ms-auto mb-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="reservas.php"><?= $lang['nav_reservations'] ?></a>
                    </li>
                </ul>

                <div class="d-flex ms-3 gap-2">
                    <?php if ($es_admin): ?>
                        <a href="admin.php"
                            class="btn btn-orange btn-sm"><?= $lang['nav_admin_panel'] ?? 'Panel Admin' ?></a>
                    <?php endif; ?>
                    <form action="logout.php" method="POST" class="m-0 align-content-center">
                        <button type="submit" class="btn-logout"><?= $lang['nav_logout'] ?></button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenedor principal -->
    <div class="container py-4">
        <!-- Card para hacer reserva -->
        <div class="card card-custom mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white"><?= $lang['res_new'] ?></h5>
            </div>
            <div class="card-body">
                <form id="reservaForm" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><?= $lang['res_date'] ?></label>
                        <input type="date" id="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?= $lang['res_court'] ?></label>
                        <select id="pista" class="form-select" required>
                            <option value="" disabled selected><?= $lang['res_select'] ?></option>
                            <option value="1"><?= $lang['court_prefix'] ?> 1</option>
                            <option value="2"><?= $lang['court_prefix'] ?> 2</option>
                            <option value="3"><?= $lang['court_prefix'] ?> 3</option>
                            <option value="4"><?= $lang['court_prefix'] ?> 4</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?= $lang['res_start_time'] ?></label>
                        <select id="hora_inicio" class="form-select" required disabled>
                            <option value="" disabled selected><?= $lang['res_select_first'] ?? 'Elige fecha y pista' ?>
                            </option>
                            <option value="07:00">07:00 - 08:30</option>
                            <option value="08:30">08:30 - 10:00</option>
                            <option value="10:00">10:00 - 11:30</option>
                            <option value="11:30">11:30 - 13:00</option>
                            <option value="13:00">13:00 - 14:30</option>
                            <option value="14:30">14:30 - 16:00</option>
                            <option value="16:00">16:00 - 17:30</option>
                            <option value="17:30">17:30 - 19:00</option>
                            <option value="19:00">19:00 - 20:30</option>
                            <option value="20:30">20:30 - 22:00</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-orange w-100"><?= $lang['res_button'] ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card con tabla de reservas -->
        <div class="card card-custom">
            <div class="card-header">
                <h5 class="mb-0 text-white"><?= $lang['res_active'] ?></h5>
            </div>
            <div class="card-body p-0">
                <table id="tablaReservas" class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?= $lang['res_date_col'] ?></th>
                            <th><?= $lang['res_start_col'] ?></th>
                            <th><?= $lang['res_end_col'] ?></th>
                            <th><?= $lang['res_court_col'] ?></th>
                            <th><?= $lang['res_actions'] ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Card con historial de reservas -->
        <div class="card card-custom mt-4">
            <div class="card-header">
                <h5 class="mb-0 text-white"><?= $lang['res_history'] ?></h5>
            </div>
            <div class="card-body p-0">
                <table id="tablaHistorial" class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?= $lang['res_date_col'] ?></th>
                            <th><?= $lang['res_start_col'] ?></th>
                            <th><?= $lang['res_end_col'] ?></th>
                            <th><?= $lang['res_court_col'] ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Gestión de cuenta -->
        <div class="mt-5 pt-4 text-center">
            <hr class="border-secondary mb-4 opacity-50">
            <p class="text-muted small mb-2"><?= $lang['res_account_settings'] ?></p>
            <a href="../backend/delete_me.php" onclick="return confirm('<?= $lang['res_confirm_delete_account'] ?>');"
                class="btn btn-sm btn-outline-danger" style="opacity: 0.8;">
                <?= $lang['res_delete_account'] ?>
            </a>
        </div>
    </div>

    <!-- Script principal que maneja las reservas -->
    <script>
        const langData = {
            noActive: "<?= $lang['res_no_active'] ?>",
            noHistory: "<?= $lang['res_no_history'] ?>",
            courtPrefix: "<?= $lang['court_prefix'] ?>",
            btnCancel: "<?= $lang['admin_cancel'] ?>",
            bookingSuccess: "<?= $lang['js_booking_success'] ?>",
            slotOccupied: "<?= $lang['js_slot_occupied'] ?>",
            confirmCancel: "<?= $lang['js_confirm_cancel'] ?>",
            bookingCancelled: "<?= $lang['js_booking_cancelled'] ?>"
        };
    </script>
    <script src="js/main.js"></script>
    <script>
        // Inyecta el ID del usuario de la sesión a JS
        const usuarioId = <?php echo (int) $_SESSION['usuario_id']; ?>;
        // Llama a la función para cargar las reservas del usuario logueado
        cargarReservas(usuarioId);
    </script>
</body>

</html>