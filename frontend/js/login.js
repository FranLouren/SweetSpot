document.getElementById('loginForm').addEventListener('submit', function(e){
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    fetch('http://localhost/Golpe_maestro/backend/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'ok'){
            alert('Bienvenido ' + data.nombre);
            localStorage.setItem('usuario_id', data.usuario_id); // guardamos usuario
            window.location.href = 'index.html'; // redirigimos a la app
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error('Error en login:', err));
});
