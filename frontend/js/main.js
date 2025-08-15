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
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error al enviar la reserva:', error));
});
