<?php
require_once __DIR__ . '/admin_check.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: ../frontend/admin.php");
    exit;
}

// No permitir que el admin se quite a sí mismo el rol
if ($id === (int)$_SESSION['usuario_id']) {
    header("Location: ../frontend/admin.php");
    exit;
}

try {
    // Obtener el rol actual del usuario
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: ../frontend/admin.php");
        exit;
    }

    // Alternar el rol
    $nuevoRol = $usuario['rol'] === 'admin' ? 'usuario' : 'admin';

    $stmt = $conn->prepare("UPDATE usuarios SET rol = :rol WHERE id = :id");
    $stmt->execute([':rol' => $nuevoRol, ':id' => $id]);

    header("Location: ../frontend/admin.php");
    exit;
} catch (PDOException $e) {
    die("Error al cambiar el rol: " . $e->getMessage());
}
