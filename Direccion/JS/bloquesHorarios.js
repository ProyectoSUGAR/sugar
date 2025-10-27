document.addEventListener('DOMContentLoaded', function () {
    const turnoSelectors = ['hor-turno', 'hor-turno-editar'].map(id => document.getElementById(id));
    const horaSelectors = ['hor-hora', 'hor-hora-editar'].map(id => document.getElementById(id));
    console.log('Inicializando selectores de horarios');
    console.log('Turnos encontrados:', turnoSelectors.map(s => s ? s.id : 'no encontrado'));
    console.log('Horas encontradas:', horaSelectors.map(s => s ? s.id : 'no encontrado'));
    const bloques = {
        'manana': [
            "07:00 - 07:45",
            "07:50 - 08:35",
            "08:40 - 09:25",
            "09:30 - 10:15",
            "10:20 - 11:05",
            "11:10 - 11:55",
            "12:00 - 12:45",
            "12:50 - 13:35",
            "13:40 - 14:25"
        ],
        'tarde': [
            "12:50 - 13:35",
            "13:40 - 14:25",
            "14:30 - 15:15",
            "15:20 - 16:05",
            "16:10 - 16:55",
            "17:00 - 17:45",
            "17:50 - 18:35",
            "18:40 - 19:35"
        ],
        'noche': [
            "18:10 - 18:55",
            "19:00 - 19:45",
            "19:50 - 20:35",
            "20:40 - 21:25",
            "21:30 - 22:15",
            "22:20 - 23:05",
            "23:10 - 23:11"
        ]
    };
    function actualizarBloques(turnoSelect, horaSelect) {
        if (!turnoSelect || !horaSelect) {
            console.warn('Selector no encontrado:', turnoSelect ? 'horaSelect falta' : 'turnoSelect falta');
            return;
        }
        console.log('Actualizando bloques para:', turnoSelect.id);
        const valoresActuales = Array.from(horaSelect.selectedOptions || []).map(o => o.value);
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
    turnoSelectors.forEach((turnoSelect, index) => {
        if (turnoSelect) {
            console.log('Configurando evento para:', turnoSelect.id);
            turnoSelect.addEventListener('change', function() {
                actualizarBloques(turnoSelect, horaSelectors[index]);
            });
            if (turnoSelect.value) {
                console.log('Turno preseleccionado:', turnoSelect.value);
                actualizarBloques(turnoSelect, horaSelectors[index]);
            }
        }
    });
    ['form-crear', 'form-editar'].forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            console.log('Configurando validación para:', formId);
            form.addEventListener('submit', function(e) {
                const turnoSelect = this.querySelector('[name="turno"]');
                const horaSelect = this.querySelector('[name="hora[]"]');
                if (!turnoSelect.value) {
                    e.preventDefault();
                    alert('Por favor, selecciona un turno');
                    return false;
                }
                const seleccionadas = horaSelect && Array.from(horaSelect.selectedOptions || []).filter(o => o.value);
                if (!seleccionadas || seleccionadas.length === 0) {
                    e.preventDefault();
                    alert('Por favor, selecciona al menos un bloque horario');
                    return false;
                }
            });
        }
    });
});