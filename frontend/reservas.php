<?php
// Inicia la sesión para acceder a $_SESSION
session_start();

//  Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Comprobamos si existe 'last_activity' y ha pasado más de 600 segundos desde la última acción
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
    session_unset();
    session_destroy();
    // Redirige al login indicando timeout
    header("Location: ../frontend/login.php?timeout=1");
    exit;
}

// Actualizar última actividad a la hora actual
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="es">

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
</head>

<body>
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
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="reservas.php">Reservas</a>
                </li>
            </ul>

            <form action="logout.php" method="POST" class="ms-3">
                <button type="submit" class="btn btn-orange btn-sm">Cerrar sesión</button>
            </form>
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="container py-4">
        <!-- Card para hacer reserva -->
        <div class="card card-custom mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white">Nueva Reserva</h5>
            </div>
            <div class="card-body">
                <form id="reservaForm" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" id="fecha" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pista</label>
                        <select id="pista" class="form-select" required>
                            <option value="" disabled selected>Selecciona</option>
                            <option value="1">Pista 1</option>
                            <option value="2">Pista 2</option>
                            <option value="3">Pista 3</option>
                            <option value="4">Pista 4</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hora inicio</label>
                        <select id="hora_inicio" class="form-select" required>
                            <option value="" disabled selected>Selecciona</option>
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
                        <button type="submit" class="btn btn-orange w-100">Reservar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card con tabla de reservas -->
        <div class="card card-custom">
            <div class="card-header">
                <h5 class="mb-0 text-white">Reservas en activo</h5>
            </div>
            <div class="card-body p-0">
                <table id="tablaReservas" class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora inicio</th>
                            <th>Hora fin</th>
                            <th>Pista</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Card con historial de reservas -->
        <div class="card card-custom mt-4">
            <div class="card-header">
                <h5 class="mb-0 text-white">Historial de Reservas</h5>
            </div>
            <div class="card-body p-0">
                <table id="tablaHistorial" class="table table-dark table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora inicio</th>
                            <th>Hora fin</th>
                            <th>Pista</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script principal que maneja las reservas -->
    <script src="js/main.js"></script>
    <script>
        // Inyecta el ID del usuario de la sesión a JS
        const usuarioId = <?php echo (int) $_SESSION['usuario_id']; ?>;
        // Llama a la función para cargar las reservas del usuario logueado
        cargarReservas(usuarioId);
    </script>
</body>

</html>