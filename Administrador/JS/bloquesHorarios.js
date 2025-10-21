document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar todos los selectores de turno y hora (tanto para crear como para editar)
    const turnoSelectors = ['hor-turno', 'hor-turno-editar'].map(id => document.getElementById(id));
    const horaSelectors = ['hor-hora', 'hor-hora-editar'].map(id => document.getElementById(id));

    console.log('Inicializando selectores de horarios');
    console.log('Turnos encontrados:', turnoSelectors.map(s => s ? s.id : 'no encontrado'));
    console.log('Horas encontradas:', horaSelectors.map(s => s ? s.id : 'no encontrado'));

    // Bloques de horario por turno
    const bloques = {
        'manana': [
            "07:00 - 07:45",
            "07:50 - 08:35",
            "08:40 - 09:25",
            "09:30 - 10:15",
            "10:20 - 11:05",
            "11:10 - 11:55",
            "12:00 - 12:45",
            "12:50 - 13:35"
        ],
        'tarde': [
            "13:40 - 14:25",
            "14:30 - 15:15",
            "15:20 - 16:05",
            "16:10 - 16:55",
            "17:00 - 17:45",
            "17:50 - 18:35",
            "18:40 - 19:25"
        ],
        'noche': [
            "18:10 - 18:55",
            "19:00 - 19:45",
            "19:50 - 20:35",
            "20:40 - 21:25",
            "21:30 - 22:15",
            "22:20 - 23:05",
            "23:10 - 23:55"
        ]
    };

    // Función para actualizar los bloques de horario
    function actualizarBloques(turnoSelect, horaSelect) {
        if (!turnoSelect || !horaSelect) {
            console.warn('Selector no encontrado:', turnoSelect ? 'horaSelect falta' : 'turnoSelect falta');
            return;
        }

        console.log('Actualizando bloques para:', turnoSelect.id);
        
        // Guardar los valores actuales si existen (para multi-select)
        const valoresActuales = Array.from(horaSelect.selectedOptions || []).map(o => o.value);
        
        // Limpiar y agregar la opción por defecto
        horaSelect.innerHTML = '<option value="">Selecciona un bloque horario</option>';
        
        const turno = turnoSelect.value;
        if (bloques[turno]) {
            bloques[turno].forEach(function (bloque) {
                const option = document.createElement('option');
                option.value = bloque;
                option.textContent = bloque;
                if (valoresActuales.indexOf(bloque) !== -1) {
                    option.selected = true;
                }
                horaSelect.appendChild(option);
            });
            console.log(`Bloques cargados para turno ${turno}:`, bloques[turno].length);
        } else {
            console.warn('No se encontraron bloques para el turno:', turno);
        }
    }

    // Configurar los event listeners para ambos selectores de turno
    turnoSelectors.forEach((turnoSelect, index) => {
        if (turnoSelect) {
            console.log('Configurando evento para:', turnoSelect.id);
            
            // Actualizar los bloques cuando cambie el turno
            turnoSelect.addEventListener('change', function() {
                actualizarBloques(turnoSelect, horaSelectors[index]);
            });
            
            // Si ya hay un turno seleccionado, cargar los bloques correspondientes
            if (turnoSelect.value) {
                console.log('Turno preseleccionado:', turnoSelect.value);
                actualizarBloques(turnoSelect, horaSelectors[index]);
            }
        }
    });

    // Asignar turno automáticamente según el bloque horario seleccionado
    function turnoPorBloque(bloque) {
        if (["07:00 - 07:45","07:50 - 08:35","08:40 - 09:25","09:30 - 10:15","10:20 - 11:05","11:10 - 11:55","12:00 - 12:45","12:50 - 13:35"].includes(bloque)) return 'manana';
        if (["13:40 - 14:25","14:30 - 15:15","15:20 - 16:05","16:10 - 16:55","17:00 - 17:45","17:50 - 18:35","18:10 - 18:55","18:40 - 19:25"].includes(bloque)) return 'tarde';
        if (["19:00 - 19:45","19:50 - 20:35","20:40 - 21:25","21:30 - 22:15","22:20 - 23:05","23:10 - 23:55"].includes(bloque)) return 'noche';
        return '';
    }

    horaSelectors.forEach((horaSelect, idx) => {
        if (horaSelect) {
            horaSelect.addEventListener('change', function() {
                const turnoSelect = turnoSelectors[idx];
                // Si solo hay una opción seleccionada, asignar turno automáticamente
                const seleccionadas = Array.from(horaSelect.selectedOptions || []).filter(o => o.value);
                if (seleccionadas.length === 1) {
                    const bloque = seleccionadas[0].value;
                    const turnoAuto = turnoPorBloque(bloque);
                    if (turnoSelect && turnoAuto) {
                        turnoSelect.value = turnoAuto;
                        actualizarBloques(turnoSelect, horaSelect);
                    }
                }
            });
        }
    });

    // Validación del formulario
    ['form-crear', 'form-editar'].forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            console.log('Configurando validación para:', formId);
            form.addEventListener('submit', function(e) {
                const turnoSelect = this.querySelector('[name="turno"]');
                const horaSelect = this.querySelector('[name="hora[]"]');

                // Si solo hay una opción seleccionada, asignar turno automáticamente antes de enviar
                const seleccionadas = horaSelect && Array.from(horaSelect.selectedOptions || []).filter(o => o.value);
                if (seleccionadas && seleccionadas.length === 1) {
                    const bloque = seleccionadas[0].value;
                    const turnoAuto = turnoPorBloque(bloque);
                    if (turnoSelect && turnoAuto) {
                        turnoSelect.value = turnoAuto;
                        actualizarBloques(turnoSelect, horaSelect);
                    }
                }

                if (!turnoSelect.value) {
                    e.preventDefault();
                    alert('Por favor, selecciona un turno');
                    return false;
                }

                if (!seleccionadas || seleccionadas.length === 0) {
                    e.preventDefault();
                    alert('Por favor, selecciona al menos un bloque horario');
                    return false;
                }
            });
        }
    });
    // (no-op) el resto se maneja por los selectores cargados previamente
});