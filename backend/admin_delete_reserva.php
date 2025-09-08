<?php
require_once __DIR__ . '/admin_check.php';

$id = $_GET['id'] ?? null;

if ($id) {
    
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: ../frontend/admin.php");
exit;
