<?php
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$usuario_id = $_GET['usuario_id'] ?? null;

if (!$usuario_id) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT r.id, r.fecha, r.hora_inicio, 
               ADDTIME(r.hora_inicio, '01:30:00') AS hora_fin, 
               p.nombre AS pista
        FROM reservas r
        JOIN pistas p ON r.pista_id = p.id
        WHERE r.usuario_id = :usuario_id
        ORDER BY r.fecha, r.hora_inicio
    ");
    $stmt->execute([':usuario_id' => $usuario_id]);

    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reservas, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
