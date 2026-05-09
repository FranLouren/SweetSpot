// Escucha el envío del formulario de reserva
document.getElementById('reservaForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const fecha = document.getElementById('fecha').value;
    const pista_id = document.getElementById('pista').value;
    const hora_inicio = document.getElementById('hora_inicio').value;

    // Crea el objeto reserva
    const reserva = {
        fecha: fecha,
        pista_id: pista_id,
        hora_inicio: hora_inicio,
        usuario_id: usuarioId
    };

    // Envía la reserva al backend usando fetch y método POST
    fetch('http://localhost/SweetSpot/backend/add_reserva.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(reserva)
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                alert(langData.bookingSuccess);

                // Vaciar campos del formulario
                document.getElementById('fecha').value = '';
                document.getElementById('hora_inicio').value = '';

                // Restablecer el select a la opción inicial
                const selectPista = document.getElementById('pista');
                selectPista.value = '';

                // Refresca la tabla de reservas
                cargarReservas(usuarioId);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error al enviar la reserva:', error));
});


const campoFecha = document.getElementById('fecha');
const campoPista = document.getElementById('pista');
const campoHora = document.getElementById('hora_inicio');

// Función para comprobar disponibilidad
function checkDisponibilidad() {
    const fechaVal = campoFecha.value;
    const pistaVal = campoPista.value;

    if (!fechaVal || !pistaVal) {
        campoHora.disabled = true;
        campoHora.options[0].selected = true;
        return;
    }

    // Si ambos tienen valor se comprueba la disponibilidad
    fetch(`http://localhost/SweetSpot/backend/get_horas_ocupadas.php?fecha=${fechaVal}&pista_id=${pistaVal}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error') {
                console.error(data.message);
                return;
            }

            // Habilitamos el select 
            campoHora.disabled = false;

            // Recorremos las opciones para marcarlas
            const ocupadas = data.ocupadas || [];

            // Empezamos desde i=1 para ignorar la opcion 'Elige fecha...'
            for (let i = 1; i < campoHora.options.length; i++) {
                const opt = campoHora.options[i];
                const horaOpt = opt.value;

                // Limpiamos primero
                opt.disabled = false;
                opt.textContent = opt.textContent.replace(' ' + langData.slotOccupied, '');

                // Si está ocupada la deshabilitamos
                if (ocupadas.includes(horaOpt)) {
                    opt.disabled = true;
                    opt.textContent += ' ' + langData.slotOccupied;
                }
            }

            // Si la opción que estaba seleccionada resulta estar ocupada ahora, desmarcar
            if (campoHora.selectedOptions.length > 0 && campoHora.selectedOptions[0].disabled) {
                campoHora.options[0].selected = true;
            }
        })
        .catch(err => console.error('Error fetching disponibilidad:', err));
}

campoFecha.addEventListener('change', checkDisponibilidad);
campoPista.addEventListener('change', checkDisponibilidad);


// Función para cargar las reservas del usuario (activas e historial)
function cargarReservas(uid) {
    fetch(`http://localhost/SweetSpot/backend/get_reservas.php?usuario_id=${uid}`)
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);

            if (data.status === 'error') {
                console.error('Error del servidor:', data.message);
                return;
            }

            // Tabla de reservas activas
            const tbodyActivas = document.querySelector('#tablaReservas tbody');
            if (tbodyActivas) {
                tbodyActivas.innerHTML = '';

                if (!data.activas || data.activas.length === 0) {
                    tbodyActivas.innerHTML = `<tr><td colspan="5">${langData.noActive}</td></tr>`;
                } else {
                    data.activas.forEach(reserva => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${reserva.fecha}</td>
                            <td>${reserva.hora_inicio}</td>
                            <td>${reserva.hora_fin}</td>
                            <td>${reserva.pista.replace('Pista', langData.courtPrefix)}</td>
                            <td>
                                <button class="btn btn-outline-danger btn-sm" onclick="cancelarReserva(${reserva.id})">${langData.btnCancel}</button>
                            </td>
                        `;
                        tbodyActivas.appendChild(tr);
                    });
                }
            }

            // Tabla de historial
            const tbodyHistorial = document.querySelector('#tablaHistorial tbody');
            if (tbodyHistorial) {
                tbodyHistorial.innerHTML = '';

                if (!data.historial || data.historial.length === 0) {
                    tbodyHistorial.innerHTML = `<tr><td colspan="4">${langData.noHistory}</td></tr>`;
                } else {
                    data.historial.forEach(reserva => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${reserva.fecha}</td>
                            <td>${reserva.hora_inicio}</td>
                            <td>${reserva.hora_fin}</td>
                            <td>${reserva.pista.replace('Pista', langData.courtPrefix)}</td>
                        `;
                        tbodyHistorial.appendChild(tr);
                    });
                }
            }
        })
        .catch(error => console.error('Error al cargar reservas:', error));
}

// Función para cancelar una reserva
function cancelarReserva(id) {
    if (!confirm(langData.confirmCancel)) return;

    fetch(`http://localhost/SweetSpot/backend/delete_reserva.php?id=${id}`, {
        method: 'DELETE'
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                alert(langData.bookingCancelled);
                cargarReservas(usuarioId);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Hace la función cancelarReserva accesible desde el HTML
window.cancelarReserva = cancelarReserva;