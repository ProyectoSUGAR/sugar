<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idGrupo      = isset($_POST['grupo']) ? intval($_POST['grupo']) : null;
    $idAsignatura = isset($_POST['asignatura']) ? intval($_POST['asignatura']) : null;
    $idProfesor   = isset($_POST['profesor']) ? intval($_POST['profesor']) : null;
    $errores = [];
    if (!$idGrupo)      $errores[] = "El grupo es obligatorio.";
    if (!$idAsignatura) $errores[] = "La asignatura es obligatoria.";
    if (!$idProfesor)   $errores[] = "El profesor es obligatorio.";
    if ($errores) {
        $mensajeError = implode(" ", $errores);
        header("Location: ../..//HTML/asignacion.php?error=" . urlencode($mensajeError));
        exit;
    }
    $sql1 = "INSERT IGNORE INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, "ii", $idProfesor, $idAsignatura);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);
    $sql2 = "INSERT IGNORE INTO tiene (id_asignatura, id_grupo) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, "ii", $idAsignatura, $idGrupo);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
if (isset($conn) && $conn instanceof mysqli) {
    if (@$conn->ping()) {
    }
}
    header("Location: ../../HTML/asignacion.php?resultado=" . urlencode("Asignación realizada correctamente."));
    exit;
}
header("Location: ../../HTML/asignacion.php?error=" . urlencode("Método no permitido."));
exit;
?>
