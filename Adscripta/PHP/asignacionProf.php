<?php
// asignacionProf.php
require("../../PHP/conexion.php");
// Conectar a la base de datos
$con = conectar_bd();

// procesar el formulario POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener y validar datos del formulario
    $grupoId      = isset($_POST['grupo']) ? intval($_POST['grupo']) : null;
    $asignaturaId = isset($_POST['asignatura']) ? intval($_POST['asignatura']) : null;
    $profesorId   = isset($_POST['profesor']) ? intval($_POST['profesor']) : null;
    $espacioId    = isset($_POST['salon']) ? intval($_POST['salon']) : null;
    $dia          = isset($_POST['dia']) ? $_POST['dia'] : null;
    $horas        = isset($_POST['hora']) ? $_POST['hora'] : [];

    // Validar campos obligatorios
    $errores = [];
    if (!$grupoId)      $errores[] = "Grupo requerido.";
    if (!$asignaturaId) $errores[] = "Asignatura requerida.";
    if (!$profesorId)   $errores[] = "Profesor requerido.";
    if (!$espacioId)    $errores[] = "Espacio requerido.";
    if (!$dia)          $errores[] = "Día requerido.";
    if (empty($horas))  $errores[] = "Debe seleccionar al menos una hora.";

    // Si hay errores, redirige con mensaje
    if ($errores) {

        $errorMsg = implode(" ", $errores);
        header("Location: ../HTML/asignacion.php?error=" . urlencode($errorMsg));
        exit;
    }

    // Relacionar profesor y asignatura
    // Relacionar asignatura y grupo
    $sql1 = "INSERT IGNORE INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, "ii", $profesorId, $asignaturaId);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // Relacionar asignatura y grupo
    $sql2 = "INSERT IGNORE INTO tiene (id_asignatura, id_grupo) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, "ii", $asignaturaId, $grupoId);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    // Insertar horarios
    $sql3 = "INSERT INTO asocia (id_asignatura, id_espacio, horario) VALUES (?, ?, ?)";
    $stmt3 = mysqli_prepare($con, $sql3);

    // Insertar cada hora seleccionada
    foreach ($horas as $hora) {
        $fecha = date('Y-m-d');
        $horario = $fecha . ' ' . $hora . ':00';
        mysqli_stmt_bind_param($stmt3, "iis", $asignaturaId, $espacioId, $horario);
        mysqli_stmt_execute($stmt3);
    }
    mysqli_stmt_close($stmt3);

    // Redirigir con mensaje de éxito
    header("Location: ../HTML/asignacion.php?resultado=" . urlencode("Asignación realizada correctamente"));
    exit;
}
// Si no es POST, redirige con error
header("Location: ../HTML/asignacion.php?error=" . urlencode("Método no permitido"));
exit;
?>