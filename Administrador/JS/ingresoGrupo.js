document.addEventListener('DOMContentLoaded', function () {
  function $id(id) { return document.getElementById(id); }
  const tipo = $id('grupo-tipo');
  const nombre = $id('grupo-nombre');
  const anio = $id('grupo-anio');
  const horas = $id('grupo-horas');
  const guardarBtn = $id('sugarads-guardar-grupo');
  const confirmarBtn = $id('sugarads-confirmar-grupo');
  const cancelarBtn = $id('sugarads-cancelar-grupo');
  const cancelarConfirmacionBtn = $id('sugarads-cancelar-confirmacion-grupo');
  const form = $id('sugarads-form-grupo');
  const resultadoCanvas = $id('sugarads-resultado-canvas');
  const varTipo = document.querySelector('.sugarads-var-tipo');
  const varNombre = document.querySelector('.sugarads-var-nombre');
  const varAnio = document.querySelector('.sugarads-var-anio');
  const varHoras = document.querySelector('.sugarads-var-horas');
  const confirmCard = document.querySelector('.formasig1');
  if (confirmCard) {
    confirmCard.classList.add('sugarads-confirm-card');
  }
  function actualizarConfirmacion() {
    varTipo.textContent = tipo.value || '—';
    varNombre.textContent = nombre.value || '—';
    varAnio.textContent = anio.value || '—';
    varHoras.textContent = horas.value || '—';
  }
  [tipo, nombre, anio, horas].forEach(el => {
    el.addEventListener('input', actualizarConfirmacion);
  });
  guardarBtn.addEventListener('click', function () {
    actualizarConfirmacion();
    document.querySelector('.sugarads-confirm-card').style.display = 'block';
  });
  cancelarConfirmacionBtn.addEventListener('click', function () {
    document.querySelector('.sugarads-confirm-card').style.display = 'none';
  });
  confirmarBtn.addEventListener('click', function () {
    const nombreVal = nombre.value.trim();
    const anioVal = parseInt(anio.value, 10);
    const horasVal = parseInt(horas.value, 10);
    if (!/^[A-Za-z]{1,3}$/.test(nombreVal)) {
      resultadoCanvas.textContent = 'Nombre inválido: solo letras (1-3).';
      return;
    }
    if (!(anioVal >= 1 && anioVal <= 6)) {
      resultadoCanvas.textContent = 'Año inválido: debe estar entre 1 y 6.';
      return;
    }
    if (!(horasVal >= 1 && horasVal <= 40)) {
      resultadoCanvas.textContent = 'Horas inválidas: debe ser entre 1 y 40.';
      return;
    }
    const datos = {
      tipo: tipo.value,
      nombre: nombreVal,
      anio: anioVal,
      horas_semanales: horasVal
    };
  fetch('../PHP/ingresoGrupo.php', {
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
  cancelarBtn.addEventListener('click', function () {
    form.reset();
    actualizarConfirmacion();
    resultadoCanvas.textContent = '';
  });
  actualizarConfirmacion();
  document.querySelector('.sugarads-confirm-card').style.display = 'none';
});