<?php
require("../../PHP/conexion.php");
$con = conectar_bd();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Datos</title>
    <link rel="stylesheet" href="../CSS/asignacion.css">
</head>
<body>
    <?php include '../../PHP/header.php'; ?>
    <main class="sugarads-main">
        <h1 class="sugarads-title">Registro de Datos</h1>
        <div class="sugarads-grid registro-datos">
            <section class="sugarads-col-left">
                <form id="form-horario" class="sugarads-form" method="post" action="../PHP/registrarHorario.php">
    <h2 class="sugarads-section-title">Registro de Horarios</h2>

    <!-- Profesor -->
    <div class="sugarads-field">
        <label for="hor-profesor" class="sugarads-label">Profesor</label>
        <select id="hor-profesor" name="id_profesor" required>
            <option value="">Seleccionar Profesor</option>
            <?php
            $profesores = mysqli_query($con, "SELECT u.id_usuario, u.nombre, u.apellido FROM usuario u WHERE u.tipo_usuario = 'profesor'");
            while ($p = mysqli_fetch_object($profesores)) {
                echo '<option value="' . $p->id_usuario . '">' . htmlspecialchars($p->nombre . ' ' . $p->apellido) . '</option>';
            }
            ?>
        </select>
    </div>

    <!-- Día -->
    <div class="sugarads-field">
        <label for="hor-dia" class="sugarads-label">Día</label>
        <select id="hor-dia" name="dia" required>
            <option value="">Seleccionar Día</option>
            <option value="Monday">Lunes</option>
            <option value="Tuesday">Martes</option>
            <option value="Wednesday">Miércoles</option>
            <option value="Thursday">Jueves</option>
            <option value="Friday">Viernes</option>
        </select>
    </div>

    <!-- Turno (solo filtro visual) -->
    <div class="sugarads-field">
        <label for="hor-turno" class="sugarads-label">Turno</label>
        <select id="hor-turno" name="turno" required>
            <option value="">Seleccionar Turno</option>
            <option value="mañana">Mañana</option>
            <option value="tarde">Tarde</option>
            <option value="noche">Noche</option>
        </select>
    </div>

    <!-- bloque horario -->
    <div class="sugarads-field">
        <label for="hor-hora" class="sugarads-label">Bloque Horario</label>
        <select id="hor-hora" name="hora" required>
            <option value="">Selecciona primero el turno</option>
        </select>
    </div>

    <div class="sugarads-field">
        <button type="submit" class="sugarads-btn sugarads-btn-guardar">Guardar</button>
        <button type="reset" class="sugarads-btn sugarads-btn-cancelar">Cancelar</button>
    </div>
</form>
                <br>

                <!-- horarios registrados -->
                <div class="sugarads-entradas sombra">
                    <h2>Horarios registrados</h2>
                    <?php
                    $query = "
                        SELECT 
                            a.horario,
                            asig.nombre AS nombre_asignatura,
                            esp.nombre AS nombre_espacio
                        FROM asocia a
                        INNER JOIN asignatura asig ON a.id_asignatura = asig.id_asignatura
                        INNER JOIN espacio esp ON a.id_espacio = esp.id_espacio
                        ORDER BY a.horario DESC
                    ";

                    $resultado = mysqli_query($con, $query);

                    function determinarTurno($horario) {
                        $hora = (int)date('H', strtotime($horario));
                        if ($hora >= 7 && $hora < 14) return 'Mañana';
                        if ($hora >= 14 && $hora < 19) return 'Tarde';
                        return 'Noche';
                    }

                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        while ($fila = mysqli_fetch_object($resultado)) {
                            $turno = determinarTurno($fila->horario);
                            echo "<div class='sugarads-horario-item'>";
                            echo "<b>Horario:</b> " . htmlspecialchars($fila->horario) . "<br>";
                            echo "<b>Asignatura:</b> " . htmlspecialchars($fila->nombre_asignatura) . "<br>";
                            echo "<b>Espacio:</b> " . htmlspecialchars($fila->nombre_espacio) . "<br>";
                            echo "<b>Turno:</b> <span class='sugarads-label sugarads-turno'>" . htmlspecialchars($turno) . "</span>";
                            echo "<hr></div>";
                        }
                    } else {
                        echo "<p>No hay horarios registrados.</p>";
                    }
                    ?>
                </div>
            </section>
        </div>
    </main>
    <script src="../JS/bloquesHorarios.js"></script>
    <?php mysqli_close($con); ?>
</body>
</html>