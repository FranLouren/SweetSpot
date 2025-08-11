<?php

$host = "localhost";
$db_name = "padel_reservas";
$user_name = "root";
$password = ""; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $user_name, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}





























?>
