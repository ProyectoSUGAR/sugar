// Espera a que todo el contenido HTML se haya cargado completamente
document.addEventListener('DOMContentLoaded', function() {
    // Array que contiene las rutas y descripciones de las imágenes de los planos por piso
    const planos = [
        { src: "/Images/placeholder.png", alt: "Plano Planta baja" },
        { src: "/Images/placeholder.png", alt: "Plano Piso 1" },
        { src: "/Images/placeholder.png", alt: "Plano Piso 2" }
    ];

    // Selección de todos los botones que permiten elegir el piso
    const btns = document.querySelectorAll('.btn-piso');

    // Selección del elemento <img> donde se mostrará el plano correspondiente
    const imgPlano = document.getElementById('imagen-plano');

    // Iteración sobre cada botón para asignar el evento de clic
    btns.forEach(btn => {
        // Evento que se ejecuta al hacer clic en un botón de piso
        btn.addEventListener('click', function() {
            // Elimina la clase 'activo' de todos los botones para desactivar el estado visual
            btns.forEach(b => b.classList.remove('activo'));

            // Agrega la clase 'activo' al botón que fue clickeado
            btn.classList.add('activo');

            // Obtiene el número de piso desde el atributo personalizado 'data-piso'
            const piso = parseInt(btn.getAttribute('data-piso'));

            // Actualiza la imagen del plano según el piso seleccionado
            imgPlano.src = planos[piso].src;
            imgPlano.alt = planos[piso].alt;
        });
    });
});