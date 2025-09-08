<?php
// Inicia la sesión para poder usar $_SESSION
session_start();

// Incluye la conexión a la base de datos (archivo con PDO)
require_once __DIR__ . '/../backend/config/db.php';

// Si ya hay un usuario logueado, lo redirigimos según su rol
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_rol'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

// Inicializamos una variable para almacenar mensajes de error
$error = '';

// Comprobamos si se ha enviado el formulario (método POST)
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
    <title>Login</title>
</head>
<body>
    <h1>Iniciar sesión</h1>

    <!-- 🔹 Mensaje si viene de register.php -->
    <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
        <p style="color:green;">Registro exitoso. Ahora puedes iniciar sesión.</p>
    <?php endif; ?>

    <!-- Mostramos el mensaje de error si existe -->
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Formulario de login -->
    <form method="POST" action="">
        <label>Email: <input type="email" name="email" required></label><br><br>
        <label>Contraseña: <input type="password" name="password" required></label><br><br>
        <button type="submit">Entrar</button>
    </form>

    <!-- Enlace para registrarse -->
    <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>

</body>
</html>
