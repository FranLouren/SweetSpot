<?php
session_start();
require_once __DIR__ . '/lang/lang.php';
require_once __DIR__ . '/../backend/config/db.php';

// Si no viene del flujo de recuperación, redirigir
if (!isset($_SESSION['recover_user_id'])) {
    header("Location: recuperar.php");
    exit;
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva = $_POST['nueva_password'] ?? '';
    $confirmar = $_POST['confirmar_password'] ?? '';

    if (!$nueva || !$confirmar) {
        $error = $lang['register_error_fields'];
    } elseif ($nueva !== $confirmar) {
        $error = $lang['register_error_pass_match'];
    } elseif (strlen($nueva) < 6) {
        $error = $lang['register_error_pass_length'];
    } else {
        try {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
            $stmt->execute([
                ':password' => $hash,
                ':id' => $_SESSION['recover_user_id']
            ]);

            // Limpiar la sesión de recuperación
            unset($_SESSION['recover_user_id']);

            header("Location: login.php?recovered=1");
            exit;
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
    <title><?= $lang['newpass_title'] ?> - SweetSpot</title>
    <link rel="icon" type="image/jpeg" href="img/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css?v=2" rel="stylesheet">
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
                    <h4 class="mb-0 text-white"><?= $lang['newpass_title'] ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label"><?= $lang['newpass_label'] ?></label>
                            <input type="password" name="nueva_password" class="form-control"
                                placeholder="<?= $lang['newpass_placeholder'] ?>" required minlength="6">
                        </div>
                        <div class="mb-4">
                            <label class="form-label"><?= $lang['register_confirm_password'] ?></label>
                            <input type="password" name="confirmar_password" class="form-control"
                                placeholder="<?= $lang['register_confirm_placeholder'] ?>" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-orange w-100"><?= $lang['newpass_button'] ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>