<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();
$conexion_abierta = true;

// crear o editar horarios
if ($_SERVER["REQUEST_METHOD"] === "POST" && $con && $conexion_abierta) {

    // se llaman los datos del formulario
    // isset es una funcion que verifica si una variable está definida y no es null
    // intval es una funcion que convierte una variable a entero
    $id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null;
    $dia = isset($_POST['dia']) ? $_POST['dia'] : null;
    $turno = isset($_POST['turno']) ? $_POST['turno'] : null;
    $bloque = isset($_POST['hora']) ? $_POST['hora'] : null;
    $id_asignatura = isset($_POST['id_asignatura']) ? intval($_POST['id_asignatura']) : null;
    $id_espacio = isset($_POST['id_espacio']) ? intval($_POST['id_espacio']) : null;
    $id_asocia = isset($_POST['id_asocia']) ? intval($_POST['id_asocia']) : null;

    // validar que los datos obligatorios estén presentes
    // empty es una funcion que verifica si una variable está vacía
    // trim es una funcion que elimina espacios en blanco al inicio y al final de una cadena
    if ($id_profesor && $dia && $bloque && $id_asignatura && $id_espacio && $turno) {
        $dia_semana = $dia;
        $horario = $bloque;
        if (empty(trim($horario))) {
            echo "<script>alert('El bloque horario no puede estar vacío.'); window.history.back();</script>";
            exit;
        }

        // 1. asegura que el profesor exista en la tabla profesor
        // si no existe, lo crea
        // si existe, no hace nada
        $sql_prof = "SELECT 1 FROM profesor WHERE id_usuario = ? LIMIT 1";

        //mysqli_prepare prepara una consulta SQL para su ejecución
        $stmt_prof = mysqli_prepare($con, $sql_prof);
        //mysqli_stmt_bind_param vincula variables a una sentencia preparada como parámetros
        mysqli_stmt_bind_param($stmt_prof, "i", $id_profesor);
        //mysqli_stmt_execute ejecuta una sentencia preparada
        mysqli_stmt_execute($stmt_prof);
        //mysqli_stmt_store_result almacena el resultado de una sentencia preparada
        mysqli_stmt_store_result($stmt_prof);
        //mysqli_stmt_num_rows obtiene el número de filas de un conjunto de resultados almacenados
        $existe_prof = mysqli_stmt_num_rows($stmt_prof) > 0;
        //mysqli_stmt_close cierra una sentencia preparada
        mysqli_stmt_close($stmt_prof);

        // si no existe, lo crea
        if (!$existe_prof) {
            //sql_insert_prof prepara la inserción en la tabla profesor
            $sql_insert_prof = "INSERT INTO profesor (id_usuario) VALUES (?)";
            //mysqli_prepare prepara una consulta SQL para su ejecución
            $stmt_insert_prof = mysqli_prepare($con, $sql_insert_prof);
            //mysqli_stmt_bind_param vincula variables a una sentencia preparada como parámetros
            mysqli_stmt_bind_param($stmt_insert_prof, "i", $id_profesor);
            //mysqli_stmt_execute ejecuta una sentencia preparada
            mysqli_stmt_execute($stmt_insert_prof);
            //mysqli_stmt_close cierra una sentencia preparada
            mysqli_stmt_close($stmt_insert_prof);
        }

        // 2. asegura la relación profesor-asignatura
        // si no existe, la crea
        // si existe, no hace nada
        $sql_check = "SELECT 1 FROM profesor_asignatura WHERE id_profesor = ? AND id_asignatura = ? LIMIT 1";
        //mysqli_prepare prepara una consulta SQL para su ejecución
        $stmt_check = mysqli_prepare($con, $sql_check);
        //mysqli_stmt_bind_param vincula variables a una sentencia preparada como parámetros
        mysqli_stmt_bind_param($stmt_check, "ii", $id_profesor, $id_asignatura);
        //mysqli_stmt_execute ejecuta una sentencia preparada
        mysqli_stmt_execute($stmt_check);
        //mysqli_stmt_store_result almacena el resultado de una sentencia preparada
        mysqli_stmt_store_result($stmt_check);
        //mysqli_stmt_num_rows obtiene el número de filas de un conjunto de resultados almacenados
        $existe = mysqli_stmt_num_rows($stmt_check) > 0;
        //mysqli_stmt_close cierra una sentencia preparada
        mysqli_stmt_close($stmt_check);

        // si no existe, la crea
        // si existe, no hace nada
        if (!$existe) {

            //sql_insert_pa prepara la inserción en la tabla profesor_asignatura
            $sql_insert_pa = "INSERT INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
            //mysqli_prepare prepara una consulta SQL para su ejecución
            $stmt_insert_pa = mysqli_prepare($con, $sql_insert_pa);
            //mysqli_stmt_bind_param vincula variables a una sentencia preparada como parámetros
            mysqli_stmt_bind_param($stmt_insert_pa, "ii", $id_profesor, $id_asignatura);
            //mysqli_stmt_execute ejecuta una sentencia preparada
            mysqli_stmt_execute($stmt_insert_pa);
            //mysqli_stmt_close cierra una sentencia preparada
            mysqli_stmt_close($stmt_insert_pa);
        }

        // CREAR
        // si no se envió id_asocia, es creación
        if (!$id_asocia) {
            // sql_check_duplicado verifica si ya existe esa combinación en la tabla asocia
            $sql_check_duplicado = "SELECT 1 FROM asocia WHERE id_espacio = ? AND turno = ? AND dia_semana = ? AND horario = ? AND id_asignatura = ? AND id_profesor = ? LIMIT 1";
            //mysqli_prepare prepara una consulta SQL para su ejecución
            $stmt_check_duplicado = mysqli_prepare($con, $sql_check_duplicado);
            //mysqli_stmt_bind_param vincula variables a una sentencia preparada como parámetros
            mysqli_stmt_bind_param($stmt_check_duplicado, "issssi", $id_espacio, $turno, $dia_semana, $horario, $id_asignatura, $id_profesor);
            //mysqli_stmt_execute ejecuta una sentencia preparada
            mysqli_stmt_execute($stmt_check_duplicado);
            //mysqli_stmt_store_result almacena el resultado de una sentencia preparada
            mysqli_stmt_store_result($stmt_check_duplicado);
            //mysqli_stmt_num_rows obtiene el número de filas de un conjunto de resultados almacenados
            $existe_duplicado = mysqli_stmt_num_rows($stmt_check_duplicado) > 0;
            //mysqli_stmt_close cierra una sentencia preparada
            mysqli_stmt_close($stmt_check_duplicado);

            // si existe el duplicado, no inserta y muestra mensaje de error
            if ($existe_duplicado) {
                //echo un script de alerta y vuelve a la página anterior
                echo "<script>alert('Ya existe esa materia y profesor en ese espacio, turno, día y bloque horario.'); window.history.back();</script>";
                exit;
            } else {
                // inserta el nuevo horario
                // sql prepara la inserción en la tabla asocia
                $sql = "INSERT INTO asocia (id_asignatura, id_espacio, horario, id_profesor, turno, dia_semana) VALUES (?, ?, ?, ?, ?, ?)";
                //stmt prepara la consulta SQL
                $stmt = mysqli_prepare($con, $sql);
                //mysqli_stmt_bind_param vincula variables a una sentencia preparada como parámetros
                mysqli_stmt_bind_param($stmt, "iissss", $id_asignatura, $id_espacio, $horario, $id_profesor, $turno, $dia_semana);
                $ok = mysqli_stmt_execute($stmt);
                //mysqli_stmt_close cierra una sentencia preparada
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

// eliminar 
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    mysqli_query($con, "DELETE FROM asocia WHERE id_asocia = $id");
    echo "<script>alert('Horario eliminado correctamente.'); window.location.href='../HTML/registroDatos.php';</script>";
    exit;
}

// cerrar la conexion si esta está abierta
if (isset($con) && $con instanceof mysqli) {
    if (@$con->ping()) {
        $conexion_abierta = false;
    }
}
?>