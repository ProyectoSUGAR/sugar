document.addEventListener('DOMContentLoaded', function () {
    // Manejar los formularios de eliminación
    document.querySelectorAll('.eliminar-form').forEach(function(formulario) {
        formulario.addEventListener('submit', function(evento) {
            evento.preventDefault();

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará permanentemente al usuario',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#071739',
                cancelButtonColor: '#d33',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Preparar los datos del formulario
                    const formData = new FormData(formulario);
                    formData.append('eliminar_usuario', '1'); // Asegurarnos de que se envía el campo eliminar_usuario

                    // Mostrar indicador de carga
                    Swal.fire({
                        title: 'Eliminando usuario...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Enviar solicitud mediante fetch
                    fetch(formulario.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: data.message || 'Usuario eliminado correctamente',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Error al procesar la solicitud');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Ha ocurrido un error al procesar la solicitud',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.reload();
                        });
                    });
                }
            });
        });
    });

    // Manejar los formularios de cambio de estado
    document.querySelectorAll('.accion-form').forEach(function(formulario) {
        if (!formulario.classList.contains('eliminar-form')) {
            formulario.addEventListener('submit', function(evento) {
                evento.preventDefault();

                const formData = new FormData(formulario);
                formData.append('cambiar_estado', '1');

                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Actualizando estado...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar solicitud mediante fetch
                fetch(formulario.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message || 'Estado actualizado correctamente',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Error al procesar la solicitud');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Ha ocurrido un error al procesar la solicitud',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                });
            });
        }
    });
});
