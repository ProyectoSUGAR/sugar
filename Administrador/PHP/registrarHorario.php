<?php 
function validar_datos_horario($datos) {
    $errores = [];

    if (!$datos['id_profesor']) $errores[] = "Profesor no seleccionado.";
    if (!$datos['dia']) $errores[] = "Día no seleccionado.";
    if (!$datos['hora']) $errores[] = "Bloque horario no seleccionado.";
    if (!$datos['id_asignatura']) $errores[] = "Asignatura no seleccionada.";
    if (!$datos['id_espacio']) $errores[] = "Espacio no seleccionado.";
    if (!$datos['turno']) $errores[] = "Turno no seleccionado.";

    // 'hora' puede ser un string o un array (selección múltiple). Manejar ambos casos.
    if (is_array($datos['hora'])) {
        $horas_limpias = array_filter(array_map('trim', $datos['hora']));
        if (empty($horas_limpias)) {
            $errores[] = "El bloque horario no puede estar vacío.";
        }
    } else {
        if (empty(trim((string)($datos['hora'] ?? '')))) {
            $errores[] = "El bloque horario no puede estar vacío.";
        }
    }

    return $errores;
}

function verificar_profesor_existe($id_profesor) {
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "SELECT 1 FROM profesor WHERE id_usuario = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id_profesor);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $existe = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $existe;
}

function crear_profesor_si_no_existe($id_profesor) {
    if (!verificar_profesor_existe($id_profesor)) {
        $conn = conectar_bd();
        $stmt = mysqli_prepare($conn, "INSERT INTO profesor (id_usuario) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "i", $id_profesor);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function verificar_asignacion_profesor_asignatura($id_profesor, $id_asignatura) {
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "SELECT 1 FROM profesor_asignatura WHERE id_profesor = ? AND id_asignatura = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ii", $id_profesor, $id_asignatura);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $existe = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $existe;
}

function crear_asignacion_profesor_asignatura($id_profesor, $id_asignatura) {
    if (!verificar_asignacion_profesor_asignatura($id_profesor, $id_asignatura)) {
        $conn = conectar_bd();
        $stmt = mysqli_prepare($conn, "INSERT INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $id_profesor, $id_asignatura);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function verificar_duplicado_horario($id_espacio, $turno, $dia_semana, $horario, $id_asignatura, $id_profesor) {
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "SELECT 1 FROM asocia WHERE id_espacio = ? AND turno = ? AND dia_semana = ? AND horario = ? AND id_asignatura = ? AND id_profesor = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "issssi", $id_espacio, $turno, $dia_semana, $horario, $id_asignatura, $id_profesor);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $existe = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $existe;
}

function insertar_horario($id_asignatura, $id_espacio, $horario, $id_profesor, $turno, $dia_semana) {
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "INSERT INTO asocia (id_asignatura, id_espacio, horario, id_profesor, turno, dia_semana) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iissss", $id_asignatura, $id_espacio, $horario, $id_profesor, $turno, $dia_semana);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $resultado;
}

function actualizar_horario($id_asocia, $id_asignatura, $id_espacio, $horario, $id_profesor, $turno, $dia_semana) {
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "UPDATE asocia SET id_asignatura=?, id_espacio=?, horario=?, id_profesor=?, turno=?, dia_semana=? WHERE id_asocia=?");
    mysqli_stmt_bind_param($stmt, "iissssi", $id_asignatura, $id_espacio, $horario, $id_profesor, $turno, $dia_semana, $id_asocia);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $resultado;
}

function eliminar_horario($id_asocia) {
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "DELETE FROM asocia WHERE id_asocia = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_asocia);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $resultado;
}

function procesar_horario_registrado() {
    $conn = conectar_bd();

    if (!$conn) {
        echo "<script>alert('Error de conexión a la base de datos.'); window.history.back();</script>";
        exit;
    }

    $datos = [
        'id_profesor' => isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null,
        'dia' => isset($_POST['dia']) ? $_POST['dia'] : null,
        'turno' => isset($_POST['turno']) ? $_POST['turno'] : null,
        'hora' => isset($_POST['hora']) ? $_POST['hora'] : null,
        'id_asignatura' => isset($_POST['id_asignatura']) ? intval($_POST['id_asignatura']) : null,
        'id_espacio' => isset($_POST['id_espacio']) ? intval($_POST['id_espacio']) : null,
        'id_asocia' => isset($_POST['id_asocia']) ? intval($_POST['id_asocia']) : null,
    ];

    $errores = validar_datos_horario($datos);
    if (!empty($errores)) {
        echo "<script>alert('" . implode(' ', $errores) . "'); window.history.back();</script>";
        exit;
    }

    $dia_semana = $datos['dia'];

    // Normalizar horarios a array de strings
    if (is_array($datos['hora'])) {
        $horarios = array_values(array_filter(array_map('trim', $datos['hora'])));
    } else {
        $horarios = [trim((string)$datos['hora'])];
    }

    crear_profesor_si_no_existe($datos['id_profesor']);
    crear_asignacion_profesor_asignatura($datos['id_profesor'], $datos['id_asignatura']);

    if (!$datos['id_asocia']) {
        // Insertar cada bloque seleccionado
        foreach ($horarios as $horario) {
            if (verificar_duplicado_horario($datos['id_espacio'], $datos['turno'], $dia_semana, $horario, $datos['id_asignatura'], $datos['id_profesor'])) {
                echo "<script>alert('Ya existe esa materia y profesor en ese espacio, turno, día y bloque horario (bloque: $horario).'); window.history.back();</script>";
                exit;
            }
            if (!insertar_horario($datos['id_asignatura'], $datos['id_espacio'], $horario, $datos['id_profesor'], $datos['turno'], $dia_semana)) {
                $error = mysqli_error($conn);
                echo "<script>alert('Error al registrar el horario (bloque: $horario): $error'); window.history.back();</script>";
                exit;
            }
        }
        // Si llegamos aquí, todos los inserts fueron OK
        echo "<script>alert('Horario(s) registrado(s) correctamente.'); window.location.href='../../Administrador/HTML/registroDatos.php';</script>";
    } else {
        // En edición: actualizar el registro indicado con el primer horario seleccionado
        $horario = reset($horarios);
        if (actualizar_horario($datos['id_asocia'], $datos['id_asignatura'], $datos['id_espacio'], $horario, $datos['id_profesor'], $datos['turno'], $dia_semana)) {
            echo "<script>alert('Horario actualizado correctamente.'); window.location.href='../../Administrador/HTML/registroDatos.php';</script>";
        } else {
            $error = mysqli_error($conn);
            echo "<script>alert('Error al actualizar el horario: $error'); window.history.back();</script>";
        }
    }
}

function procesar_eliminacion_horario() {
    if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
        $id = intval($_GET['eliminar']);
        if (eliminar_horario($id)) {
            echo "<script>alert('Horario eliminado correctamente.'); window.location.href='../../Administrador/HTML/registroDatos.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar el horario.'); window.history.back();</script>";
        }
        exit;
    }
}

function procesar_registro_horario_principal() {
    require_once("../../PHP/conexion.php");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        procesar_horario_registrado();
    } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
        procesar_eliminacion_horario();
    }
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    procesar_registro_horario_principal();
}
?>