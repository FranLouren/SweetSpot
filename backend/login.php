<?php
require_once __DIR__ . '/config/db.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'ok', 'usuario_id' => $user['id'], 'nombre' => $user['nombre']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email o contraseña incorrectos']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
