// Código JavaScript para manejar los planos y horarios
// DOMContentLoaded es un evento que se dispara cuando el documento HTML ha sido completamente cargado y parseado
document.addEventListener('DOMContentLoaded', function() {
    // Array con las rutas y descripciones de las imágenes de los planos por piso
    const planos = [
        { src: "/Images/PlantaBaja.jpeg", alt: "Plano Planta baja" },
        { src: "/Images/Piso1y2.jpeg", alt: "Plano Piso 1" },
        { src: "/Images/Piso1y2.jpeg", alt: "Plano Piso 2" }
    ];

    // Array que contiene los salones disponibles por cada piso
    const salonesPorPiso = [
        ["Aula 1", "Laboratorio de Electrónica", "Laboratorio de Química", "Laboratorio de Robótica", "Zoom", "Taller"],
        ["Aula 2", "Salón 1", "Salón 2", "Laboratorio de Física"],
        ["Aula 3", "Salón 3", "Salón 4", "Salón 5"]
    ];

    // Bloques horarios definidos para cada turno
    const bloques = ["1°", "2°", "3°", "4°", "5°", "6°", "7°", "8°"];

    // Definición de los turnos con sus respectivos bloques
    const turnos = [
        { id: "manana", titulo: "Turno Mañana", bloques: bloques },
        { id: "tarde", titulo: "Turno Tarde", bloques: bloques },
        { id: "noche", titulo: "Turno Noche", bloques: bloques }
    ];

    // Selección de los botones de piso
    const btns = document.querySelectorAll('.btn-piso');

    // Selección del elemento <img> donde se mostrará el plano correspondiente
    const imgPlano = document.getElementById('imagen-plano');

    // Selección del contenedor donde se insertarán las tablas de horarios
    const contenedorTablas = document.getElementById('contenedor-tablas-horarios');

    // Función que genera dinámicamente las tablas de horarios según el piso seleccionado
    function crearTablasHorarios(piso) {
        // Limpia el contenido previo del contenedor
        contenedorTablas.innerHTML = "";

        // Itera sobre cada turno para generar su sección y tabla correspondiente
        turnos.forEach(turno => {
            const section = document.createElement('section');
            section.className = "turno";
            section.id = `turno-${turno.id}`;

            // Título del turno
            const h3 = document.createElement('h3');
            h3.textContent = turno.titulo;
            section.appendChild(h3);

            // Creación de la tabla de horarios
            const table = document.createElement('table');
            table.className = "tabla-horarios";
            table.id = `tabla-${turno.id}`;

            // Encabezado de la tabla
            const thead = document.createElement('thead');
            const trHead = document.createElement('tr');
            const thSalon = document.createElement('th');
            thSalon.textContent = "Espacio";
            trHead.appendChild(thSalon);

            // Agrega las columnas correspondientes a los bloques horarios
            turno.bloques.forEach(bloque => {
                const th = document.createElement('th');
                th.textContent = bloque;
                trHead.appendChild(th);
            });

            thead.appendChild(trHead);
            table.appendChild(thead);

            // Cuerpo de la tabla con los salones y celdas vacías
            const tbody = document.createElement('tbody');
            salonesPorPiso[piso].forEach((salon, idx) => {
                const tr = document.createElement('tr');
                const tdSalon = document.createElement('td');
                tdSalon.textContent = salon;
                tr.appendChild(tdSalon);

                turno.bloques.forEach((_, i) => {
                    const td = document.createElement('td');
                    if (idx === 0 && i === 0) {
                        td.innerHTML = ""; // Celda inicial vacía como ejemplo
                    }
                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            section.appendChild(table);
            contenedorTablas.appendChild(section);
        });
    }

    // Inicializa la vista con el plano y horarios del primer piso (planta baja)
    crearTablasHorarios(0);

    // Asigna eventos a los botones para cambiar de piso y actualizar plano y horarios
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            btns.forEach(b => b.classList.remove('activo')); // Quita clase activa de todos los botones
            btn.classList.add('activo'); // Activa el botón clickeado

            const piso = parseInt(btn.getAttribute('data-piso')); // Obtiene el número de piso
            imgPlano.src = planos[piso].src; // Actualiza la imagen del plano
            imgPlano.alt = planos[piso].alt; // Actualiza el texto alternativo
            crearTablasHorarios(piso); // Genera las tablas de horarios para el piso seleccionado
        });
    });
});