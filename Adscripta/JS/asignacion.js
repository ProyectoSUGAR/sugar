document.addEventListener('DOMContentLoaded', function () {
    // Elementos para resumen visual
    const varAsignatura = document.querySelector('.sugarads-var-asignatura span');
    const varProfesor = document.querySelector('.sugarads-var-profesor span');
    const varGrupo = document.querySelector('.sugarads-var-grupo span');
    const varDia = document.querySelector('.sugarads-var-dia span');
    const varHoras = document.querySelector('.sugarads-var-horas span');
    const varEspacio = document.querySelector('.sugarads-var-espacio span');

    // Selectores
    const selectAsignatura = document.getElementById('campo-asignatura');
    const selectProfesor = document.getElementById('campo-profesor');
    const selectGrupo = document.getElementById('campo-grupo');
    const selectDia = document.getElementById('campo-dia');
    const selectHoras = document.getElementById('campo-horas');
    const selectEspacio = document.getElementById('campo-espacio');

    function actualizarResumen() {
        if (varAsignatura) varAsignatura.textContent = selectAsignatura.selectedOptions[0]?.textContent || '—';
        if (varProfesor) varProfesor.textContent = selectProfesor.selectedOptions[0]?.textContent || '—';
        if (varGrupo) varGrupo.textContent = selectGrupo.selectedOptions[0]?.textContent || '—';
        if (varDia) varDia.textContent = selectDia.selectedOptions[0]?.textContent || '—';
        if (varHoras) {
            const horas = Array.from(selectHoras.selectedOptions).map(opt => opt.textContent);
            varHoras.textContent = horas.length ? horas.join(', ') : '—';
        }
    }

    [selectAsignatura, selectProfesor, selectGrupo, selectDia, selectHoras, selectEspacio].forEach(el => {
        if (el) el.addEventListener('change', actualizarResumen);
    });

    actualizarResumen();
});