document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.eliminar-form').forEach(function(formulario) {
        formulario.addEventListener('submit', function(evento) {
            if (!confirm('¿Seguro que deseas eliminar este usuario?')) {
                evento.preventDefault();
            }
        });
    });
});