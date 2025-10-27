<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    error_log("Procesando datos del formulario:");
    error_log(print_r($_POST, true));
    $id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null;
    $dia = isset($_POST['dia']) ? $_POST['dia'] : null;
    $turno = isset($_POST['turno']) ? $_POST['turno'] : null;
    $bloque = isset($_POST['hora']) ? $_POST['hora'] : null;
    $id_asignatura = isset($_POST['id_asignatura']) ? intval($_POST['id_asignatura']) : null;
    $id_espacio = isset($_POST['id_espacio']) ? intval($_POST['id_espacio']) : null;
    $id_asocia = isset($_POST['id_asocia']) ? intval($_POST['id_asocia']) : null;
    error_log("Datos procesados:");
    error_log("Profesor: $id_profesor");
    error_log("Día: $dia");
    error_log("Turno: $turno");
    error_log("Bloque: $bloque");
    error_log("Asignatura: $id_asignatura");
    error_log("Espacio: $id_espacio");
}
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    mysqli_query($con, "DELETE FROM asocia WHERE id_asocia = $id");
    header("Location: registroDatos.php");
    exit;
}
$horarioEditar = null;
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = mysqli_query($con, "SELECT * FROM asocia WHERE id_asocia = $id");
    $horarioEditar = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Datos</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <script src="../JS/bloquesHorarios.js"></script>
</head>
<body class="bodyregidat">
    <?php include '../../HEADERS/headerAA.php'; ?>
    <main class="sugarads-main">
        <h1 class="sugarads-title">Registro de Horarios</h1>
        <div class="sugarads-grid">
            <div class="sugarads-col-left sugar-card">
                <!-- crear horario -->
                <form id="form-crear" class="formasig" method="post" action="procesarHorario.php" style="border-top:2px solid #c7b299; padding-top:1.5rem; max-width:400px;">
                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                        Horario guardado exitosamente
                    </div>
                    <?php endif; ?>
                    <h2 class="sugarads-section-title">Crear Horario</h2>
                    <!-- profesor -->
                    <div class="sugarads-field">
                        <label for="hor-profesor" class="sugarads-label">Profesor</label>
                        <select id="hor-profesor" class="sasignacion1" name="id_profesor" required>
                            <option value="">Seleccionar Profesor</option>
                            <?php
                            $profesores = mysqli_query($con, "SELECT u.id_usuario, u.nombre, u.apellido 
                                FROM usuario u 
                                WHERE u.tipo_usuario = 'profesor'
                                ORDER BY u.apellido, u.nombre");
                            while ($p = mysqli_fetch_object($profesores)) {
                                echo '<option value="' . $p->id_usuario . '">' . 
                                     htmlspecialchars($p->apellido . ', ' . $p->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- asignatura -->
                    <div class="sugarads-field">
                        <label for="hor-asignatura" class="sugarads-label">Asignatura</label>
                        <select id="hor-asignatura" class="sasignacion1" name="id_asignatura" required>
                            <option value="">Seleccionar Asignatura</option>
                            <?php
                            $asignaturas = mysqli_query($con, "SELECT id_asignatura, nombre FROM asignatura");
                            while ($a = mysqli_fetch_object($asignaturas)) {
                                echo '<option value="' . $a->id_asignatura . '">' . htmlspecialchars($a->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- espacio -->
                    <div class="sugarads-field">
                        <label for="hor-espacio" class="sugarads-label">Espacio</label>
                        <select id="hor-espacio" class="sasignacion1" name="id_espacio" required>
                            <option value="">Seleccionar Espacio</option>
                            <?php
                            $espacios = mysqli_query($con, "SELECT id_espacio, nombre FROM espacio");
                            while ($e = mysqli_fetch_object($espacios)) {
                                echo '<option value="' . $e->id_espacio . '">' . htmlspecialchars($e->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- grupo -->
                    <div class="sugarads-field">
                        <label for="hor-grupo" class="sugarads-label">Grupo</label>
                        <select id="hor-grupo" class="sasignacion1" name="id_grupo">
                            <option value="">(Sin grupo)</option>
                            <?php
                            $grupos = mysqli_query($con, "SELECT id_grupo, nombre, grupo, anio FROM grupo ORDER BY anio, nombre, grupo");
                            while ($g = mysqli_fetch_assoc($grupos)) {
                                $label = trim(($g['anio'] ? $g['anio'].' - ' : '') . $g['nombre']);
                                echo '<option value="' . $g['id_grupo'] . '">' . htmlspecialchars($label) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- dia -->
                    <div class="sugarads-field">
                        <label for="hor-dia" class="sugarads-label">Día</label>
                        <select id="hor-dia" class="sasignacion1" name="dia" required>
                            <option value="">Seleccionar Día</option>
                            <?php
                            $dias = ['lunes','martes','miercoles','jueves','viernes'];
                            foreach($dias as $d) {
                                echo '<option value="'.$d.'">'.ucfirst($d).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- tuerno -->
                    <div class="sugarads-field">
                        <label for="hor-turno" class="sugarads-label">Turno</label>
                        <select id="hor-turno" class="sasignacion1" name="turno" required>
                            <option value="">Seleccionar Turno</option>
                            <?php
                            $turnos = ['manana'=>'Mañana','tarde'=>'Tarde','noche'=>'Noche'];
                            foreach($turnos as $val=>$txt) {
                                echo '<option value="'.$val.'">'.$txt.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- bloque horario -->
                    <div class="sugarads-field">
                        <label for="hor-hora" class="sugarads-label">Bloque Horario (selecciona uno o varios)</label>
                        <select id="hor-hora" name="hora[]" class="sasignacion1" multiple required>
                            <option value="">Selecciona primero el turno para ver los bloques</option>
                        </select>
                    </div>
                    <div class="sugarads-field">
                        <br>
                        <button type="submit" class="btn-primario">Guardar</button>
                        <button type="reset" class="btn-secundario">Cancelar</button>
                    </div>
                </form>
                <!-- editar el horario -->
                <?php if ($horarioEditar): ?>
                <form id="form-editar" class="sugarads-form" method="post" action="../../PHP/registrarHorario.php" style="margin-top:2.5rem; border-top:2px solid #2d89ef; padding-top:1.5rem;">
                    <h2 class="sugarads-section-title">Editar Horario</h2>
                    <input type="hidden" name="id_asocia" value="<?= htmlspecialchars($horarioEditar['id_asocia']) ?>">
                    <!-- perofesor -->
                    <div class="sugarads-field">
                        <label for="hor-profesor-editar" class="sugarads-label">Profesor</label>
                        <select id="hor-profesor-editar" name="id_profesor" required>
                            <option value="">Seleccionar Profesor</option>
                            <?php
                            $profesores = mysqli_query($con, "SELECT u.id_usuario, u.nombre, u.apellido 
                                FROM profesor p 
                                INNER JOIN usuario u ON p.id_usuario = u.id_usuario 
                                WHERE u.tipo_usuario = 'profesor'
                                ORDER BY u.apellido, u.nombre");
                            while ($p = mysqli_fetch_object($profesores)) {
                                $sel = $horarioEditar && $horarioEditar['id_profesor'] == $p->id_usuario ? 'selected' : '';
                                echo '<option value="' . $p->id_usuario . '" '.$sel.'>' . 
                                     htmlspecialchars($p->apellido . ', ' . $p->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- asignatura -->
                    <div class="sugarads-field">
                        <label for="hor-asignatura-editar" class="sugarads-label">Asignatura</label>
                        <select id="hor-asignatura-editar" name="id_asignatura" required>
                            <option value="">Seleccionar Asignatura</option>
                            <?php
                            $asignaturas = mysqli_query($con, "SELECT id_asignatura, nombre FROM asignatura");
                            while ($a = mysqli_fetch_object($asignaturas)) {
                                $sel = $horarioEditar && $horarioEditar['id_asignatura'] == $a->id_asignatura ? 'selected' : '';
                                echo '<option value="' . $a->id_asignatura . '" '.$sel.'>' . htmlspecialchars($a->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- espacio -->
                    <div class="sugarads-field">
                        <label for="hor-espacio-editar" class="sugarads-label">Espacio</label>
                        <select id="hor-espacio-editar" name="id_espacio" required>
                            <option value="">Seleccionar Espacio</option>
                            <?php
                            $espacios = mysqli_query($con, "SELECT id_espacio, nombre FROM espacio");
                            while ($e = mysqli_fetch_object($espacios)) {
                                $sel = $horarioEditar && $horarioEditar['id_espacio'] == $e->id_espacio ? 'selected' : '';
                                echo '<option value="' . $e->id_espacio . '" '.$sel.'>' . htmlspecialchars($e->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- dia -->
                    <div class="sugarads-field">
                        <label for="hor-dia-editar" class="sugarads-label">Día</label>
                        <select id="hor-dia-editar" name="dia" required>
                            <option value="">Seleccionar Día</option>
                            <?php
                            $dias = ['lunes','martes','miercoles','jueves','viernes'];
                            foreach($dias as $d) {
                                $sel = $horarioEditar && $horarioEditar['dia_semana'] == $d ? 'selected' : '';
                                echo '<option value="'.$d.'" '.$sel.'>'.ucfirst($d).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- turno -->
                    <div class="sugarads-field">
                        <label for="hor-turno-editar" class="sugarads-label">Turno</label>
                        <select id="hor-turno-editar" name="turno" required>
                            <option value="">Seleccionar Turno</option>
                            <?php
                            $turnos = ['manana'=>'Mañana','tarde'=>'Tarde','noche'=>'Noche'];
                            foreach($turnos as $val=>$txt) {
                                $sel = $horarioEditar && $horarioEditar['turno'] == $val ? 'selected' : '';
                                echo '<option value="'.$val.'" '.$sel.'>'.$txt.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- bloque horario (checkboxes renderizados por JS) -->
                    <div class="sugarads-field">
                        <label class="sugarads-label">Bloque Horario (selecciona uno o varios)</label>
                        <div id="hor-hora-editar-container" class="horarios-container" aria-live="polite">
                            <small class="help">Selecciona primero el turno para ver los bloques.</small>
                        </div>
                    </div>
                    <div class="sugarads-field">
                        <button type="submit" class="btn-primario">Actualizar</button>
                        <a href="registroDatos.php" class="btn-secundario" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center; padding:8px 12px;">Cancelar</a>
                    </div>
                </form>
                <?php endif; ?>
                <!-- eliminar horaio -->
                <?php if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])): ?>
                <form id="form-eliminar" class="sugarads-form" method="get" action="registroDatos.php" style="margin-top:2.5rem; border-top:2px solid #e53935; padding-top:1.5rem;">
                    <h2 class="sugarads-section-title" style="color:#e53935;">Eliminar Horario</h2>
                    <input type="hidden" name="eliminar" value="<?= intval($_GET['eliminar']) ?>">
                    <div class="sugarads-field">
                        <span>¿Estás seguro que deseas eliminar este horario?</span>
                    </div>
                    <div class="sugarads-field">
                        <button type="submit" class="btn-secundario" style="background:#e53935;color:#fff;">Eliminar</button>
                        <a href="registroDatos.php" class="btn-primario" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center; padding:8px 12px;">Cancelar</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
            <div class="horarios-registrados">
                <h2 class="horarios-titulo">Horarios Registrados</h2>
                <div class="table-container">
                    <table class="horarios-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profesor</th>
                                <th>Asignatura</th>
                                <th>Espacio</th>
                                <th>Grupo</th>
                                <th>Día</th>
                                <th>Turno</th>
                                <th>Horario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $q = "SELECT a.*, u.nombre AS prof_nombre, u.apellido AS prof_apellido, asig.nombre AS asig_nombre, e.nombre AS esp_nombre, g.nombre AS grupo_nombre, g.grupo AS grupo_codigo, g.anio AS grupo_anio FROM asocia a
                        LEFT JOIN usuario u ON a.id_profesor = u.id_usuario
                        LEFT JOIN asignatura asig ON a.id_asignatura = asig.id_asignatura
                        LEFT JOIN espacio e ON a.id_espacio = e.id_espacio
                        LEFT JOIN grupo g ON a.id_grupo = g.id_grupo
                        ORDER BY a.dia_semana, a.turno, a.horario";
                        $res = mysqli_query($con, $q);
                        while($h = mysqli_fetch_assoc($res)) {
                            echo '<tr>';
                            echo '<td>'.(isset($h['id_asocia']) ? htmlspecialchars($h['id_asocia']) : '-').'</td>';
                            echo '<td>'.htmlspecialchars($h['prof_nombre'].' '.$h['prof_apellido']).'</td>';
                            echo '<td>'.htmlspecialchars($h['asig_nombre']).'</td>';
                            echo '<td>'.htmlspecialchars($h['esp_nombre']).'</td>';
                            echo '<td>'.(isset($h['grupo_nombre']) ? htmlspecialchars((($h['grupo_anio']) ? $h['grupo_anio'].' - ' : '').$h['grupo_nombre']) : '-') .'</td>';
                            echo '<td>'.htmlspecialchars($h['dia_semana']).'</td>';
                            echo '<td>'.htmlspecialchars($h['turno']).'</td>';
                            echo '<td>'.htmlspecialchars($h['horario']).'</td>';
                            echo '<td class="actions">';
                            if (isset($h['id_asocia'])) {
                                echo '<a href="?editar='.$h['id_asocia'].'" class="btn-editar">Editar</a>';
                                echo '<a href="?eliminar='.$h['id_asocia'].'" class="btn-eliminar" onclick="return confirm(\'¿Eliminar este horario?\')">Eliminar</a>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="../JS/bloquesHorarios.js"></script>
</body>
</html>
