<?php

// Si el usuario elige un idioma en el login, se guarda en sesión
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Si no hay idioma en sesión, por defecto español
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'es';
}

// Cargar el archivo de idioma correspondiente
$lang_file = __DIR__ . '/' . $_SESSION['lang'] . '.php';
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    // Si el archivo no existe, usar español por defecto
    require_once __DIR__ . '/es.php';
}