<?php
session_start();
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

    if (!$nombre || !$email || !$password) {
        $error = "Por favor completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es válido.";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $error = "El email ya está registrado.";
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
</head>

<body>
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
                <p class="brand-subtitle">Reserva tu pista de pádel</p>
            </div>

            <!-- Card del formulario -->
            <div class="card card-custom">
                <div class="card-header text-center">
                    <h4 class="mb-0 text-white">Crear cuenta</h4>
                </div>
                <div class="card-body">
                    <!-- Mensaje de error -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <!-- Formulario de registro -->
                    <form method="POST" action="" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Nombre:</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" required
                                value="<?php echo htmlspecialchars($nombre); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control" placeholder="tu@email.com" required
                                value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña:</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Mínimo 6 caracteres" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-orange w-100">Registrarse</button>
                    </form>

                    <!-- Enlace a login -->
                    <div class="text-center mt-4">
                        <span class="text-muted">¿Ya tienes cuenta?</span>
                        <a href="login.php">Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>