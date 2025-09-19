<?php
include '../../PHP/header.php';
require("../../PHP/conexion.php");

$conn = conectar_bd();

function getOpciones($query, $conn) {
    $result = mysqli_query($conn, $query);
    $opciones = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $opciones .= '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
    }
    return $opciones;
}

$listaGrupos = getOpciones("SELECT id_grupo as id, CONCAT(nombre, ' (Año ', anio, ')') as nombre FROM grupo", $conn);
$listaAsignaturas = getOpciones("SELECT id_asignatura as id, nombre FROM asignatura", $conn);
$listaProfesores = getOpciones("SELECT id_usuario as id, CONCAT(nombre, ' ', apellido) as nombre FROM usuario WHERE tipo_usuario = 'profesor'", $conn);
$listaEspacios = getOpciones("SELECT id_espacio as id, nombre FROM espacio", $conn);

mysqli_close($conn);
?>
<main class="sugarads-main">
  <section class="sugarads-section titulo">
    <h1 class="sugarads-title">Asignación de clases</h1>
  </section>

  <section class="sugarads-grid">
    <aside class="sugarads-col-left" aria-labelledby="sugarads-form-labels">
      <form id="formulario-asignacion" class="sugarads-form" method="post" action="../PHP/asignacionProf.php">
        <div class="sugarads-field">
          <label for="campo-grupo" class="sugarads-label">Grupo</label>
          <select id="campo-grupo" name="grupo" aria-label="Grupo" required>
            <option value="">Seleccionar Grupo</option>
            <?php echo $listaGrupos; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <label for="campo-asignatura" class="sugarads-label">Asignatura</label>
          <select id="campo-asignatura" name="asignatura" aria-label="Asignatura" required>
            <option value="">Seleccionar Asignatura</option>
            <?php echo $listaAsignaturas; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <label for="campo-profesor" class="sugarads-label">Profesor</label>
          <select id="campo-profesor" name="profesor" aria-label="Profesor" required>
            <option value="">Seleccionar Profesor</option>
            <?php echo $listaProfesores ?: '<option value="">No hay profesores</option>'; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <label for="campo-dia" class="sugarads-label">Día</label>
          <select id="campo-dia" name="dia" aria-label="Día" required>
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
          <select id="campo-horas" name="hora[]" aria-label="Hora" multiple size="6" required>
            <option value="12:50">12:50</option>
            <option value="13:40">13:40</option>
            <option value="14:30">14:30</option>
            <option value="15:20">15:20</option>
            <option value="16:10">16:10</option>
            <option value="17:00">17:00</option>
            <option value="17:50">17:50</option>
            <option value="18:40">18:40</option>
            <option value="19:25">19:25</option>
          </select>
          <small>Selecciona varias horas manteniendo Ctrl</small>
        </div>

        <div class="sugarads-field">
          <label for="campo-espacio" class="sugarads-label">Espacio</label>
          <select id="campo-espacio" name="salon" aria-label="Espacio" required>
            <option value="">Seleccionar Espacio</option>
            <?php echo $listaEspacios; ?>
          </select>
        </div>

        <div class="sugarads-field">
          <button id="boton-guardar" class="sugarads-btn sugarads-btn-guardar" type="submit">Guardar</button>
          <button id="boton-cancelar" class="sugarads-btn sugarads-btn-cancelar" type="reset">Cancelar</button>
        </div>
      </form>
      <?php if (isset($_GET['resultado'])): ?>
        <div class="sugarads-canvas sugarads-success"><?= htmlspecialchars($_GET['resultado']) ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="sugarads-canvas sugarads-error"><?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>
    </aside>
  </section>
</main>