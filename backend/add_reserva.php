<?php
// Decimos que la respuesta será JSON
header('Content-Type: application/json');

// 🔹 Verificación de sesión e inactividad
require_once __DIR__ . '/auth_check.php';

// 🔹 Conexión a la base de datos
require_once __DIR__ . '/config/db.php';

try {
    // 🔹 Capturamos los datos enviados por JS en formato JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // 🔹 Obtenemos cada dato, o null si no existe
    $fecha = $data['fecha'] ?? null;
    $pista_id = $data['pista_id'] ?? null;
    $hora_inicio = $data['hora_inicio'] ?? null;
    $usuario_id = $_SESSION['usuario_id'] ?? null; // Del login

    // 🔹 Validación básica: si falta algún dato, cortamos
    if (!$fecha || !$pista_id || !$hora_inicio || !$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
        exit;
    }

    // 🔹 Validación de franjas horarias permitidas
    $slots_permitidos = ['07:00', '08:30', '10:00', '11:30', '13:00', '14:30', '16:00', '17:30', '19:00', '20:30'];
    if (!in_array($hora_inicio, $slots_permitidos)) {
        echo json_encode(['status' => 'error', 'message' => 'Hora no válida. Usa las franjas horarias establecidas.']);
        exit;
    }

    // 🔹 Validación: no permitir reservas en el pasado
    $reserva_datetime = new DateTime($fecha . ' ' . $hora_inicio); // Fecha y hora de la reserva
    $ahora = new DateTime(); // Fecha y hora actuales
    if ($reserva_datetime < $ahora) {
        echo json_encode(['status' => 'error', 'message' => 'La fecha no puede ser anterior a la actual.']);
        exit;
    }

    // 🔹 Creamos objetos DateTime para calcular la hora de fin de la reserva
    $inicio = new DateTime($hora_inicio);
    $fin = clone $inicio;
    $fin->modify('+1 hour 30 minutes'); // duración fija de 1h30

    // 🔹 Comprobamos si la pista ya está ocupada en ese horario
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

    // 🔹 Si hay solapamiento, mostramos error
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'La pista no está disponible en ese horario']);
        exit;
    }

    // 🔹 Insertamos la nueva reserva en la base de datos
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

    // 🔹 Devolvemos respuesta JSON con éxito y hora de fin calculada
    echo json_encode([
        'status' => 'ok',
        'hora_fin' => $fin->format('H:i:s')
    ]);

} catch (Exception $e) {
    // 🔹 Capturamos cualquier excepción y la devolvemos en JSON
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
