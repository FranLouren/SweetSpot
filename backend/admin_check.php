<?php
session_start();

// 1) Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../frontend/login.php");
    exit;
}

// 2) Verificar inactividad (10 minutos = 600 seg)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
    session_unset();
    session_destroy();
    header("Location: ../frontend/login.php?expired=1");
    exit;
}

// 3) Actualizar última actividad
$_SESSION['last_activity'] = time();

require_once __DIR__ . '/config/db.php';

// 4) Verificar que el usuario sea admin
$stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['rol'] !== 'admin') {
    echo "Acceso denegado. No tienes permisos de administrador.";
    exit;
}
