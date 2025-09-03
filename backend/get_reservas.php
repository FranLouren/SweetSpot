<?php

// Indicamos que la salida será JSON
header('Content-Type: application/json; charset=utf-8');

// Verificación de sesión
require_once __DIR__ . '/auth_check.php';

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
    // Preparamos la consulta para obtener las reservas del usuario
    // JOIN con la tabla pistas para obtener el nombre de la pista
    // ADDTIME() calcula la hora de fin sumando 1 hora 30 minutos a la hora de inicio
    $stmt = $conn->prepare("
        SELECT r.id, 
               r.fecha, 
               r.hora_inicio, 
               ADDTIME(r.hora_inicio, '01:30:00') AS hora_fin, 
               p.nombre AS pista
        FROM reservas r
        JOIN pistas p ON r.pista_id = p.id
        WHERE r.usuario_id = :usuario_id
        ORDER BY r.fecha, r.hora_inicio
    ");

    // Ejecutamos la consulta pasando el parámetro seguro del usuario
    $stmt->execute([':usuario_id' => $usuario_id]);

    // Obtenemos todas las reservas en un array asociativo
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos las reservas en formato JSON
    echo json_encode($reservas, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Si hay un error en la base de datos, devolvemos un JSON con el mensaje de error
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
