<?php
include '../../PHP/header.php';
require("../../PHP/conexion.php");
$conn = conectar_bd();
mysqli_close($conn);
?>
<main class="sugarads-main">
  <section class="sugarads-section titulo">
    <h1 class="sugarads-title">Ingreso de grupos</h1>
  </section>

  <section class="sugarads-grid">
    <aside class="sugarads-col-left" aria-labelledby="sugarads-form-labels">
      <form id="sugarads-form-grupo" class="sugarads-form" novalidate>
        <div class="sugarads-field">
          <label for="grupo-tipo" class="sugarads-label">Tipo</label>
          <input type="text" id="grupo-tipo" name="tipo" placeholder="Ej: Bachillerato, Terciario.">
        </div>
        <div class="sugarads-field">
          <label for="grupo-nombre" class="sugarads-label">Nombre</label>
          <input type="text" id="grupo-nombre" name="nombre" placeholder="Ej: 1A, 2B, 3B.">
        </div>
        <div class="sugarads-field">
          <label for="grupo-anio" class="sugarads-label">Año</label>
          <input type="number" id="grupo-anio" name="anio" min="1" max="1" placeholder="Ej: 1, 2, 3...">
        </div>
        <div class="sugarads-field">
          <label for="grupo-grupo" class="sugarads-label">Grupo</label>
          <input type="text" id="grupo-grupo" name="grupo" placeholder="Letra o código de grupo">
        </div>
        <div class="sugarads-field">
          <label for="grupo-horas" class="sugarads-label">Horas semanales</label>
          <input type="number" id="grupo-horas" name="horas_semanales" min="1" max="50" placeholder="Ej: 30">
        </div>
        <div class="sugarads-field">
          <button type="button" id="sugarads-guardar-grupo" class="sugarads-btn sugarads-btn-guardar">Guardar</button>
          <button type="reset" id="sugarads-cancelar-grupo" class="sugarads-btn sugarads-btn-cancelar">Cancelar</button>
        </div>
      </form>
    </aside>

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
<script src="../JS/ingresoGrupo.js" defer></script>