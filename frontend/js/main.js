fetch('../backend/get_pistas.php')
    .then(res => res.json())
    .then(data => {
        const lista = document.getElementById('lista-pistas');
        data.forEach(pista => {
            const li = document.createElement('li');
            li.textContent = pista.nombre;
            lista.appendChild(li);
        });
    })
    .catch(err => console.error("Error cargando pistas:", err));
