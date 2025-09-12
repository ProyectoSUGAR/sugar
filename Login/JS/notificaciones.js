document.addEventListener("DOMContentLoaded", () => {
    fetch("../JSON/mensajes.json")
        .then(response => response.json())
        .then(data => {
            if (data.estado && data.mensaje) {
                Swal.fire({
                    icon: data.estado,
                    title: data.estado.charAt(0).toUpperCase() + data.estado.slice(1),
                    text: data.mensaje,
                    confirmButtonColor: "#3085d6"
                });

                // Limpiar el mensaje después de mostrarlo
                                fetch("../PHP/limpiar_mensaje.php");
            }
        });
});
