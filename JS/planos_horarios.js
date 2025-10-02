
document.addEventListener('DOMContentLoaded', function() {
    // --- DATOS PRECARGADOS ---
    const diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
    const bloques = [1,2,3,4,5,6,7,8];
    // Nombres de espacios sin tildes, igual que en la base de datos
    const salonesPorPiso = {
        0: ['Aula 1', 'Laboratorio de Robotica', 'Laboratorio de Quimica', 'Laboratorio de Electronica', 'Zoom', 'Taller'],
        1: ['Aula 2', 'Salon 1', 'Salon 2', 'Laboratorio de Fisica'],
        2: ['Aula 3', 'salon 3', 'salon 4', 'salon 5']
    };

    // --- ELEMENTOS DEL DOM ---
    const selectorDia = document.getElementById('selector-dia');
    const botonesPiso = document.querySelectorAll('.btn-piso');
    const contenedorTablas = document.getElementById('contenedor-tablas-horarios');
    const imagenPlano = document.getElementById('imagen-plano');

    let pisoActual = '0';
    let diaActual = 'lunes';

    // Normaliza nombres de espacios para coincidir con las claves del JSON
    // Normaliza nombres quitando tildes y caracteres especiales
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

    // Genera el grid de horarios para un turno
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

    // Renderiza la estructura de los tres turnos
    function renderizarEstructuraHorarios(piso) {
        const salones = salonesPorPiso[parseInt(piso)];
        contenedorTablas.innerHTML =
            crearGridTurno('manana', salones) +
            crearGridTurno('tarde', salones) +
            crearGridTurno('noche', salones);
    }

    // Llama al endpoint y llena las celdas
    function cargarHorarios(dia, piso) {
        renderizarEstructuraHorarios(piso);
        fetch(`/PHP/planosHorarios.php?dia=${dia}`)
            .then(res => res.json())
            .then(data => {
                console.log('Datos recibidos del backend:', data); // DEPURACIÓN
                if (!data || !data[piso]) return;
                document.querySelectorAll('.horario-celda').forEach(cell => cell.innerHTML = '');
                ['manana','tarde','noche'].forEach(turno => {
                    const datosTurno = data[piso][turno] || {};
                    Object.keys(datosTurno).forEach(salon => {
                        Object.keys(datosTurno[salon]).forEach(bloque => {
                            const celda = document.querySelector(`.horario-celda[data-turno="${turno}"][data-salon="${salon}"][data-bloque="${bloque}"]`);
                            if (celda) {
                                const asigns = datosTurno[salon][bloque];
                                celda.innerHTML = asigns.map(a => `<div class='asignatura'><strong>${a.materia}</strong><br>${a.profesor}</div>`).join('');
                            }
                        });
                    });
                });
            })
            .catch(() => {
                contenedorTablas.innerHTML = '<p class="error">No se pudieron cargar los horarios.</p>';
            });
    }

    // Cambia la imagen del plano según el piso
    function actualizarPlano(piso) {
        const planos = {
            '0': '/Images/PlantaBaja.jpeg',
            '1': '/Images/Piso1y2.jpeg',
            '2': '/Images/Piso1y2.jpeg'
        };
        if (imagenPlano && planos[piso]) {
            imagenPlano.src = planos[piso];
            imagenPlano.alt = `Plano del piso ${piso}`;
        }
    }

    // Eventos
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

    // Inicialización
    actualizarPlano(pisoActual);
    cargarHorarios(diaActual, pisoActual);
});