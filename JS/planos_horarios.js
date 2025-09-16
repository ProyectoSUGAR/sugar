document.addEventListener('DOMContentLoaded', function() {
    const planos = [
        { src: "/Images/PlantaBaja.jpeg", alt: "Plano Planta baja" },
        { src: "/Images/Piso1y2.jpeg", alt: "Plano Piso 1" },
        { src: "/Images/Piso1y2.jpeg", alt: "Plano Piso 2" }
    ];

    const salonesPorPiso = [
        ["Aula 1", "Laboratorio de Electrónica", "Laboratorio de Química", "Laboratorio de Robótica", "Zoom", "Taller"],
        ["Aula 2", "Salón 1", "Salón 2", "Laboratorio de Física"],
        ["Aula 3", "Salón 3", "Salón 4", "Salón 5"]
    ];

    const bloques = ["1°", "2°", "3°", "4°", "5°", "6°", "7°", "8°"];

    const turnos = [
        { id: "manana", titulo: "Turno Mañana", bloques: bloques },
        { id: "tarde", titulo: "Turno Tarde", bloques: bloques },
        { id: "noche", titulo: "Turno Noche", bloques: bloques }
    ];

    const btns = document.querySelectorAll('.btn-piso');
    const imgPlano = document.getElementById('imagen-plano');
    const contenedorTablas = document.getElementById('contenedor-tablas-horarios');
    let datosHorarios = {};

    function crearTablasHorarios(piso) {
        contenedorTablas.innerHTML = "";
        turnos.forEach(turno => {
            const section = document.createElement('section');
            section.className = "turno";
            section.id = `turno-${turno.id}`;

            const h3 = document.createElement('h3');
            h3.textContent = turno.titulo;
            section.appendChild(h3);

            const table = document.createElement('table');
            table.className = "tabla-horarios";
            table.id = `tabla-${turno.id}`;

            const thead = document.createElement('thead');
            const trHead = document.createElement('tr');
            const thSalon = document.createElement('th');
            thSalon.textContent = "Espacio";
            trHead.appendChild(thSalon);

            turno.bloques.forEach(bloque => {
                const th = document.createElement('th');
                th.textContent = bloque;
                trHead.appendChild(th);
            });

            thead.appendChild(trHead);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            salonesPorPiso[piso].forEach((salon) => {
                const tr = document.createElement('tr');
                const tdSalon = document.createElement('td');
                tdSalon.textContent = salon;
                tr.appendChild(tdSalon);

                turno.bloques.forEach(() => {
                    const td = document.createElement('td');
                    td.innerHTML = "";
                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            section.appendChild(table);
            contenedorTablas.appendChild(section);
        });
    }

    function llenarHorarios(piso) {
        fetch('/PHP/planosHorarios.php')
            .then(res => res.json())
            .then(data => {
                datosHorarios = data;
                turnos.forEach(turno => {
                    const tabla = document.getElementById(`tabla-${turno.id}`);
                    if (!tabla) return;
                    const tbody = tabla.querySelector('tbody');
                    Array.from(tbody.rows).forEach((tr, idxSalon) => {
                        const nombreSalon = salonesPorPiso[piso][idxSalon];
                        Array.from(tr.cells).forEach((td, idxBloque) => {
                            if (idxBloque === 0) return;
                            td.innerHTML = "";
                            const bloqueNum = idxBloque; // 1-based
                            const info = data?.[piso]?.[turno.id]?.[nombreSalon]?.[bloqueNum];
                            if (info && info.materia) {
                                td.innerHTML = `<div class="horario-celda">
                                    <strong>${info.materia}</strong><br>
                                    <span style="font-size:0.9em">${info.profesor}</span>
                                </div>`;
                            }
                        });
                    });
                });
            });
    }

    let pisoActivo = 0;
    btns.forEach((btn, idx) => {
        if (btn.classList.contains('activo')) {
            pisoActivo = idx;
        }
    });
    imgPlano.src = planos[pisoActivo].src;
    imgPlano.alt = planos[pisoActivo].alt;
    crearTablasHorarios(pisoActivo);
    llenarHorarios(pisoActivo);

    btns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            btns.forEach(b => b.classList.remove('activo'));
            btn.classList.add('activo');
            imgPlano.src = planos[idx].src;
            imgPlano.alt = planos[idx].alt;
            crearTablasHorarios(idx);
            llenarHorarios(idx);
        });
    });
});