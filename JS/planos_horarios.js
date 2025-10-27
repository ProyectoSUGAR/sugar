document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .error-container {
            padding: 20px;
            text-align: center;
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            margin: 20px 0;
        }
        .error {
            color: #e53e3e;
            font-size: 1.1em;
            margin-bottom: 10px;
        }
        .error-details {
            color: #718096;
            margin-bottom: 15px;
        }
        .btn-reintentar {
            background: #4299e1;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-reintentar:hover {
            background: #3182ce;
        }
    `;
    document.head.appendChild(style);
    const diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
    const bloques = [1,2,3,4,5,6,7,8];
    const salonesPorPiso = {
        0: ['Aula 1', 'Laboratorio de Robotica', 'Laboratorio de Quimica', 'Laboratorio de Electronica', 'Zoom', 'Taller'],
        1: ['Aula 2', 'Salon 1', 'Salon 2', 'Laboratorio de Fisica'],
        2: ['Aula 3', 'salon 3', 'salon 4', 'salon 5']
    };
    const selectorDia = document.getElementById('selector-dia');
    const botonesPiso = document.querySelectorAll('.btn-piso');
    const contenedorTablas = document.getElementById('contenedor-tablas-horarios');
    const imagenPlano = document.getElementById('imagen-plano');
    let pisoActual = '0';
    let diaActual = 'lunes';
    function normalizarNombre(nombre) {
        return nombre
            .toLowerCase()
            .replace(/[áàäâ]/g, 'a')
            .replace(/[éèëê]/g, 'e')
            .replace(/[íìïî]/g, 'i')
            .replace(/[óòöô]/g, 'o')
            .replace(/[úùüû]/g, 'u')
            .replace(/ñ/g, 'n')
            .replace(/ç/g, 'c')
            .replace(/\s+/g, ' ')
            .trim();
    }
    function crearGridTurno(titulo, salones) {
        let html = '<div class="grid-header grid-salon">Espacio</div>';
        bloques.forEach(b => html += `<div class="grid-header grid-bloque">${b}</div>`);
        salones.forEach(salon => {
            html += `<div class="grid-cell grid-salon">${salon}</div>`;
            bloques.forEach(bloque => {
                html += `<div class="grid-cell horario-celda" data-turno="${titulo}" data-salon="${normalizarNombre(salon)}" data-bloque="${bloque}"></div>`;
            });
        });
        return `<div class="tabla-horario"><h3>${titulo.charAt(0).toUpperCase()+titulo.slice(1)}</h3><div class="grid-horarios">${html}</div></div>`;
    }
    function renderizarEstructuraHorarios(piso) {
        const salones = salonesPorPiso[parseInt(piso)];
        contenedorTablas.innerHTML =
            crearGridTurno('manana', salones) +
            crearGridTurno('tarde', salones) +
            crearGridTurno('noche', salones);
    }
    function cargarHorarios(dia, piso) {
        console.log(`Cargando horarios para día: ${dia}, piso: ${piso}`);
        renderizarEstructuraHorarios(piso);
        fetch(`../../PHP/planosHorarios.php?dia=${dia}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('Datos recibidos del backend:', data);
                if (!data) {
                    throw new Error('No se recibieron datos del servidor');
                }
                if (!data[piso]) {
                    console.warn(`No hay datos para el piso ${piso}`);
                    return;
                }
                document.querySelectorAll('.horario-celda').forEach(cell => cell.innerHTML = '');
                ['manana','tarde','noche'].forEach(turno => {
                    const datosTurno = data[piso][turno] || {};
                    Object.entries(datosTurno).forEach(([salon, bloques]) => {
                        Object.entries(bloques).forEach(([bloque, asignaturas]) => {
                            const selector = `.horario-celda[data-turno="${turno}"][data-salon="${salon}"][data-bloque="${bloque}"]`;
                            const celda = document.querySelector(selector);
                            if (celda) {
                                const contenidoCelda = asignaturas.map(a => {
                                    const grupo = a.grupo ? `<div class="grupo">${a.grupo}</div>` : '';
                                    return `<div class='asignatura'>
                                        <strong>${a.materia || 'Sin materia'}</strong><br>
                                        ${a.profesor || 'Sin profesor'}
                                        ${grupo}
                                     </div>`;
                                }).join('');
                                celda.innerHTML = contenidoCelda;
                            } else {
                                console.warn(`No se encontró la celda para: ${selector}`);
                            }
                        });
                    });
                });
            })
            .catch((error) => {
                console.error('Error al cargar los horarios:', error);
                contenedorTablas.innerHTML = `
                    <div class="error-container">
                        <p class="error">No se pudieron cargar los horarios.</p>
                        <p class="error-details">Por favor, intente nuevamente más tarde.</p>
                        <button onclick="cargarHorarios('${dia}', '${piso}')" class="btn-reintentar">
                            Reintentar
                        </button>
                    </div>`;
            });
    }
    function actualizarPlano(piso) {
        const planos = {
            '0': '../../Images/PlantaBaja.jpeg',
            '1': '../../Images/Piso1y2.jpeg',
            '2': '../../Images/Piso1y2.jpeg'
        };
        if (imagenPlano && planos[piso]) {
            imagenPlano.src = planos[piso];
            imagenPlano.alt = `Plano del piso ${piso}`;
        }
    }
    selectorDia.addEventListener('change', function(e) {
        diaActual = e.target.value;
        cargarHorarios(diaActual, pisoActual);
    });
    botonesPiso.forEach(btn => {
        btn.addEventListener('click', function() {
            botonesPiso.forEach(b => b.classList.remove('activo'));
            btn.classList.add('activo');
            pisoActual = btn.dataset.piso;
            actualizarPlano(pisoActual);
            cargarHorarios(diaActual, pisoActual);
        });
    });
    actualizarPlano(pisoActual);
    cargarHorarios(diaActual, pisoActual);
});
