<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config/db.php'; // conexión PDO

try {
    // Capturamos datos enviados por JS
    $data = json_decode(file_get_contents('php://input'), true);

    $fecha = $data['fecha'] ?? null;
    $pista_id = $data['pista_id'] ?? null;
    $hora_inicio = $data['hora_inicio'] ?? null;
    $usuario_id = $data['usuario_id'] ?? null;

    // Validación básica
    if (!$fecha || !$pista_id || !$hora_inicio || !$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
        exit;
    }

    $inicio = new DateTime($hora_inicio);
    $fin = clone $inicio;
    $fin->modify('+1 hour 30 minutes'); // Duración fija

    // Comprobamos solapamiento con otras reservas
    $stmt = $conn->prepare("
        SELECT * FROM reservas
        WHERE pista_id = :pista_id
          AND fecha = :fecha
          AND (TIME_TO_SEC(hora_inicio) < TIME_TO_SEC(:hora_fin)
               AND ADDTIME(hora_inicio,'01:30:00') > :hora_inicio)
    ");
    $stmt->execute([
        ':pista_id' => $pista_id,
        ':fecha' => $fecha,
        ':hora_inicio' => $inicio->format('H:i:s'),
        ':hora_fin' => $fin->format('H:i:s')
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'La pista no está disponible en ese horario']);
        exit;
    }

    // Insertamos la reserva
    $stmt = $conn->prepare("
        INSERT INTO reservas (usuario_id, pista_id, fecha, hora_inicio)
        VALUES (:usuario_id, :pista_id, :fecha, :hora_inicio)
    ");
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':pista_id' => $pista_id,
        ':fecha' => $fecha,
        ':hora_inicio' => $inicio->format('H:i:s')
    ]);

    echo json_encode([
        'status' => 'ok',
        'hora_fin' => $fin->format('H:i:s')
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
