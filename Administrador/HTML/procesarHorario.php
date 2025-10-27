<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Método no permitido");
}
error_log("Datos POST recibidos en procesarHorario.php:");
error_log(print_r($_POST, true));
$id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null;
$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$turno = isset($_POST['turno']) ? $_POST['turno'] : null;
$bloques = [];
if (isset($_POST['hora'])) {
    if (is_array($_POST['hora'])) $bloques = array_values(array_filter($_POST['hora']));
    else if (is_string($_POST['hora']) && $_POST['hora'] !== '') $bloques = [$_POST['hora']];
}
$id_asignatura = isset($_POST['id_asignatura']) ? intval($_POST['id_asignatura']) : null;
$id_espacio = isset($_POST['id_espacio']) ? intval($_POST['id_espacio']) : null;
$id_asocia = isset($_POST['id_asocia']) ? intval($_POST['id_asocia']) : null;
$id_grupo = isset($_POST['id_grupo']) && $_POST['id_grupo'] !== '' ? intval($_POST['id_grupo']) : null;
if (!$id_profesor || !$dia || empty($bloques) || !$id_asignatura || !$id_espacio || !$turno) {
    die("Error: Todos los campos son obligatorios");
}
try {
    mysqli_begin_transaction($con);
    $sql_check_prof = "SELECT 1 FROM profesor WHERE id_usuario = ?";
    $stmt_check = mysqli_prepare($con, $sql_check_prof);
    mysqli_stmt_bind_param($stmt_check, "i", $id_profesor);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) == 0) {
        $sql_prof = "INSERT INTO profesor (id_usuario) VALUES (?)";
        $stmt_prof = mysqli_prepare($con, $sql_prof);
        mysqli_stmt_bind_param($stmt_prof, "i", $id_profesor);
        mysqli_stmt_execute($stmt_prof);
        mysqli_stmt_close($stmt_prof);
    }
    mysqli_stmt_close($stmt_check);
    $sql_check_pa = "SELECT 1 FROM profesor_asignatura WHERE id_profesor = ? AND id_asignatura = ?";
    $stmt_check = mysqli_prepare($con, $sql_check_pa);
    mysqli_stmt_bind_param($stmt_check, "ii", $id_profesor, $id_asignatura);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) == 0) {
        $sql_pa = "INSERT INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
        $stmt_pa = mysqli_prepare($con, $sql_pa);
        mysqli_stmt_bind_param($stmt_pa, "ii", $id_profesor, $id_asignatura);
        mysqli_stmt_execute($stmt_pa);
        mysqli_stmt_close($stmt_pa);
    }
    mysqli_stmt_close($stmt_check);
    $sql_check_dup = "SELECT 1 FROM asocia WHERE 
                     ((id_espacio = ? AND turno = ? AND dia_semana = ? AND horario = ?) OR
                      (id_profesor = ? AND turno = ? AND dia_semana = ? AND horario = ?)) 
                     LIMIT 1";
    $stmt_check = mysqli_prepare($con, $sql_check_dup);
    $sql_insert = "INSERT INTO asocia 
        (id_asignatura, id_espacio, horario, id_profesor, id_grupo, turno, dia_semana)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($con, $sql_insert);
    $sql_update = "UPDATE asocia SET 
        id_asignatura = ?, id_espacio = ?, horario = ?, id_profesor = ?, id_grupo = ?, turno = ?, dia_semana = ?
        WHERE id_asocia = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    if (!$stmt_check || !$stmt_insert || !$stmt_update) {
        throw new Exception('Error preparando sentencias: ' . mysqli_error($con));
    }
    $first = true;
    foreach ($bloques as $idx => $b) {
        mysqli_stmt_bind_param($stmt_check, "isssisss", $id_espacio, $turno, $dia, $b, $id_profesor, $turno, $dia, $b);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            throw new Exception("Ya existe una clase en el bloque '$b' para el espacio o profesor seleccionado.");
        }
        mysqli_stmt_free_result($stmt_check);
        if ($id_asocia && $first) {
            mysqli_stmt_bind_param($stmt_update, "iisssisi", $id_asignatura, $id_espacio, $b, $id_profesor, $id_grupo, $turno, $dia, $id_asocia);
            if (!mysqli_stmt_execute($stmt_update)) {
                throw new Exception('Error actualizando horario: ' . mysqli_stmt_error($stmt_update));
            }
            $first = false;
        } else {
            mysqli_stmt_bind_param($stmt_insert, "iississ", $id_asignatura, $id_espacio, $b, $id_profesor, $id_grupo, $turno, $dia);
            if (!mysqli_stmt_execute($stmt_insert)) {
                throw new Exception('Error insertando horario: ' . mysqli_stmt_error($stmt_insert));
            }
        }
    }
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_insert);
    mysqli_stmt_close($stmt_update);
    mysqli_commit($con);
    header("Location: registroDatos.php?success=1");
    exit;
} catch (Exception $e) {
    mysqli_rollback($con);
    die("Error: " . $e->getMessage());
} finally {
    if (isset($con)) {
        mysqli_close($con);
    }
}
?>