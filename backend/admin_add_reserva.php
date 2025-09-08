<?php
require_once __DIR__ . '/admin_check.php'; // Solo admins

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? null;
    $pista_id   = $_POST['pista_id'] ?? null;
    $fecha      = $_POST['fecha'] ?? null;
    $hora       = $_POST['hora'] ?? null;

    if ($usuario_id && $pista_id && $fecha && $hora) {

        // 🔹 Validar que la fecha + hora no sean del pasado
        $reserva_datetime = new DateTime("$fecha $hora");
        $ahora = new DateTime();
        if ($reserva_datetime < $ahora) {
            header("Location: ../frontend/admin.php?error=pasado");
            exit;
        }

        try {
            // 🔹 Verificar si la pista está ocupada en esa fecha y hora
            $stmt = $conn->prepare("
                SELECT id FROM reservas
                WHERE pista_id = :pista_id
                  AND fecha = :fecha
                  AND hora_inicio = :hora
            ");
            $stmt->execute([
                ':pista_id' => $pista_id,
                ':fecha'    => $fecha,
                ':hora'     => $hora
            ]);

            if ($stmt->fetch()) {
                header("Location: ../frontend/admin.php?error=ocupada");
                exit;
            }

            // 🔹 Insertar reserva
            $stmt = $conn->prepare("
                INSERT INTO reservas (usuario_id, pista_id, fecha, hora_inicio)
                VALUES (:usuario_id, :pista_id, :fecha, :hora)
            ");
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':pista_id'   => $pista_id,
                ':fecha'      => $fecha,
                ':hora'       => $hora
            ]);

            header("Location: ../frontend/admin.php?success=1");
            exit;

        } catch (PDOException $e) {
            header("Location: ../frontend/admin.php?error=db");
            exit;
        }
    } else {
        header("Location: ../frontend/admin.php?error=datos");
        exit;
    }
}
 