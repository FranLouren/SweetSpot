<?php
session_start();
require_once __DIR__ . '/lang/lang.php';
require_once __DIR__ . '/../backend/config/db.php';

// Si ya está logueado, redirigir
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $error = $lang['recover_error_empty'];
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // En producción real aquí se enviaría un email con token.
                // En esta demo redirigimos directamente al formulario de nueva contraseña.
                $_SESSION['recover_user_id'] = $usuario['id'];
                header("Location: nueva_password.php");
                exit;
            } else {
                $error = $lang['recover_error_notfound'];
            }
        } catch (PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'es' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['recover_title'] ?> - SweetSpot</title>
    <link rel="icon" type="image/jpeg" href="img/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css?v=2" rel="stylesheet">
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
                    <h1 class="brand-title mb-0"><span class="text-sweet">Sweet</span><span class="text-spot">Spot</span></h1>
                </a>
                <p class="brand-subtitle"><?= $lang['subtitle'] ?></p>
            </div>

            <!-- Card del formulario -->
            <div class="card card-custom">
                <div class="card-header text-center">
                    <h4 class="mb-0 text-white"><?= $lang['recover_title'] ?></h4>
                </div>
                <div class="card-body">
                    <p class="text-muted small text-center mb-4"><?= $lang['recover_subtitle'] ?></p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label"><?= $lang['login_email'] ?></label>
                            <input type="email" name="email" class="form-control"
                                placeholder="<?= $lang['login_email_placeholder'] ?>" required>
                        </div>
                        <button type="submit" class="btn btn-orange w-100"><?= $lang['recover_button'] ?></button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="login.php" class="small"><?= $lang['recover_back_login'] ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
