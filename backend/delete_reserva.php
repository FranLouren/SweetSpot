<?php
session_start(); // Inicia la sesión para acceder a los datos del usuario autenticado
require_once __DIR__ . '/config/db.php'; // Incluye la conexión a la base de datos
header('Content-Type: application/json; charset=utf-8'); // Define el tipo de respuesta como JSON

$id = $_GET['id'] ?? null; // Obtiene el ID de la reserva a cancelar desde la URL
$usuario_id = $_SESSION['usuario_id'] ?? null; // Obtiene el ID del usuario autenticado desde la sesión

// Verifica que se haya recibido el ID de la reserva y que el usuario esté autenticado
if (!$id || !$usuario_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    exit;
}

try {
    // Intenta eliminar la reserva solo si pertenece al usuario autenticado
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);

    // Si se eliminó alguna fila, la reserva era del usuario y se canceló correctamente
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'ok']);
    } else {
        // Si no se eliminó ninguna fila, la reserva no pertenece al usuario o no existe
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para cancelar esta reserva']);
    }
} catch (PDOException $e) {
    // Si ocurre un error en la base de datos, lo informa en la respuesta
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} // <-- Faltaba esta llave de cierre

?>