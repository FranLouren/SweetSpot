<?php
require_once __DIR__ . '/config/db.php';
header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? null;  // <-- así lo toma de ?id=xxx

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode(['status' => 'ok']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
