<?php
// Inicia la sesión para acceder a $_SESSION
session_start();

// 1) Verificar si hay sesión activa
// Si no hay 'usuario_id' en la sesión, significa que el usuario no está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Redirige al login
    header("Location: login.php");
    exit; // Importante para detener la ejecución del resto del script
}

// 2) Verificar inactividad (10 minutos = 600 segundos)
// Si existe 'last_activity' y ha pasado más de 600 segundos desde la última acción
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
    // Cerrar sesión por inactividad
    session_unset();    // Elimina todas las variables de sesión
    session_destroy();  // Destruye la sesión
    // Redirige al login indicando timeout
    header("Location: ../frontend/login.php?timeout=1");
    exit; // Detiene la ejecución del resto del script
}

// Actualizar última actividad a la hora actual
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas de Padel</title>
</head>
<body>
    <h1>Pistas disponibles</h1>

    <!-- Formulario para hacer una reserva -->
    <form id="reservaForm">
        <input type="date" id="fecha" required>
        <select id="pista" required>
            <option value="" disabled selected>Selecciona una pista</option>
            <option value="1">Pista 1</option>
            <option value="2">Pista 2</option>
            <option value="3">Pista 3</option>
            <option value="4">Pista 4</option>
        </select>
        <input type="time" id="hora_inicio" required>
        <button type="submit">Reservar</button>
    </form>

    <h2>Mis Reservas</h2>
    <!-- Tabla donde se mostrarán las reservas del usuario -->
    <table id="tablaReservas" border="1">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Hora inicio</th>
          <th>Hora fin</th>
          <th>Pista</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody> <!-- El contenido se llenará dinámicamente con JS -->
    </table>

    <!-- Formulario para cerrar sesión -->
    <form action="logout.php" method="POST" style="margin-top:16px;">
        <button type="submit">Cerrar sesión</button>
    </form>

    <!-- Script principal que maneja las reservas -->
    <script src="js/main.js"></script>
    <script>
      // Inyecta el ID del usuario de la sesión a JS
      const usuarioId = <?php echo (int)$_SESSION['usuario_id']; ?>;
      // Llama a la función para cargar las reservas del usuario logueado
      cargarReservas(usuarioId);
    </script>
</body>
</html>
