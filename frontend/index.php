<?php
session_start();
require_once __DIR__ . '/lang/lang.php';
require_once __DIR__ . '/../backend/config/db.php';

// Variables para obtener el nombre del usuario y su rol
$logged_in = isset($_SESSION['usuario_id']);
$nombre_usuario = '';

$es_admin = false;

if ($logged_in) {
    try {
        $stmt = $conn->prepare("SELECT nombre, rol FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['usuario_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $nombre_usuario = $user['nombre'];
            if (isset($user['rol']) && $user['rol'] === 'admin') {
                $es_admin = true;
            }
        }
    } catch (PDOException $e) {
        // Error silencioso
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">

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

                <!-- Menú de acciones (Derecha) -->
                <div class="d-flex align-items-center ms-auto gap-3">
                    <a href="reservas.php"
                        class="<?= $logged_in ? 'btn btn-orange btn-sm' : 'nav-link fw-medium' ?>"><?= $lang['nav_reservations'] ?></a>

                    <?php if ($logged_in): ?>
                        <?php if ($es_admin): ?>
                            <a href="admin.php" class="nav-link fw-medium"><?= $lang['nav_admin_panel'] ?? 'Panel Admin' ?></a>
                        <?php endif; ?>
                        <form action="logout.php" method="POST" class="m-0">
                            <button type="submit" class="btn-logout"><?= $lang['nav_logout'] ?></button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="nav-link fw-medium"><?= $lang['nav_login'] ?></a>
                        <a href="register.php" class="btn btn-orange btn-sm"><?= $lang['nav_register'] ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="container py-5">
        <div class="text-center mb-5">
            <?php if ($logged_in && $nombre_usuario): ?>
                <h1 class="mb-3"><?= $lang['index_hello'] ?>     <?php echo htmlspecialchars($nombre_usuario); ?>!</h1>
                <p class="lead text-muted"><?= $lang['index_welcome_back'] ?></p>
            <?php else: ?>
                <h1 class="mb-3"><?= $lang['index_welcome'] ?></h1>
                <p class="lead text-muted"><?= $lang['index_subtitle'] ?></p>
            <?php endif; ?>

        </div>

        <!-- Imagen de las pistas -->
        <div class="text-center">
            <img src="img/pistas.jpg" alt="<?= $lang['index_img_alt'] ?>" class="img-fluid rounded shadow-lg"
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
                    <p class="text-muted"><?= $lang['index_about'] ?></p>
                </div>

                <!-- Contacto -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-orange mb-3"><?= $lang['index_contact'] ?></h5>
                    <p class="text-muted mb-1">📍 Musterstraße 123, 30159 Hannover</p>
                    <p class="text-muted mb-1">📞 +49 511 123 4567</p>
                    <p class="text-muted mb-1">✉️ info@sweetspot-hannover.de</p>
                </div>

                <!-- Horarios -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-orange mb-3"><?= $lang['index_schedule'] ?></h5>
                    <p class="text-muted mb-1"><?= $lang['index_weekdays'] ?></p>
                    <p class="text-muted mb-1"><?= $lang['index_saturday'] ?></p>
                    <p class="text-muted mb-1"><?= $lang['index_sunday'] ?></p>
                </div>
            </div>

            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

            <!-- Copyright -->
            <div class="text-center text-muted">
                <p class="mb-0"><?= $lang['index_copyright'] ?></p>
            </div>
        </div>
    </footer>

</body>

</html>