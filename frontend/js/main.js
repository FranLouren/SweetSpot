document.getElementById('reservaForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const fecha = document.getElementById('fecha').value;
    const pista_id = document.getElementById('pista').value;
    const hora_inicio = document.getElementById('hora_inicio').value;

    const reserva = {
        fecha: fecha,
        pista_id: pista_id,
        hora_inicio: hora_inicio,
        usuario_id: 1
    };

    fetch('http://localhost/Golpe_maestro/backend/add_reserva.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(reserva)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'ok'){
            alert('Reserva realizada correctamente');
            cargarReservas(1); // 🔄 refresca la tabla automáticamente
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error al enviar la reserva:', error));
});

function cargarReservas(usuarioId = 1) {
    fetch(`http://localhost/Golpe_maestro/backend/get_reservas.php?usuario_id=${usuarioId}`)
        .then(response => response.json())
        .then(reservas => {
            console.log("Reservas recibidas:", reservas);

            const tbody = document.querySelector('#tablaReservas tbody');
            tbody.innerHTML = '';

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

function cancelarReserva(id) {
    if (!confirm('¿Seguro que quieres cancelar esta reserva?')) return;

    fetch(`http://localhost/Golpe_maestro/backend/delete_reserva.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            alert('Reserva cancelada');
            cargarReservas(1); // 🔄 refresca la tabla automáticamente
        } else {
            alert('Error al cancelar: ' + data.message);
        }
    })
    .catch(error => console.error('Error al cancelar reserva:', error));
}

// Asegurarte de que esté disponible globalmente
window.cancelarReserva = cancelarReserva;


// Llamada inicial para cargar las reservas al abrir la página
cargarReservas(1);
