<?php
include '../../PHP/header.php';
require("../../PHP/conexion.php");

$conn = conectar_bd();

// Función para obtener opciones
function getOpciones($query, $conn) {
    $result = mysqli_query($conn, $query);
    $opciones = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $opciones .= '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
    }
    return $opciones;
}

// Grupos
$grupos = getOpciones("SELECT id_grupo as id, CONCAT(nombre, ' (Año ', anio, ')') as nombre FROM grupo", $conn);

// Asignaturas
$asignaturas = getOpciones("SELECT id_asignatura as id, nombre FROM asignatura", $conn);

// Profesores (solo por tipo_usuario)
$profesores = getOpciones("SELECT id_usuario as id, CONCAT(nombre, ' ', apellido) as nombre FROM usuario WHERE tipo_usuario = 'profesor'", $conn);

// Todos los espacios
$espacios = getOpciones("SELECT id_espacio as id, nombre FROM espacio", $conn);

mysqli_close($conn);
?>
<main class="sugarads-main">
  <section class="sugarads-section titulo">
    <h1 class="sugarads-title">Asignación de clases</h1>
  </section>

  <section class="sugarads-grid">
    <aside class="sugarads-col-left" aria-labelledby="sugarads-form-labels">
      <form id="formulario-asignacion" class="sugarads-form" novalidate>
        <div class="sugarads-field">
          <label for="campo-grupo" class="sugarads-label">Grupo</label>
          <select id="campo-grupo" name="grupo" aria-label="Grupo">
            <option value="">Seleccionar Grupo</option>
            <?php echo $listaGrupos; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <label for="campo-asignatura" class="sugarads-label">Asignatura</label>
          <select id="campo-asignatura" name="asignatura" aria-label="Asignatura">
            <option value="">Seleccionar Asignatura</option>
            <?php echo $listaAsignaturas; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <label for="campo-profesor" class="sugarads-label">Profesor</label>
          <select id="campo-profesor" name="profesor" aria-label="Profesor">
            <option value="">Seleccionar Profesor</option>
            <?php echo $listaProfesores ?: '<option value="">No hay profesores</option>'; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <label for="campo-dia" class="sugarads-label">Día</label>
          <select id="campo-dia" name="dia" aria-label="Día">
            <option value="">Seleccionar Día</option>
            <option value="Monday">Lunes</option>
            <option value="Tuesday">Martes</option>
            <option value="Wednesday">Miércoles</option>
            <option value="Thursday">Jueves</option>
            <option value="Friday">Viernes</option>
          </select>
        </div>

        <div class="sugarads-field">
          <label class="sugarads-label">Horas</label>
          <select id="campo-horas" name="hora[]" aria-label="Hora" multiple size="6">
            <option value="08:00">12:50</option>
            <option value="09:00">13:40</option>
            <option value="10:00">14:30</option>
            <option value="11:00">15:20</option>
            <option value="12:00">16:10</option>
            <option value="13:00">17:00</option>
            <option value="14:00">17:50</option>
            <option value="15:00">18:40</option>
            <option value="16:00">19:25</option>
          </select>
          <small>selecciona varias horas manteniendo Ctrl</small>
        </div>

        <div class="sugarads-field">
          <label for="campo-espacio" class="sugarads-label">Espacio</label>
          <select id="campo-espacio" name="salon" aria-label="Espacio">
            <option value="">Seleccionar Espacio</option>
            <?php echo $listaEspacios; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <label for="campo-turno" class="sugarads-label">Turno</label>
          <select id="campo-turno" name="turno" aria-label="Turno">
            <option value="">Seleccionar Turno</option>
            <option value="mañana">Mañana</option>
            <option value="tarde">Tarde</option>
            <option value="noche">Noche</option>
          </select>
        </div>

        <div class="sugarads-field">
          <button id="boton-guardar" class="sugarads-btn sugarads-btn-guardar" type="submit">Guardar</button>
          <button id="boton-cancelar" class="sugarads-btn sugarads-btn-cancelar" type="reset">Cancelar</button>
        </div>
      </form>
    </aside>

    <!-- Columna derecha: confirmación y resultado -->
    <article class="sugarads-col-right">
      <div class="sugarads-confirm-card" role="region" aria-live="polite" aria-label="Confirmación de asignación">
        <p class="sugarads-confirm-text">
          Se asignará la materia
          <strong class="sugarads-var var-asignatura">(<span aria-hidden="true">…</span>)</strong>
          con el profesor <strong class="sugarads-var var-profesor">(<span aria-hidden="true">…</span>)</strong>
          al grupo <strong class="sugarads-var var-grupo">(<span aria-hidden="true">…</span>)</strong>
          el día <strong class="sugarads-var var-dia">(<span aria-hidden="true">…</span>)</strong>,
          a las horas <strong class="sugarads-var var-horas">(<span aria-hidden="true">…</span>)</strong>,
          en el espacio <strong class="sugarads-var var-espacio">(<span aria-hidden="true">…</span>)</strong>,
          del turno <strong class="sugarads-var var-turno">(<span aria-hidden="true">…</span>)</strong>.
          <br>¿Desea realizar esta acción?
        </p>
      </div>

      <section class="sugarads-resultado" aria-label="Resultado de asignación">
        <h2 class="sugarads-resultado-title">Resultado</h2>
        <div id="resultado-asignacion" class="sugarads-canvas" aria-live="polite"></div>
      </section>
    </article>
  </section>
</main>
<script src="../JS/Adscripta/asignacion.js" defer></script>