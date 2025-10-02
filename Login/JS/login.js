// Espera a que todo el contenido HTML se haya cargado completamente
document.addEventListener('DOMContentLoaded', () => {

    // Obtiene el formulario de login por su ID
    const form = document.getElementById('formulario-login');

    // Si el formulario no existe, se detiene la ejecución
    if (!form) return;

    // Evento que se ejecuta al enviar el formulario
    form.addEventListener('submit', function (e) {
        // Previene el envío tradicional del formulario
        e.preventDefault();

        // Obtiene el valor del campo 'usuario' dentro del formulario
        const usuario = form.querySelector('[name="usuario"]').value;

        // Obtiene el valor del campo 'password' dentro del formulario
        const password = form.querySelector('[name="password"]').value;

        // Realiza una solicitud HTTP POST al servidor para validar las credenciales
        fetch('/Login/PHP/login.php', {
            method: 'POST', // Método de envío
            headers: { 'Content-Type': 'application/json' }, // Tipo de contenido enviado
            body: JSON.stringify({ usuario, password }) // Convierte los datos a formato JSON
        })
        // Convierte la respuesta del servidor a formato JSON
        .then(res => res.json())

        // Maneja la respuesta recibida del servidor
        .then(data => {
            // Si el login fue exitoso, muestra mensaje y redirige al dashboard correspondiente
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                // Si hubo error en las credenciales, muestra mensaje de error
                Swal.fire('Error', data.message || 'Error desconocido', 'error');
            }
        })

        // Maneja errores de conexión con el servidor
        .catch(() => {
            Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
        });
    });
});
