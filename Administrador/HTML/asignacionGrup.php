<?php
include '../../HEADERS/headerAA.php';
require_once("../../PHP/conexion.php");
?>
 <body class="agp-body">
<main class="agp-main">
  <section class="agp-section agp-titulo">
    <h1 class="agp-title">Ingreso de grupos</h1>
  </section>
  <section class="agp-grid">
    <div class="agp-col-left agp-card">
      <form id="agp-form-grupo" class="agp-form form-constrain" autocomplete="off" onsubmit="return false;">
        <div class="agp-field">
          <label for="agp-grupo-tipo" class="agp-label">Tipo</label>
          <select id="agp-grupo-tipo" name="tipo" class="agp-select" required>
            <option value="">Selecciona un tipo</option>
            <option value="Bachillerato">Bachillerato</option>
            <option value="Terciario">Terciario</option>
          </select>
        </div>
        <div class="agp-field">
          <label for="agp-grupo-nombre" class="agp-label">Nombre</label>
         <input type="text" id="agp-grupo-nombre" name="nombre" class="agp-input" required placeholder="Ejemplo: MC" maxlength="3" pattern="[A-Za-z]{1,3}">
        </div>
        <div class="agp-field">
          <label for="agp-grupo-anio" class="agp-label">Año</label>
         <input type="number" id="agp-grupo-anio" name="anio" class="agp-input" min="1" max="6" required placeholder="Ejemplo: 3">
        </div>
        <div class="agp-field">
          <label for="agp-grupo-horas" class="agp-label">Horas semanales</label>
         <input type="number" id="agp-grupo-horas" name="horas_semanales" class="agp-input" min="1" max="40" required placeholder="Ejemplo: 36">
        </div>
        <div class="agp-actions">
          <button id="agp-guardar-grupo" class="btn-primario" type="button">Guardar</button>
          <button id="agp-cancelar-grupo" class="btn-secundario" type="button">Cancelar</button>
        </div>
      </form>
    </div>
    <article class="agp-col-right agp-card">
      <div class="agp-confirm-card" role="region" aria-live="polite" aria-label="Confirmación de grupo">
        <p class="agp-confirm-text">
          Se ingresará el grupo
          <strong class="agp-var agp-var-nombre">(<span aria-hidden="true">…</span>)</strong>
          de tipo <strong class="agp-var agp-var-tipo">(<span aria-hidden="true">…</span>)</strong>,
          año <strong class="agp-var agp-var-anio">(<span aria-hidden="true">…</span>)</strong>,
          con <strong class="agp-var agp-var-horas">(<span aria-hidden="true">…</span>)</strong> horas semanales.
          <br>¿Desea realizar esta acción?
        </p>
        <div class="agp-confirm-actions" role="group" aria-label="Acciones de confirmación">
          <button id="agp-confirmar-grupo" class="btn-primario" type="button">Confirmar</button>
          <button id="agp-cancelar-confirmacion-grupo" class="btn-secundario" type="button">Cancelar</button>
        </div>
      </div>
      <section class="agp-resultado" aria-label="Resultado de ingreso de grupo">
        <h2 class="agp-result-title">Resultado</h2>
        <div id="agp-resultado-canvas" class="agp-canvas" aria-live="polite"></div>
      </section>
    </article>
  </section>
</main>
<script src="../../Administrador/JS/ingresoGrupo.js" defer></script>
 </body>
