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
                    ':nombre'   => $nombre,
                    ':email'    => $email,
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
    <title>Registro</title>
</head>
<body>
    <h1>Crear cuenta</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
        <label>Nombre:<br>
            <input type="text" name="nombre" required value="<?php echo htmlspecialchars($nombre); ?>">
        </label><br><br>

        <label>Email:<br>
            <input type="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
        </label><br><br>

        <label>Contraseña:<br>
            <input type="password" name="password" required autocomplete="new-password">
        </label><br><br>

        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</body>
</html>
