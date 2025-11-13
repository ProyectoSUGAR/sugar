
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formulario-login');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const usuario = form.querySelector('[name="usuario"]').value;
        const password = form.querySelector('[name="password"]').value;
    fetch('../../Login/PHP/ingreso.php', {
            method: 'POST', // Método de envío
            headers: { 'Content-Type': 'application/json' }, // Tipo de contenido enviado
            body: JSON.stringify({ usuario, password }) // Convierte los datos a formato JSON
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire('Error', data.message || 'Error desconocido', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
        });
    });
});
