<?php
// backend/auth_check.php
session_start();

// Cargar sistema de idiomas
require_once __DIR__ . '/../frontend/lang/lang.php';

// Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => $lang['auth_not_logged_in']]);
    exit;
}

// Verificar inactividad durante 10 minutos
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'error', 'message' => $lang['auth_session_expired']]);
    exit;
}

// Actualizar última actividad
$_SESSION['last_activity'] = time();