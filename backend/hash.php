<?php
$password = '1234'; // tu contraseña actual en texto plano
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
?>
