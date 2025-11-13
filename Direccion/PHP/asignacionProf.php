<?php
// Función para validar datos de asignación profesor-asignatura-grupo
function validar_datos_asignacion($idGrupo, $idAsignatura, $idProfesor) {
    $errores = [];
    if (!$idGrupo) $errores[] = "El grupo es obligatorio.";
    if (!$idAsignatura) $errores[] = "La asignatura es obligatoria.";
    if (!$idProfesor) $errores[] = "El profesor es obligatorio.";
    return $errores;
}

// Función para asignar profesor a asignatura
function asignar_profesor_asignatura($conn, $idProfesor, $idAsignatura) {
    $sql = "INSERT IGNORE INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idAsignatura);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Función para asignar asignatura a grupo
function asignar_asignatura_grupo($conn, $idAsignatura, $idGrupo) {
    $sql = "INSERT IGNORE INTO tiene (id_asignatura, id_grupo) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idAsignatura, $idGrupo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Función para procesar asignación profesor-asignatura-grupo
function procesar_asignacion_profesor() {
    require_once("../../PHP/conexion.php");
    $conn = conectar_bd();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: ../../HTML/asignacion.php?error=" . urlencode("Método no permitido."));
        exit;
    }

    $idGrupo = isset($_POST['grupo']) ? intval($_POST['grupo']) : null;
    $idAsignatura = isset($_POST['asignatura']) ? intval($_POST['asignatura']) : null;
    $idProfesor = isset($_POST['profesor']) ? intval($_POST['profesor']) : null;

    $errores = validar_datos_asignacion($idGrupo, $idAsignatura, $idProfesor);
    if ($errores) {
        $mensajeError = implode(" ", $errores);
        header("Location: ../../HTML/asignacion.php?error=" . urlencode($mensajeError));
        exit;
    }

    asignar_profesor_asignatura($conn, $idProfesor, $idAsignatura);
    asignar_asignatura_grupo($conn, $idAsignatura, $idGrupo);

    header("Location: ../../HTML/asignacion.php?resultado=" . urlencode("Asignación realizada correctamente."));
    exit;
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    procesar_asignacion_profesor();
}
