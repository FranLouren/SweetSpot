<?php
require_once __DIR__ . '/admin_check.php';

$id = $_GET['id'] ?? null;

if ($id) {

    try {
        // Primero borramos todas las reservas del usuario
        $stmt = $conn->prepare("DELETE FROM reservas WHERE usuario_id = :id");
        $stmt->execute([':id' => $id]);

        // Luego borramos al usuario
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        // Por si hay algún error de base de datos
        echo "Error al eliminar usuario: " . $e->getMessage();
        exit;
    }
}

header("Location: ../frontend/admin.php");
exit;
