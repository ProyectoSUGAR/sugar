document.addEventListener('DOMContentLoaded', function () {
  function $id(id) { return document.getElementById(id); }

  // Enlaces a elementos del DOM
  const tipo = $id('grupo-tipo');
  const nombre = $id('grupo-nombre');
  const anio = $id('grupo-anio');
  const grupo = $id('grupo-grupo');
  const horas = $id('grupo-horas');
  const guardarBtn = $id('sugarads-guardar-grupo');
  const confirmarBtn = $id('sugarads-confirmar-grupo');
  const cancelarBtn = $id('sugarads-cancelar-grupo');
  const cancelarConfirmacionBtn = $id('sugarads-cancelar-confirmacion-grupo');
  const form = $id('sugarads-form-grupo');
  const resultadoCanvas = $id('sugarads-resultado-canvas');

  // Variables para resumen de confirmación
  const varTipo = document.querySelector('.sugarads-var-tipo');
  const varNombre = document.querySelector('.sugarads-var-nombre');
  const varAnio = document.querySelector('.sugarads-var-anio');
  const varGrupo = document.querySelector('.sugarads-var-grupo');
  const varHoras = document.querySelector('.sugarads-var-horas');

  function actualizarConfirmacion() {
    varTipo.textContent = tipo.value || '—';
    varNombre.textContent = nombre.value || '—';
    varAnio.textContent = anio.value || '—';
    varGrupo.textContent = grupo.value || '—';
    varHoras.textContent = horas.value || '—';
  }

  [tipo, nombre, anio, grupo, horas].forEach(el => {
    el.addEventListener('input', actualizarConfirmacion);
  });

  // Mostrar confirmación al guardar
  guardarBtn.addEventListener('click', function () {
    actualizarConfirmacion();
    document.querySelector('.sugarads-confirm-card').style.display = 'block';
  });

  // Cancelar confirmación
  cancelarConfirmacionBtn.addEventListener('click', function () {
    document.querySelector('.sugarads-confirm-card').style.display = 'none';
  });

  // Confirmar y enviar datos
  confirmarBtn.addEventListener('click', function () {
    const datos = {
      tipo: tipo.value,
      nombre: nombre.value,
      anio: anio.value,
      grupo: grupo.value,
      horas_semanales: horas.value
    };
    fetch('/Adscripta/PHP/ingresoGrupo.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(data => {
      resultadoCanvas.textContent = data.message;
      if (data.status === 'success') form.reset();
      document.querySelector('.sugarads-confirm-card').style.display = 'none';
      actualizarConfirmacion();
    })
    .catch(() => {
      resultadoCanvas.textContent = 'Error al guardar el grupo.';
      document.querySelector('.sugarads-confirm-card').style.display = 'none';
    });
  });

  //cacnelar y resetear formulario
  cancelarBtn.addEventListener('click', function () {
    form.reset();
    actualizarConfirmacion();
    resultadoCanvas.textContent = '';
  });

  // inicializar confirmación
  actualizarConfirmacion();
  document.querySelector('.sugarads-confirm-card').style.display = 'none';
});