document.addEventListener('DOMContentLoaded', function () {
    const turnoSelect = document.getElementById('hor-turno');
    const horaSelect = document.getElementById('hor-hora');

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

    // Actualiza los bloques de horario según el turno seleccionado
    turnoSelect.addEventListener('change', function () {
        horaSelect.innerHTML = '<option value="">Selecciona un bloque horario</option>';
        const turno = turnoSelect.value;
        if (bloques[turno]) {
            bloques[turno].forEach(function (bloque, idx) {
                horaSelect.innerHTML += `<option value="${bloque}">${bloque}</option>`;
            });
        }
    });

    // Si el turno ya está seleccionado al cargar la página, mostrar los bloques
    if (turnoSelect.value && bloques[turnoSelect.value]) {
        horaSelect.innerHTML = '<option value="">Selecciona un bloque horario</option>';
        bloques[turnoSelect.value].forEach(function (bloque, idx) {
            horaSelect.innerHTML += `<option value="${bloque}">${bloque}</option>`;
        });
    }
});