<?php
// Inicia la sesión para poder usar $_SESSION
session_start();
require_once __DIR__ . '/lang/lang.php';

// Incluye la conexión a la base de datos 
require_once __DIR__ . '/../backend/config/db.php';

// Si ya hay un usuario logueado se redirige según su rol
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_rol'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

// Inicializamos variable para almacenar mensajes de error
$error = '';

// Comprobamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los datos enviados desde el formulario HTML
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            // Ahora también seleccionamos el rol
            $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['password'])) {
                // Guardamos en sesión ID y rol
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                // Redirigimos según el rol
                if ($usuario['rol'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = $lang['login_error'];
            }
        } catch (PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
        }
    } else {
        $error = $lang['login_error_fields'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SweetSpot</title>
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
                <a href="index.php"
                    class="d-flex align-items-center justify-content-center gap-3 mb-2 text-decoration-none">
                    <img src="img/logo.jpg" alt="Logo" class="logo-icon">
                    <h1 class="brand-title mb-0"><span class="text-sweet">Sweet</span><span
                            class="text-spot">Spot</span></h1>
                </a>
                <p class="brand-subtitle"><?= $lang['subtitle'] ?></p>
            </div>

            <!-- Card del formulario -->
            <div class="card card-custom">
                <div class="card-header text-center">
                    <h4 class="mb-0 text-white"><?= $lang['login_title'] ?></h4>
                </div>
                <div class="card-body">
                    <!-- 🔹 Mensaje si viene de register.php -->
                    <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
                        <div class="alert alert-success"><?= $lang['login_success'] ?></div>
                    <?php endif; ?>

                    <!-- Mostramos el mensaje de error si existe -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <!-- Formulario de login -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label"><?= $lang['login_email'] ?></label>
                            <input type="email" name="email" class="form-control"
                                placeholder="<?= $lang['login_email_placeholder'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= $lang['login_password'] ?></label>
                            <input type="password" name="password" class="form-control"
                                placeholder="<?= $lang['login_password_placeholder'] ?>" required>
                        </div>
                        <button type="submit" class="btn btn-orange w-100"><?= $lang['login_button'] ?></button>
                    </form>

                    <!-- Enlace para registrarse -->
                    <div class="text-center mt-4">
                        <span class="text-muted"><?= $lang['login_no_account'] ?></span>
                        <a href="register.php"><?= $lang['login_register_link'] ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>