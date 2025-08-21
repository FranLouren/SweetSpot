<?php
// Inicia o reanuda la sesión actual. Es un paso obligatorio para manipular las variables de sesión.
session_start();

// Elimina todas las variables de la sesión, liberando los datos del usuario.
session_unset();

// Destruye completamente la sesión en el servidor, borrando el identificador.
session_destroy();

// Redirige al usuario al archivo de login.php.
header("Location: login.php");

// Detiene la ejecución del script para asegurar que no se ejecute más código.
exit;