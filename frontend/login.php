<?php
// Inicia la sesión para poder usar $_SESSION
session_start();

// Incluye la conexión a la base de datos (archivo con PDO)
require_once __DIR__ . '/../backend/config/db.php';

// Si ya hay un usuario logueado, lo redirigimos directamente a la página principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php"); // Redirige a index.php
    exit; // Termina la ejecución del script
}

// Inicializamos una variable para almacenar mensajes de error
$error = '';

// Comprobamos si se ha enviado el formulario (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los datos enviados desde el formulario HTML
    $email = $_POST['email'] ?? ''; // Si no existe, ponemos cadena vacía
    $password = $_POST['password'] ?? '';

    // Verificamos que ambos campos estén llenos
    if ($email && $password) {
        try {
            // Preparamos una consulta segura con PDO para buscar el usuario por email
            $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]); // Ejecutamos la consulta pasando el email como parámetro seguro
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Obtenemos la fila como array asociativo

            // Verificamos si existe el usuario y si la contraseña coincide con password_verify()
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Guardamos el ID del usuario en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                // Redirigimos a la página principal
                header("Location: index.php");
                exit;
            } else {
                // Si usuario o contraseña son incorrectos, guardamos mensaje de error
                $error = 'Usuario o contraseña incorrectos';
            }
        } catch (PDOException $e) {
            // Si ocurre un error de base de datos, lo guardamos en la variable de error
            $error = 'Error en la base de datos: ' . $e->getMessage();
        }
    } else {
        // Si no se llenaron los campos, mostramos un mensaje
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
</body>
</html>
