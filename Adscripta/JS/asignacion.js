document.addEventListener('DOMContentLoaded', function () {
  function obtenerValorSeleccionado(elemento, placeholder = '—') {
    if (!elemento) return placeholder;
    if (elemento.multiple) {
      const seleccionados = Array.from(elemento.selectedOptions).map(op => op.textContent.trim());
      return seleccionados.length ? seleccionados.join(', ') : placeholder;
    }
    return (elemento.value && elemento.options[elemento.selectedIndex])
      ? elemento.options[elemento.selectedIndex].textContent.trim()
      : placeholder;
  }

  const campoGrupo = document.getElementById('campo-grupo');
  const campoAsignatura = document.getElementById('campo-asignatura');
  const campoProfesor = document.getElementById('campo-profesor');
  const campoDia = document.getElementById('campo-dia');
  const campoHoras = document.getElementById('campo-horas');
  const campoEspacio = document.getElementById('campo-espacio');
  const campoTurno = document.getElementById('campo-turno');
  const resultadoAsignacion = document.getElementById('resultado-asignacion');

  function actualizarConfirmacion() {
    document.querySelector('.var-asignatura').textContent = obtenerValorSeleccionado(campoAsignatura);
    document.querySelector('.var-profesor').textContent = obtenerValorSeleccionado(campoProfesor);
    document.querySelector('.var-grupo').textContent = obtenerValorSeleccionado(campoGrupo);
    document.querySelector('.var-dia').textContent = obtenerValorSeleccionado(campoDia);
    document.querySelector('.var-horas').textContent = obtenerValorSeleccionado(campoHoras);
    document.querySelector('.var-espacio').textContent = obtenerValorSeleccionado(campoEspacio);
    document.querySelector('.var-turno').textContent = obtenerValorSeleccionado(campoTurno);
  }

  [campoGrupo, campoAsignatura, campoProfesor, campoDia, campoHoras, campoEspacio, campoTurno].forEach(function (el) {
    if (el) el.addEventListener('change', actualizarConfirmacion);
  });

  // Enviar formulario por AJAX
  document.getElementById('formulario-asignacion').addEventListener('submit', function (e) {
    e.preventDefault();
    actualizarConfirmacion();
    const formulario = e.target;
    const datosFormulario = new FormData(formulario);

    fetch('../PHP/asignacionProf.php', {
      method: 'POST',
      body: datosFormulario
    })
    .then(res => res.json())
    .then(datos => {
      resultadoAsignacion.textContent = datos.message;
    })
    .catch(() => {
      resultadoAsignacion.textContent = 'Error al guardar la asignación.';
    });
  });

  // Limpiar confirmación y resultado al resetear
  document.getElementById('formulario-asignacion').addEventListener('reset', function () {
    setTimeout(() => {
      actualizarConfirmacion();
      resultadoAsignacion.textContent = '';
    }, 50);
  });

  // Inicializar confirmación
  actualizarConfirmacion();
});
