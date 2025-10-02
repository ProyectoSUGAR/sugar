<?php
include '../../HEADERS/headerAA.php';
require_once("../../PHP/conexion.php");
?>
 <body class="bodyregidat">
<main class="sugarads-main">
  <section class="sugarads-section titulo">
    <h1 class="sugarads-title">Ingreso de grupos</h1>
  </section>
  <section class="sugarads-grid">
    <div class="sugarads-col-left">
      <!-- Formulario de ingreso de grupo -->
      <form id="sugarads-form-grupo" class="sugarads-form" autocomplete="off" onsubmit="return false;" style="max-width: 400px; margin: 0 auto;">
        <div class="sugarads-field">
          <label for="grupo-tipo" class="sugarads-label">Tipo</label>
          <select id="grupo-tipo" name="tipo" class="input-text sugarads-input" required>
            <option value="">Selecciona un tipo</option>
            <option value="Bachillerato">Bachillerato</option>
            <option value="Terciario">Terciario</option>
          </select>
        </div>
        <div class="sugarads-field">
          <label for="grupo-nombre" class="sugarads-label">Nombre</label>
          <input type="text" id="grupo-nombre" name="nombre" class="input-text sugarads-input" required placeholder="Ejemplo: MC">
        </div>
        <div class="sugarads-field">
          <label for="grupo-anio" class="sugarads-label">Año</label>
          <input type="number" id="grupo-anio" name="anio" class="input-text sugarads-input" min="1" max="6" required placeholder="Ejemplo: 3">
        </div>
        <div class="sugarads-field">
          <label for="grupo-grupo" class="sugarads-label">Grupo</label>
          <input type="text" id="grupo-grupo" name="grupo" class="input-text sugarads-input" required placeholder="Ejemplo: 3MC">
        </div>
        <div class="sugarads-field">
          <label for="grupo-horas" class="sugarads-label">Horas semanales</label>
          <input type="number" id="grupo-horas" name="horas_semanales" class="input-text sugarads-input" min="1" max="50" required placeholder="Ejemplo: 36">
        </div>
        <div class="sugarads-field" style="text-align:center;">
          <button id="sugarads-guardar-grupo" class="sugarads-btn sugarads-btn-guardar" type="button">Guardar</button>
          <button id="sugarads-cancelar-grupo" class="sugarads-btn sugarads-btn-cancelar" type="button">Cancelar</button>
        </div>
      </form>
    </div>
    <article class="sugarads-col-right">
      <div class="sugarads-confirm-card" role="region" aria-live="polite" aria-label="Confirmación de grupo">
        <p class="sugarads-confirm-text">
          Se ingresará el grupo
          <strong class="sugarads-var sugarads-var-nombre">(<span aria-hidden="true">…</span>)</strong>
          de tipo <strong class="sugarads-var sugarads-var-tipo">(<span aria-hidden="true">…</span>)</strong>,
          año <strong class="sugarads-var sugarads-var-anio">(<span aria-hidden="true">…</span>)</strong>,
          grupo <strong class="sugarads-var sugarads-var-grupo">(<span aria-hidden="true">…</span>)</strong>,
          con <strong class="sugarads-var sugarads-var-horas">(<span aria-hidden="true">…</span>)</strong> horas semanales.
          <br>¿Desea realizar esta acción?
        </p>
        <div class="sugarads-confirm-actions" role="group" aria-label="Acciones de confirmación">
          <button id="sugarads-confirmar-grupo" class="sugarads-btn sugarads-btn-guardar" type="button">Confirmar</button>
          <button id="sugarads-cancelar-confirmacion-grupo" class="sugarads-btn sugarads-btn-cancelar" type="button">Cancelar</button>
        </div>
      </div>
      <section class="sugarads-resultado" aria-label="Resultado de ingreso de grupo">
        <h2 class="sugarads-resultado-title">Resultado</h2>
        <div id="sugarads-resultado-canvas" class="sugarads-canvas" aria-live="polite"></div>
      </section>
    </article>
  </section>
</main>
<script src="/SUGAR/Adscripta/JS/ingresoGrupo.js" defer></script>
</body>