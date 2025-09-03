// Escucha el envío del formulario de reserva
document.getElementById('reservaForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Evita el envío tradicional del formulario

    // Obtiene los valores del formulario
    const fecha = document.getElementById('fecha').value;
    const pista_id = document.getElementById('pista').value;
    const hora_inicio = document.getElementById('hora_inicio').value;

    // Crea el objeto reserva usando el usuarioId global
    const reserva = {
        fecha: fecha,
        pista_id: pista_id,
        hora_inicio: hora_inicio,
        usuario_id: usuarioId // Variable global inyectada desde PHP
    };

    // Envía la reserva al backend usando fetch y método POST
    fetch('http://localhost/Golpe_maestro/backend/add_reserva.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(reserva)
    })
    .then(response => response.json()) // Convierte la respuesta en JSON
    .then(data => {
        if (data.status === 'ok') {
            alert('Reserva realizada correctamente');

            // 🔹 Vaciar campos del formulario
            document.getElementById('fecha').value = '';
            document.getElementById('hora_inicio').value = '';
            
            // 🔹 Restablecer el select a la opción inicial
            const selectPista = document.getElementById('pista');
            selectPista.value = ''; // Valor vacío de la opción "Selecciona una pista"

            // 🔹 Refresca la tabla de reservas
            cargarReservas(usuarioId);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error al enviar la reserva:', error));
});

// Función para cargar las reservas del usuario
function cargarReservas(uid) {
    fetch(`http://localhost/Golpe_maestro/backend/get_reservas.php?usuario_id=${uid}`)
        .then(response => response.json())
        .then(reservas => {
            const tbody = document.querySelector('#tablaReservas tbody');
            tbody.innerHTML = ''; // Limpia la tabla

            if (!reservas || reservas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">No tienes reservas</td></tr>';
                return;
            }

            reservas.forEach(reserva => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${reserva.fecha}</td>
                    <td>${reserva.hora_inicio}</td>
                    <td>${reserva.hora_fin}</td>
                    <td>${reserva.pista}</td>
                    <td>
                        <button onclick="cancelarReserva(${reserva.id})">Cancelar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error al cargar reservas:', error));
}

// Función para cancelar una reserva
function cancelarReserva(id) {
    if (!confirm('¿Seguro que quieres cancelar esta reserva?')) return;

    fetch(`http://localhost/Golpe_maestro/backend/delete_reserva.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            alert('Reserva cancelada');
            cargarReservas(usuarioId);
        } else {
            alert('Error al cancelar: ' + data.message);
        }
    })
    .catch(error => console.error('Error al cancelar reserva:', error));
}

// Hace la función cancelarReserva accesible desde el HTML
window.cancelarReserva = cancelarReserva;
