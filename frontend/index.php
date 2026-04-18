<?php
session_start();
require_once __DIR__ . '/../backend/config/db.php';

// index.php es pública - no requiere login
$logged_in = isset($_SESSION['usuario_id']);
$nombre_usuario = '';

// Si está logueado, obtener el nombre del usuario
if ($logged_in) {
    try {
        $stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['usuario_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $nombre_usuario = $user['nombre'];
        }
    } catch (PDOException $e) {
        // Error silencioso
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - SweetSpot</title>
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
                    <a class="nav-link active" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reservas.php">Reservas</a>
                </li>
            </ul>

            <?php if ($logged_in): ?>
                <form action="logout.php" method="POST" class="ms-3">
                    <button type="submit" class="btn btn-orange btn-sm">Cerrar sesión</button>
                </form>
            <?php else: ?>
                <div class="ms-3">
                    <a href="login.php" class="btn btn-orange btn-sm">Iniciar sesión</a>
                    <a href="register.php" class="btn btn-orange btn-sm ms-2">Registrarse</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="container py-5">
        <div class="text-center mb-5">
            <?php if ($logged_in && $nombre_usuario): ?>
                <h1 class="mb-3">¡Hola, <?php echo htmlspecialchars($nombre_usuario); ?>!</h1>
                <p class="lead text-muted">Bienvenido de nuevo a SweetSpot</p>
            <?php else: ?>
                <h1 class="mb-3">Bienvenido a SweetSpot</h1>
                <p class="lead text-muted">Tu plataforma de reservas de pistas de pádel</p>
            <?php endif; ?>

        </div>

        <!-- Imagen de las pistas -->
        <div class="text-center">
            <img src="img/pistas.jpg" alt="Nuestras pistas de pádel" class="img-fluid rounded shadow-lg"
                style="max-height: 500px;">
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <!-- Sobre nosotros -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-orange mb-3">SweetSpot</h5>
                    <p class="text-muted">El mejor centro de pádel indoor de Hannover. Instalaciones modernas y ambiente
                        premium para disfrutar de tu deporte favorito.</p>
                </div>

                <!-- Contacto -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-orange mb-3">Contacto</h5>
                    <p class="text-muted mb-1">📍 Musterstraße 123, 30159 Hannover</p>
                    <p class="text-muted mb-1">📞 +49 511 123 4567</p>
                    <p class="text-muted mb-1">✉️ info@sweetspot-hannover.de</p>
                </div>

                <!-- Horarios -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-orange mb-3">Horarios</h5>
                    <p class="text-muted mb-1">Lunes - Viernes: 07:00 - 23:00</p>
                    <p class="text-muted mb-1">Sábados: 08:00 - 22:00</p>
                    <p class="text-muted mb-1">Domingos: 09:00 - 21:00</p>
                </div>
            </div>

            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

            <!-- Copyright -->
            <div class="text-center text-muted">
                <p class="mb-0">&copy; 2026 SweetSpot Hannover. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

</body>

</html>