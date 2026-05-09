<?php
header('Content-Type: application/json');

require_once __DIR__ . '/middleware/Auth_middleware.php';
require_auth_api();
require_once __DIR__ . '/config/db.php';

$fecha = $_GET['fecha'] ?? null;
$pista_id = $_GET['pista_id'] ?? null;

if (!$fecha || !$pista_id) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT hora_inicio FROM reservas WHERE fecha = :fecha AND pista_id = :pista_id");
    $stmt->execute([
        ':fecha' => $fecha,
        ':pista_id' => $pista_id
    ]);

    $horas_ocupadas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $horas_ocupadas[] = substr($row['hora_inicio'], 0, 5);
    }

    echo json_encode(['status' => 'ok', 'ocupadas' => $horas_ocupadas]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}