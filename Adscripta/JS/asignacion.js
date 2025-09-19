document.addEventListener('DOMContentLoaded', function () {
    // Elementos para resumen visual
    const varAsignatura = document.querySelector('.var-asignatura span');
    const varProfesor = document.querySelector('.var-profesor span');
    const varGrupo = document.querySelector('.var-grupo span');
    const varDia = document.querySelector('.var-dia span');
    const varHoras = document.querySelector('.var-horas span');
    const varEspacio = document.querySelector('.var-espacio span');
    // Elimina la línea de turno
    // const varTurno = document.querySelector('.var-turno span');

    // Selects
    const selectAsignatura = document.getElementById('campo-asignatura');
    const selectProfesor = document.getElementById('campo-profesor');
    const selectGrupo = document.getElementById('campo-grupo');
    const selectDia = document.getElementById('campo-dia');
    const selectHoras = document.getElementById('campo-horas');
    const selectEspacio = document.getElementById('campo-espacio');
    // Elimina la línea de turno
    // const selectTurno = document.getElementById('campo-turno');

    function actualizarResumen() {
        if (varAsignatura) varAsignatura.textContent = selectAsignatura.selectedOptions[0]?.textContent || '…';
        if (varProfesor) varProfesor.textContent = selectProfesor.selectedOptions[0]?.textContent || '…';
        if (varGrupo) varGrupo.textContent = selectGrupo.selectedOptions[0]?.textContent || '…';
        if (varDia) varDia.textContent = selectDia.selectedOptions[0]?.textContent || '…';
        if (varEspacio) varEspacio.textContent = selectEspacio.selectedOptions[0]?.textContent || '…';
        // Elimina la línea de turno
        // if (varTurno) varTurno.textContent = selectTurno.selectedOptions[0]?.textContent || '…';
        if (varHoras) {
            const horas = Array.from(selectHoras.selectedOptions).map(opt => opt.textContent);
            varHoras.textContent = horas.length ? horas.join(', ') : '…';
        }
    }

    [selectAsignatura, selectProfesor, selectGrupo, selectDia, selectHoras, selectEspacio /*, selectTurno*/].forEach(el => {
        if (el) el.addEventListener('change', actualizarResumen);
    });

    actualizarResumen();
});