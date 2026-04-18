<?php
// Inicia la sesión para poder usar $_SESSION
session_start();

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
                $error = 'Usuario o contraseña incorrectos';
            }
        } catch (PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
        }
    } else {
        $error = 'Por favor ingresa email y contraseña';
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
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo y título -->
            <div class="text-center mb-4">
                <a href="index.php" class="d-flex align-items-center justify-content-center gap-3 mb-2 text-decoration-none">
                    <img src="img/logo.jpg" alt="Logo" class="logo-icon">
                    <h1 class="brand-title mb-0"><span class="text-sweet">Sweet</span><span
                            class="text-spot">Spot</span></h1>
                </a>
                <p class="brand-subtitle">Reserva tu pista de pádel</p>
            </div>

            <!-- Card del formulario -->
            <div class="card card-custom">
                <div class="card-header text-center">
                    <h4 class="mb-0 text-white">Iniciar Sesión</h4>
                </div>
                <div class="card-body">
                    <!-- 🔹 Mensaje si viene de register.php -->
                    <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
                        <div class="alert alert-success">Registro exitoso. Ahora puedes iniciar sesión.</div>
                    <?php endif; ?>

                    <!-- Mostramos el mensaje de error si existe -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <!-- Formulario de login -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña:</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-orange w-100">Entrar</button>
                    </form>

                    <!-- Enlace para registrarse -->
                    <div class="text-center mt-4">
                        <span class="text-muted">¿No tienes cuenta?</span>
                        <a href="register.php">Regístrate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>