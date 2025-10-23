<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();

// Verificar si es una petición POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Método no permitido");
}

// Debug - Imprimir los datos recibidos
error_log("Datos POST recibidos en procesarHorario.php:");
error_log(print_r($_POST, true));

// Obtener y validar los datos del formulario
$id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null;
$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$turno = isset($_POST['turno']) ? $_POST['turno'] : null;
$bloques = [];
if (isset($_POST['hora'])) {
    // puede venir como array (hora[]) o como string simple
    if (is_array($_POST['hora'])) $bloques = array_values(array_filter($_POST['hora']));
    else if (is_string($_POST['hora']) && $_POST['hora'] !== '') $bloques = [$_POST['hora']];
}
$id_asignatura = isset($_POST['id_asignatura']) ? intval($_POST['id_asignatura']) : null;
$id_espacio = isset($_POST['id_espacio']) ? intval($_POST['id_espacio']) : null;
$id_asocia = isset($_POST['id_asocia']) ? intval($_POST['id_asocia']) : null;
$id_grupo = isset($_POST['id_grupo']) && $_POST['id_grupo'] !== '' ? intval($_POST['id_grupo']) : null;

// Validar campos obligatorios
if (!$id_profesor || !$dia || empty($bloques) || !$id_asignatura || !$id_espacio || !$turno) {
    die("Error: Todos los campos son obligatorios");
}

try {
    // Iniciar transacción
    mysqli_begin_transaction($con);
    
    // 1. Asegurar que el profesor exista en la tabla profesor
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

    // 2. Asegurar la relación profesor-asignatura
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

    // 3. Preparar sentencias para verificación e inserción
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

    // Si se está editando: actualizamos la fila id_asocia con la primera hora y creamos nuevas filas para las horas restantes
    $first = true;
    foreach ($bloques as $idx => $b) {
        // comprobar duplicado para este bloque
        mysqli_stmt_bind_param($stmt_check, "isssisss", $id_espacio, $turno, $dia, $b, $id_profesor, $turno, $dia, $b);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            throw new Exception("Ya existe una clase en el bloque '$b' para el espacio o profesor seleccionado.");
        }
        mysqli_stmt_free_result($stmt_check);

        if ($id_asocia && $first) {
            // actualizar la fila existente con la primera hora
            mysqli_stmt_bind_param($stmt_update, "iisssisi", $id_asignatura, $id_espacio, $b, $id_profesor, $id_grupo, $turno, $dia, $id_asocia);
            if (!mysqli_stmt_execute($stmt_update)) {
                throw new Exception('Error actualizando horario: ' . mysqli_stmt_error($stmt_update));
            }
            $first = false;
        } else {
            // insertar nueva fila
            mysqli_stmt_bind_param($stmt_insert, "iississ", $id_asignatura, $id_espacio, $b, $id_profesor, $id_grupo, $turno, $dia);
            if (!mysqli_stmt_execute($stmt_insert)) {
                throw new Exception('Error insertando horario: ' . mysqli_stmt_error($stmt_insert));
            }
        }
    }

    // cerrar statements
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_insert);
    mysqli_stmt_close($stmt_update);

    // Confirmar la transacción
    mysqli_commit($con);
    
    // Redirigir con mensaje de éxito
    header("Location: ../../Adscripta/HTML/registroDatos.php?success=1");
    exit;

} catch (Exception $e) {
    // Revertir la transacción si hay error
    mysqli_rollback($con);
    die("Error: " . $e->getMessage());
} finally {
    // Cerrar la conexión
    if (isset($con)) {
        mysqli_close($con);
    }
}
?>