<?php

$host = "localhost";
$db_name = "padel_reservas";
$user_name = "root";
$password = "";

// Intento de conexión a la base de datos mediante PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $user_name, $password);
    // PDO lanza excepciones en caso de error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    //Se captura el error y se muestra por pantalla
    echo "Connection failed: " . $e->getMessage();
}





























?>