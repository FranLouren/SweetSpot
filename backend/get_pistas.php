<?php
require_once __DIR__ . '/config/db.php';


try {
    $stmt = $conn->prepare("SELECT * FROM pistas");
    $stmt->execute();

    $pistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($pistas) {
        echo json_encode($pistas);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}