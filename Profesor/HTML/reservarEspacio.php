<?php 
session_start();
require_once("../../PHP/conexion.php");
// incluir helpers del módulo de reservas
require_once(__DIR__ . '/../PHP/funcReservas.php');
// Obtener horarios en PHP para uso en selects y para inyectar en JS
$HORARIOS = function_exists('obtenerHorarios') ? obtenerHorarios() : [];

// Debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validación de sesión deshabilitada por petición del usuario.
// Si se necesita volver a habilitar, comprobar `$_SESSION['id_usuario']` y `$_SESSION['tipo_usuario']` aquí.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Espacios</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <!-- Estilos movidos a Css/style.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</head>
<body class="body-reservas">
    <?php require_once '../../HEADERS/headerP.php'; ?>
    <main class="reservas-main">
        <h1 class="reservas-title">Reserva de Espacios</h1>
        
        <div class="reservas-grid">
            <!-- Calendario y formulario -->
            <div class="reservas-form-container">
                <div class="calendario-container">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Seleccionar Fecha
                    </h2>
                    <div class="calendario">
                        <!-- Aquí irá el componente de calendario -->
                        <div id="calendario"></div>
                    </div>
                </div>

                <form id="reservaForm" class="reserva-form">
                    <h2 class="form-title">
                        <i class="fas fa-bookmark"></i>
                        Realizar Reserva
                    </h2>

                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="text" id="fecha" name="fecha" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="espacio">Espacio a Reservar</label>
                        <select id="espacio" name="espacio" class="form-control" required>
                            <option value="">Seleccione un espacio</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="width:100%;">
                            <label for="horario">Horario</label>
                            <select id="horario" name="horario" class="form-control" required>
                                <option value="">Seleccionar horario</option>
                                <?php foreach($HORARIOS as $h) {
                                    echo "<option value=\"" . htmlspecialchars($h) . "\">" . htmlspecialchars($h) . "</option>\n";
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Confirmar Reserva
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Vista de disponibilidad -->
            <div class="disponibilidad-container">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i>
                    Disponibilidad del Día
                </h2>

                <div class="tabla-horarios-container">
                    <table class="tabla-horarios" id="tabla-reservas">
                        <thead>
                            <tr>
                                <th class="header-espacios">Espacios</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php
    // pasar horarios a JS
    $HORARIOS = function_exists('obtenerHorarios') ? obtenerHorarios() : [
        '08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'
    ];
    $HORARIOS_TURNOS = function_exists('obtenerHorariosPorTurno') ? obtenerHorariosPorTurno() : [];
    ?>
    <script>
        const HORARIOS = <?php echo json_encode($HORARIOS, JSON_UNESCAPED_UNICODE); ?>;
        const HORARIOS_TURNOS = <?php echo json_encode($HORARIOS_TURNOS, JSON_UNESCAPED_UNICODE); ?>;
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página cargada');
            
            // Inicializar Flatpickr para el calendario
            const fechaInput = flatpickr("#fecha", {
                locale: "es",
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates) {
                    console.log('Fecha seleccionada:', selectedDates[0]);
                    if (selectedDates.length > 0) {
                        cargarDisponibilidad(selectedDates[0]);
                        const horario = document.getElementById('horario').value;
                        if (horario) {
                            cargarEspacios(selectedDates[0].toISOString().split('T')[0], horario);
                        }
                    }
                }
            });

            // Poblar select de horarios con optgroups por turno (mañana / tarde / noche)
            (function poblarHorarios() {
                const horarioSelect = document.getElementById('horario');
                if (!horarioSelect) return;
                if (typeof HORARIOS_TURNOS === 'undefined' || !HORARIOS_TURNOS) return;
                horarioSelect.innerHTML = '<option value="">Seleccionar horario</option>';
                const labels = { 'manana': 'Mañana', 'tarde': 'Tarde', 'noche': 'Noche' };
                ['manana','tarde','noche'].forEach(turno => {
                    const lista = HORARIOS_TURNOS[turno];
                    if (!lista || !Array.isArray(lista) || lista.length === 0) return;
                    const og = document.createElement('optgroup');
                    og.label = labels[turno] || turno;
                    lista.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h;
                        opt.textContent = h;
                        og.appendChild(opt);
                    });
                    horarioSelect.appendChild(og);
                });
            })();

            // Event listener para cambios en el horario
            document.getElementById('horario').addEventListener('change', function() {
                const fecha = fechaInput.selectedDates.length > 0 ? fechaInput.selectedDates[0].toISOString().split('T')[0] : null;
                const horario = this.value;
                if (fecha && horario) {
                    cargarEspacios(fecha, horario);
                }
            });

            // Cargar espacios disponibles
            function cargarEspacios(fecha = null, horario = null) {
                console.log('Cargando espacios...', {fecha, horario});

                const formData = new FormData();
                formData.append('accion', 'obtenerEspacios');
                if (fecha) formData.append('fecha', fecha);
                if (horario) formData.append('horario', horario);

                fetch('../PHP/procesarReserva.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Respuesta recibida:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    if (data.success) {
                        const select = document.getElementById('espacio');
                        select.innerHTML = '<option value="">Seleccione un espacio</option>';
                        // store global espacios for grid rendering
                        window.ESPACIOS = data.data || [];
                        data.data.forEach(espacio => {
                            select.innerHTML += `<option value="${espacio.id_espacio}">${espacio.nombre} (${espacio.tipo_espacio})</option>`;
                        });
                        // build spaces grid columns for availability
                        buildSpacesGrid();
                        // cargar disponibilidad para la fecha actual
                        if (fechaInput.selectedDates.length > 0) {
                            cargarDisponibilidad(fechaInput.selectedDates[0]);
                        }
                    } else {
                        console.error('Error en la respuesta:', data.message);
                        alert('Error al cargar los espacios: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error al cargar espacios:', error);
                    alert('Error al cargar los espacios. Por favor, intente de nuevo.');
                });
            }

            function buildSpacesGrid() {
                const espacios = window.ESPACIOS || [];
                const table = document.getElementById('tabla-reservas');
                const thead = table.querySelector('thead tr');
                const tbody = table.querySelector('tbody');

                // Clear existing content except the first th
                while (thead.children.length > 1) {
                    thead.removeChild(thead.lastChild);
                }
                tbody.innerHTML = '';

                const horarios = Array.isArray(HORARIOS) && HORARIOS.length ? HORARIOS : ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'];

                // Add horario headers
                horarios.forEach(h => {
                    const th = document.createElement('th');
                    th.textContent = h;
                    th.className = 'celda-horario';
                    thead.appendChild(th);
                });

                // Add rows for each espacio
                espacios.forEach(espacio => {
                    const tr = document.createElement('tr');
                    // First cell: space name
                    const tdName = document.createElement('td');
                    tdName.className = 'celda-espacio';
                    tdName.textContent = espacio.nombre;
                    tr.appendChild(tdName);
                    // Then cells for each horario
                    horarios.forEach(h => {
                        const td = document.createElement('td');
                        td.className = 'celda-horario disponible';
                        td.dataset.espacio = espacio.id_espacio;
                        td.dataset.horario = h;
                        tr.appendChild(td);
                    });
                    tbody.appendChild(tr);
                });
            }

            // Cargar disponibilidad del día
            function cargarDisponibilidad(fecha) {
                const formattedFecha = fecha.toISOString().split('T')[0];
                // ensure grid structure exists for current espacios
                if (typeof buildSpacesGrid === 'function') buildSpacesGrid();
                fetch('../PHP/procesarReserva.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `accion=obtenerReservasDia&fecha=${formattedFecha}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarDisponibilidad(data.data);
                    }
                })
                .catch(error => console.error('Error:', error));
            }

            // Mostrar disponibilidad en la tabla (recibe objeto {reservas:[], clases:[]})
            function mostrarDisponibilidad(payload) {
                const table = document.getElementById('tabla-reservas');
                const tbody = table.querySelector('tbody');

                // Reset all cells to available
                const allCells = tbody.querySelectorAll('td.celda-horario');
                allCells.forEach(cell => {
                    cell.className = 'celda-horario disponible';
                    cell.innerHTML = '';
                });

                const horarios = Array.isArray(HORARIOS) && HORARIOS.length ? HORARIOS : ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'];

                // helpers
                function indexOfHoraByStart(hora) {
                    if (!hora) return -1;
                    // hora puede ser '08:40' o '08:40 - 09:25'
                    const start = hora.split('-')[0].trim();
                    return horarios.findIndex(h => h.indexOf(start) === 0 || h.startsWith(start));
                }

                const reservas = (payload && payload.reservas) ? payload.reservas : [];
                const clases = (payload && payload.clases) ? payload.clases : [];

                // Marcar reservas (usuario)
                reservas.forEach(reserva => {
                    // extraer inicio/fin
                    let inicioStr = reserva.fecha_inicio || '';
                    let finStr = reserva.fecha_fin || '';
                    if (inicioStr.indexOf('-') !== -1) {
                        inicioStr = inicioStr.split('-')[0].trim();
                    }
                    if (finStr.indexOf('-') !== -1) {
                        finStr = finStr.split('-')[0].trim();
                    }

                    let sIdx = indexOfHoraByStart(inicioStr);
                    let eIdx = indexOfHoraByStart(finStr);
                    if (sIdx < 0) sIdx = 0;
                    if (eIdx < 0) eIdx = Math.min(horarios.length - 1, sIdx + 1);

                    // For each affected time slot
                    for (let idx = sIdx; idx <= eIdx; idx++) {
                        const horario = horarios[idx];
                        const cell = tbody.querySelector(`td[data-espacio="${reserva.id_espacio}"][data-horario="${horario}"]`);
                        if (cell) {
                            cell.className = 'celda-horario ocupado';
                            cell.innerHTML = `
                                <div class="reserva-info">
                                    <strong>${escapeHtml(reserva.nombre_usuario || '')} ${escapeHtml(reserva.apellido_usuario || '')}</strong>
                                </div>
                            `;
                        }
                    }
                });

                // Marcar clases programadas (asignadas en asocia)
                clases.forEach(clase => {
                    let horario = clase.horario || '';
                    let inicioStr = horario.indexOf('-') !== -1 ? horario.split('-')[0].trim() : horario.trim();
                    let finStr = horario.indexOf('-') !== -1 ? horario.split('-')[1].trim() : inicioStr;

                    let sIdx = indexOfHoraByStart(inicioStr);
                    let eIdx = indexOfHoraByStart(finStr);
                    if (sIdx < 0) sIdx = 0;
                    if (eIdx < 0) eIdx = Math.min(horarios.length - 1, sIdx + 1);

                    // For each affected time slot
                    for (let idx = sIdx; idx <= eIdx; idx++) {
                        const horarioSlot = horarios[idx];
                        const cell = tbody.querySelector(`td[data-espacio="${clase.id_espacio}"][data-horario="${horarioSlot}"]`);
                        if (cell) {
                            cell.className = 'celda-horario ocupado';
                            cell.innerHTML = `
                                <div class="reserva-info">
                                    <strong>${escapeHtml(clase.nombre_asignatura || '')}</strong>
                                    <span>${escapeHtml(clase.nombre_profesor || '')}</span>
                                </div>
                            `;
                        }
                    }
                });
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>"'`]/g, function (s) {
                    return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'})[s];
                });
            }

            // Manejar envío del formulario
            document.getElementById('reservaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('accion', 'crearReserva');
                // si usamos single select 'horario', enviar ese valor como hora_inicio y hora_fin para compatibilidad
                const horario = document.getElementById('horario') ? document.getElementById('horario').value : '';
                if (horario) {
                    formData.set('hora_inicio', horario);
                    formData.set('hora_fin', horario);
                }

                fetch('../PHP/procesarReserva.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reserva creada exitosamente');
                        this.reset();
                        if (fechaInput.selectedDates.length > 0) {
                            cargarDisponibilidad(fechaInput.selectedDates[0]);
                        }
                    } else {
                        alert(data.message || 'Error al crear la reserva');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
            });

            // Cargar espacios al inicio
            cargarEspacios();
        });
    </script>
    <script src="../JS/reservasGrid.js"></script>
</body>
</html>