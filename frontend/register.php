<?php
session_start();
require_once __DIR__ . '/lang/lang.php';
require_once __DIR__ . '/../backend/config/db.php';

$error = '';

// Si ya está logueado → index
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nombre = '';
$email = '';

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$nombre || !$email || !$password || !$confirm_password) {
        $error = $lang['register_error_fields'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $lang['register_error_email'];
    } elseif (strlen($password) < 6) {
        $error = $lang['register_error_password'];
    } elseif ($password !== $confirm_password) {
        $error = $lang['register_error_password_match'];
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $error = $lang['register_error_exists'];
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    INSERT INTO usuarios (nombre, email, password, rol) 
                    VALUES (:nombre, :email, :password, 'usuario')
                ");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':password' => $hash
                ]);

                // Redirigir al login con mensaje
                header("Location: login.php?registered=1");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SweetSpot</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="img/logo.jpg">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Personalizado -->
    <link href="css/custom.css?v=2" rel="stylesheet">
    <!-- Flag Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
</head>

<body>
    <!-- Selector de idioma -->
    <div class="lang-switcher">
        <a href="?lang=es" class="lang-btn <?= $_SESSION['lang'] === 'es' ? 'active' : '' ?>" title="Español"><span class="fi fi-es"></span></a>
        <a href="?lang=en" class="lang-btn <?= $_SESSION['lang'] === 'en' ? 'active' : '' ?>" title="English"><span class="fi fi-gb"></span></a>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo y título -->
            <div class="text-center mb-4">
                <a href="index.php" class="d-flex align-items-center justify-content-center gap-3 mb-2 text-decoration-none">
                    <img src="img/logo.jpg" alt="Logo" class="logo-icon">
                    <h1 class="brand-title mb-0">
                        <span class="text-sweet">Sweet</span><span class="text-spot">Spot</span>
                    </h1>
                </a>
                <p class="brand-subtitle"><?= $lang['subtitle'] ?></p>
            </div>

            <!-- Card del formulario -->
            <div class="card card-custom">
                <div class="card-header text-center">
                    <h4 class="mb-0 text-white"><?= $lang['register_title'] ?></h4>
                </div>
                <div class="card-body">
                    <!-- Mensaje de error -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <!-- Formulario de registro -->
                    <form method="POST" action="" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label"><?= $lang['register_name'] ?></label>
                            <input type="text" name="nombre" class="form-control" placeholder="<?= $lang['register_name_placeholder'] ?>" required
                                value="<?php echo htmlspecialchars($nombre); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= $lang['register_email'] ?></label>
                            <input type="email" name="email" class="form-control" placeholder="<?= $lang['register_email_placeholder'] ?>" required
                                value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= $lang['register_password'] ?></label>
                            <input type="password" name="password" class="form-control"
                                placeholder="<?= $lang['register_password_placeholder'] ?>" required autocomplete="new-password">
                        </div>
                        <div class="mb-4">
                            <label class="form-label"><?= $lang['register_confirm_password'] ?></label>
                            <input type="password" name="confirm_password" class="form-control"
                                placeholder="<?= $lang['register_confirm_password_placeholder'] ?>" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-orange w-100"><?= $lang['register_button'] ?></button>
                    </form>

                    <!-- Enlace a login -->
                    <div class="text-center mt-4">
                        <span class="text-muted"><?= $lang['register_have_account'] ?></span>
                        <a href="login.php"><?= $lang['register_login_link'] ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>