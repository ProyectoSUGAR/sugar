<?php

// Incluir el header y la conexión a la base de datos
include '../../HEADERS/headerA.php';
require_once("../../PHP/conexion.php");

// Conectar a la base de datos
$conn = conectar_bd();

// Función para obtener opciones de un query
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
if (isset($conn) && $conn instanceof mysqli) {
  if (@$conn->ping()) {

  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de clases</title>
    <link rel="stylesheet" href="/SUGAR/Css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="body-estudiante">
    <main class="contenido-principal">
      <section class="panels-formulario asignacion-panel">
        <h1 class="titulo-estadisticas">Asignación de clases</h1>
        <hr>
        <br>
        <div class="division-asignacion"></div>
        <form id="formulario-asignacion" class="formulario-registro form-asignacion-grid" method="post" action="../PHP/asignacionProf.php">
          <div class="campo-asignacion">
            <label for="campo-grupo" class="sugarads-label">Grupo</label>
            <br>
            <select id="campo-grupo" class="sasignacion" name="grupo" aria-label="Grupo" required>
              <option value="">Selecciona un grupo</option>
              <?php echo $listaGrupos; ?>
            </select>
          </div>
          <div class="campo-asignacion">
            <label for="campo-asignatura" class="sugarads-label">Asignatura</label>
            <br>
            <select id="campo-asignatura" class="sasignacion" name="asignatura" aria-label="Asignatura" required>
              <option value="">Selecciona una asignatura</option>
              <?php echo $listaAsignaturas; ?>
            </select>
          </div>
          <div class="campo-asignacion">
            <label for="campo-profesor" class="sugarads-label">Profesor</label>
            <br>
            <select id="campo-profesor" class="sasignacion" name="profesor" aria-label="Profesor" required>
              <option value="">Selecciona un profesor</option>
              <?php echo $listaProfesores ?: '<option value="">No hay profesores</option>'; ?>
            </select>
          </div>

          <div class="campo-asignacion" style="grid-column:1/3;display:flex;gap:1.2rem;justify-content:center;">
            <button id="boton-guardar" class="btn-primario" type="submit">Guardar</button>
            <button id="boton-cancelar" class="btn-primario" type="reset">Cancelar</button>
          </div>
        </form>
        <?php 
        // Mostrar mensajes de resultado o error
        if (isset($_GET['resultado'])): 
        ?>
          <div class="sugarads-canvas sugarads-success">
            <?= htmlspecialchars($_GET['resultado']) ?>
          </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
          <div class="sugarads-canvas sugarads-error">
            <?= htmlspecialchars($_GET['error']) ?>
          </div>
        <?php endif; ?>
      </section>
    </main>
    <script src="../JS/menuHamburguesa.js"></script>
</body>
</html>