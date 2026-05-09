<?php

// Indicamos que la salida será JSON
header('Content-Type: application/json; charset=utf-8');

// Verificación de sesión
require_once __DIR__ . '/middleware/Auth_middleware.php';
require_auth_api();

// Incluye la conexión a la base de datos
require_once __DIR__ . '/config/db.php';

// Obtenemos el ID del usuario de la sesión
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Si no hay usuario logueado, devolvemos un array vacío y salimos
if (!$usuario_id) {
    echo json_encode([]);
    exit;
}

try {
    // Consulta para obtener todas las reservas del usuario
    $stmt = $conn->prepare("
        SELECT r.id, 
               r.fecha, 
               r.hora_inicio, 
               ADDTIME(r.hora_inicio, '01:30:00') AS hora_fin, 
               p.nombre AS pista
        FROM reservas r
        JOIN pistas p ON r.pista_id = p.id
        WHERE r.usuario_id = :usuario_id
        ORDER BY r.fecha DESC, r.hora_inicio DESC
    ");

    $stmt->execute([':usuario_id' => $usuario_id]);
    $todasReservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separamos en activas e historial
    $activas = [];
    $historial = [];
    $ahora = new DateTime();

    foreach ($todasReservas as $reserva) {
        $finReserva = new DateTime($reserva['fecha'] . ' ' . $reserva['hora_fin']);

        if ($finReserva > $ahora) {
            $activas[] = $reserva;
        } else {
            $historial[] = $reserva;
        }
    }

    // Ordenamos las activas por fecha ascendente
    usort($activas, function ($a, $b) {
        return strcmp($a['fecha'] . $a['hora_inicio'], $b['fecha'] . $b['hora_inicio']);
    });

    // Devolvemos ambos arrays
    echo json_encode([
        'activas' => $activas,
        'historial' => $historial
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}