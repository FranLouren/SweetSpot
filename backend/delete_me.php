<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Si no está logueado, fuera
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../frontend/login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
    // Borramos todas sus reservas primero por seguridad (por si no hay borrado en cascada en la BD)
    $stmt = $conn->prepare("DELETE FROM reservas WHERE usuario_id = :uid");
    $stmt->execute([':uid' => $usuario_id]);

    // Borramos al usuario
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :uid");
    $stmt->execute([':uid' => $usuario_id]);

    // Destruimos la sesión y lo mandamos a la portada
    session_destroy();
    header("Location: ../frontend/index.php?deleted=1");
    exit;
} catch (PDOException $e) {
    die("Error al eliminar la cuenta: " . $e->getMessage());
}
