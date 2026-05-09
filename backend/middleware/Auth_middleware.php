<?php
// backend/middleware/Auth_middleware.php

// Iniciar sesión automáticamente si no se ha iniciado antes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../frontend/lang/lang.php';

/**
 * Función interna para revisar si han pasado 10 minutos
 * Return: true si todo está bien | false si la sesión expiró
 */
function check_inactivity()
{
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Funciones para el backend (API / FETCH DE JAVASCRIPT)
// Las que responden con JSON y mueren (exit;)

function require_auth_api()
{
    global $lang;

    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['status' => 'error', 'message' => $lang['auth_not_logged_in']]);
        exit;
    }
    if (!check_inactivity()) {
        echo json_encode(['status' => 'error', 'message' => $lang['auth_session_expired']]);
        exit;
    }
}


function require_admin_api()
{
    require_auth_api();

    global $lang, $conn;
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || $usuario['rol'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => $lang['auth_access_denied']]);
        exit;
    }
}



// Funciones para el frontend

function require_auth_web()
{
    // Si la llamada viene de dentro de /backend bajamos al raiz, si no, lo asumimos directo
    $login_url = (strpos($_SERVER['SCRIPT_NAME'], '/backend/') !== false) ? '../frontend/login.php' : 'login.php';

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: $login_url");
        exit;
    }
    if (!check_inactivity()) {
        header("Location: $login_url?timeout=1");
        exit;
    }
}

function require_admin_web()
{
    require_auth_web();

    global $lang, $conn;
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || $usuario['rol'] !== 'admin') {
        echo $lang['auth_access_denied'];
        exit;
    }
}
