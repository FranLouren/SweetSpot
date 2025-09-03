<?php
// backend/auth_check.php
session_start();

// 1) Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no iniciada']);
    exit;
}

// 2) Verificar inactividad (10 minutos = 600 seg)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'error', 'message' => 'Sesión caducada']);
    exit;
}

// 3) Actualizar última actividad
$_SESSION['last_activity'] = time();
