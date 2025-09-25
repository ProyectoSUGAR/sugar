<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();
$conexion_abierta = true;

// CREAR o EDITAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && $con && $conexion_abierta) {
    $id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null;
    $dia = isset($_POST['dia']) ? $_POST['dia'] : null;
    $turno = isset($_POST['turno']) ? $_POST['turno'] : null;
    $bloque = isset($_POST['hora']) ? $_POST['hora'] : null;
    $id_asignatura = isset($_POST['id_asignatura']) ? intval($_POST['id_asignatura']) : null;
    $id_espacio = isset($_POST['id_espacio']) ? intval($_POST['id_espacio']) : null;
    $id_asocia = isset($_POST['id_asocia']) ? intval($_POST['id_asocia']) : null;

    if ($id_profesor && $dia && $bloque && $id_asignatura && $id_espacio && $turno) {
        $dia_semana = $dia;
        $horario = $bloque;
        if (empty(trim($horario))) {
            echo "<script>alert('El bloque horario no puede estar vacío.'); window.history.back();</script>";
            exit;
        }

        // 1. Asegura que el profesor exista en la tabla profesor
        $sql_prof = "SELECT 1 FROM profesor WHERE id_usuario = ? LIMIT 1";
        $stmt_prof = mysqli_prepare($con, $sql_prof);
        mysqli_stmt_bind_param($stmt_prof, "i", $id_profesor);
        mysqli_stmt_execute($stmt_prof);
        mysqli_stmt_store_result($stmt_prof);
        $existe_prof = mysqli_stmt_num_rows($stmt_prof) > 0;
        mysqli_stmt_close($stmt_prof);
        if (!$existe_prof) {
            $sql_insert_prof = "INSERT INTO profesor (id_usuario) VALUES (?)";
            $stmt_insert_prof = mysqli_prepare($con, $sql_insert_prof);
            mysqli_stmt_bind_param($stmt_insert_prof, "i", $id_profesor);
            mysqli_stmt_execute($stmt_insert_prof);
            mysqli_stmt_close($stmt_insert_prof);
        }

        // 2. Asegura la relación profesor-asignatura
        $sql_check = "SELECT 1 FROM profesor_asignatura WHERE id_profesor = ? AND id_asignatura = ? LIMIT 1";
        $stmt_check = mysqli_prepare($con, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "ii", $id_profesor, $id_asignatura);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        $existe = mysqli_stmt_num_rows($stmt_check) > 0;
        mysqli_stmt_close($stmt_check);
        if (!$existe) {
            $sql_insert_pa = "INSERT INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
            $stmt_insert_pa = mysqli_prepare($con, $sql_insert_pa);
            mysqli_stmt_bind_param($stmt_insert_pa, "ii", $id_profesor, $id_asignatura);
            mysqli_stmt_execute($stmt_insert_pa);
            mysqli_stmt_close($stmt_insert_pa);
        }

        // CREAR
        if (!$id_asocia) {
            // Verifica duplicado
            $sql_check_duplicado = "SELECT 1 FROM asocia WHERE id_espacio = ? AND turno = ? AND dia_semana = ? AND horario = ? AND id_asignatura = ? AND id_profesor = ? LIMIT 1";
            $stmt_check_duplicado = mysqli_prepare($con, $sql_check_duplicado);
            mysqli_stmt_bind_param($stmt_check_duplicado, "issssi", $id_espacio, $turno, $dia_semana, $horario, $id_asignatura, $id_profesor);
            mysqli_stmt_execute($stmt_check_duplicado);
            mysqli_stmt_store_result($stmt_check_duplicado);
            $existe_duplicado = mysqli_stmt_num_rows($stmt_check_duplicado) > 0;
            mysqli_stmt_close($stmt_check_duplicado);
            if ($existe_duplicado) {
                echo "<script>alert('Ya existe esa materia y profesor en ese espacio, turno, día y bloque horario.'); window.history.back();</script>";
                exit;
            } else {
                $sql = "INSERT INTO asocia (id_asignatura, id_espacio, horario, id_profesor, turno, dia_semana) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "iissss", $id_asignatura, $id_espacio, $horario, $id_profesor, $turno, $dia_semana);
                $ok = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                if ($ok) {
                    echo "<script>alert('Horario registrado correctamente.'); window.location.href='../HTML/registroDatos.php';</script>";
                    exit;
                } else {
                    $error = mysqli_error($con);
                    echo "<script>alert('Error al registrar el horario: $error'); window.history.back();</script>";
                    exit;
                }
            }
        } else {
            // EDITAR
            $sql = "UPDATE asocia SET id_asignatura=?, id_espacio=?, horario=?, id_profesor=?, turno=?, dia_semana=? WHERE id_asocia=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "iissssi", $id_asignatura, $id_espacio, $horario, $id_profesor, $turno, $dia_semana, $id_asocia);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if ($ok) {
                echo "<script>alert('Horario actualizado correctamente.'); window.location.href='../HTML/registroDatos.php';</script>";
                exit;
            } else {
                $error = mysqli_error($con);
                echo "<script>alert('Error al actualizar el horario: $error'); window.history.back();</script>";
                exit;
            }
        }
    } else {
        echo "<script>alert('Datos incompletos.'); window.history.back();</script>";
        exit;
    }
}

// ELIMINAR (por GET, para compatibilidad con el form de eliminar)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    mysqli_query($con, "DELETE FROM asocia WHERE id_asocia = $id");
    echo "<script>alert('Horario eliminado correctamente.'); window.location.href='../HTML/registroDatos.php';</script>";
    exit;
}

if (isset($con) && $con instanceof mysqli) {
    if (@$con->ping()) {
        $conexion_abierta = false;
    }
}
?>