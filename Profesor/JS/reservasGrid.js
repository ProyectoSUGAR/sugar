document.addEventListener('DOMContentLoaded', function() {
    // HORARIOS se inyecta desde PHP en el HTML principal (reservarEspacio.php)
    if (typeof HORARIOS === 'undefined') {
        console.error("La variable HORARIOS no está definida. Asegúrate de que se inyecta correctamente en el PHP.");
        return;
    }
    initializeReservasTable();
    cargarYRenderizarEspacios();
});

/**
 * Agrega los horarios al encabezado de la tabla.
 */
function initializeReservasTable() {
    const tablaReservas = document.getElementById('tabla-reservas');
    if (!tablaReservas) return;
    const headerRow = tablaReservas.querySelector('thead tr');
    if (!headerRow) return;
    
    HORARIOS.forEach(horario => {
        const th = document.createElement('th');
        th.textContent = horario;
        headerRow.appendChild(th);
    });
}

/**
 * Obtiene la lista completa de espacios desde el backend y los renderiza en la tabla.
 */
function cargarYRenderizarEspacios() {
    fetch('../PHP/procesarReserva.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=obtenerEspacios'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderizarEspacios(data.data);
        } else {
            mostrarError('Error al cargar espacios', data.message);
        }
    })
    .catch(error => {
        mostrarError('Error de conexión al cargar espacios', error);
    });
}

/**
 * Renderiza las filas de la tabla, una para cada espacio, con celdas para cada horario.
 * @param {Array} espacios - El array de objetos de espacio.
 */
function renderizarEspacios(espacios) {
    const tbody = document.querySelector('#tabla-reservas tbody');
    if (!tbody) return;
    tbody.innerHTML = ''; // Limpiar contenido existente

    const espaciosPorTipo = {};
    espacios.forEach(espacio => {
        if (!espaciosPorTipo[espacio.tipo_espacio]) {
            espaciosPorTipo[espacio.tipo_espacio] = [];
        }
        espaciosPorTipo[espacio.tipo_espacio].push(espacio);
    });

    Object.entries(espaciosPorTipo).forEach(([tipo, espaciosDelTipo]) => {
        if (Object.keys(espaciosPorTipo).length > 1) {
            const tipoRow = document.createElement('tr');
            const tipoCell = document.createElement('td');
            tipoCell.textContent = tipo;
            tipoCell.colSpan = HORARIOS.length + 1;
            tipoCell.className = 'header-tipo-espacio';
            tipoRow.appendChild(tipoCell);
            tbody.appendChild(tipoRow);
        }

        espaciosDelTipo.forEach(espacio => {
            const row = document.createElement('tr');
            row.dataset.idEspacio = espacio.id_espacio;
            
            const espacioCell = document.createElement('td');
            espacioCell.textContent = espacio.nombre;
            espacioCell.className = 'celda-espacio';
            row.appendChild(espacioCell);
            
            HORARIOS.forEach(horario => {
                const cell = document.createElement('td');
                cell.className = 'celda-horario disponible';
                cell.dataset.horario = horario;
                cell.dataset.idEspacio = espacio.id_espacio;
                cell.addEventListener('click', () => seleccionarHorario(cell));
                row.appendChild(cell);
            });
            
            tbody.appendChild(row);
        });
    });
}

/**
 * Carga la disponibilidad (reservas y clases) para una fecha específica.
 * @param {Date} fecha - La fecha seleccionada en el calendario.
 */
function cargarDisponibilidadDia(fecha) {
    if (!fecha) return;
    const formattedFecha = fecha.toISOString().split('T')[0];

    fetch('../PHP/procesarReserva.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `accion=obtenerReservasDia&fecha=${formattedFecha}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarDisponibilidad(data.data);
        } else {
            mostrarError('Error al cargar disponibilidad', data.message);
        }
    })
    .catch(error => {
        mostrarError('Error de conexión al cargar disponibilidad', error);
    });
}

/**
 * Actualiza la tabla para reflejar los horarios ocupados.
 * @param {Object} data - El objeto que contiene arrays de `reservas` y `clases`.
 */
function actualizarDisponibilidad(data) {
    const todasLasCeldas = document.querySelectorAll('#tabla-reservas .celda-horario');
    
    // 1. Resetear todas las celdas a 'disponible'
    todasLasCeldas.forEach(cell => {
        cell.classList.remove('ocupado', 'ocupado-clase', 'ocupado-reserva');
        cell.classList.add('disponible');
        cell.innerHTML = ''; // Limpiar contenido previo
    });

    const { reservas = [], clases = [] } = data;

    // 2. Marcar las celdas ocupadas por clases
    clases.forEach(clase => {
        const selector = `.celda-horario[data-id-espacio='${clase.id_espacio}'][data-horario='${clase.horario}']`;
        const celda = document.querySelector(selector);
        if (celda) {
            celda.classList.remove('disponible');
            celda.classList.add('ocupado', 'ocupado-clase');
            celda.innerHTML = `<span>${clase.nombre_asignatura}<br><small>${clase.nombre_profesor}</small></span>`;
        }
    });

    // 3. Marcar las celdas ocupadas por reservas
    reservas.forEach(reserva => {
        // Asumimos que las reservas pueden ocupar un solo bloque de horario
        const horarioReserva = reserva.fecha_inicio; // o el campo que corresponda
        const selector = `.celda-horario[data-id-espacio='${reserva.id_espacio}'][data-horario='${horarioReserva}']`;
        const celda = document.querySelector(selector);
        if (celda) {
            celda.classList.remove('disponible');
            celda.classList.add('ocupado', 'ocupado-reserva');
            celda.innerHTML = `<span>Reservado<br><small>${reserva.nombre_usuario} ${reserva.apellido_usuario}</small></span>`;
        }
    });
}


/**
 * Maneja la selección de una celda de horario en la tabla.
 * @param {HTMLElement} celda - La celda (TD) que fue clickeada.
 */
function seleccionarHorario(celda) {
    if (celda.classList.contains('ocupado')) {
        // Opcional: mostrar un mensaje de que no se puede seleccionar
        return;
    }
    
    const celdaAnterior = document.querySelector('.celda-horario.seleccionado');
    if (celdaAnterior) {
        celdaAnterior.classList.remove('seleccionado');
    }
    
    celda.classList.add('seleccionado');
    
    // Actualizar el formulario de reserva
    const idEspacio = celda.dataset.idEspacio;
    const horario = celda.dataset.horario;
    
    const espacioSelect = document.getElementById('espacio');
    const horarioSelect = document.getElementById('horario');

    if (espacioSelect) espacioSelect.value = idEspacio;
    if (horarioSelect) horarioSelect.value = horario;
}

/**
 * Muestra un error en la consola.
 * @param {string} titulo - Un título para el error.
 * @param {any} mensaje - El mensaje o objeto de error.
 */
function mostrarError(titulo, mensaje) {
    console.error(titulo, mensaje);
    // Aquí se podría implementar una notificación más visible para el usuario.
}
