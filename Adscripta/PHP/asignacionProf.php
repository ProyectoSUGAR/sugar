<?php
// asignacionProf.php
require_once("../../PHP/conexion.php");
// Conexión a la base de datos
$con = conectar_bd();

// Procesar el formulario POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener y validar datos del formulario
    $idGrupo      = isset($_POST['grupo']) ? intval($_POST['grupo']) : null;
    $idAsignatura = isset($_POST['asignatura']) ? intval($_POST['asignatura']) : null;
    $idProfesor   = isset($_POST['profesor']) ? intval($_POST['profesor']) : null;
    $idEspacio    = isset($_POST['salon']) ? intval($_POST['salon']) : null;
    $dia          = isset($_POST['dia']) ? $_POST['dia'] : null;
    $horas        = isset($_POST['hora']) ? $_POST['hora'] : [];

    // Validar campos obligatorios
    $errores = [];
    if (!$idGrupo)      $errores[] = "El grupo es obligatorio.";
    if (!$idAsignatura) $errores[] = "La asignatura es obligatoria.";
    if (!$idProfesor)   $errores[] = "El profesor es obligatorio.";
    if (!$idEspacio)    $errores[] = "El espacio es obligatorio.";
    if (!$dia)          $errores[] = "El día es obligatorio.";
    if (empty($horas))  $errores[] = "Debes seleccionar al menos una hora.";

    // Si hay errores, redirige con mensaje
    if ($errores) {
        $mensajeError = implode(" ", $errores);
        header("Location: ../HTML/asignacion.php?error=" . urlencode($mensajeError));
        exit;
    }

    // Relacionar profesor y asignatura
    $sql1 = "INSERT IGNORE INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, "ii", $idProfesor, $idAsignatura);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // Relacionar asignatura y grupo
    $sql2 = "INSERT IGNORE INTO tiene (id_asignatura, id_grupo) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, "ii", $idAsignatura, $idGrupo);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    // Insertar horarios con la fecha del próximo día seleccionado
    $sql3 = "INSERT INTO asocia (id_asignatura, id_espacio, horario) VALUES (?, ?, ?)";
    $stmt3 = mysqli_prepare($con, $sql3);

    // Calcular la fecha del próximo día seleccionado
    $diasMap = [
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6,
        'Sunday' => 7
    ];
    $hoy = date('N');
    $target = isset($diasMap[$dia]) ? $diasMap[$dia] : 1;
    $diff = ($target - $hoy + 7) % 7;
    $fecha = date('Y-m-d', strtotime("+{$diff} days"));

    foreach ($horas as $hora) {
        $horario = $fecha . ' ' . $hora . ':00';
        mysqli_stmt_bind_param($stmt3, "iis", $idAsignatura, $idEspacio, $horario);
        mysqli_stmt_execute($stmt3);
    }
    mysqli_stmt_close($stmt3);
if (isset($conn) && $conn instanceof mysqli) {
    if (@$conn->ping()) {

    }
}

    // Redirigir con mensaje de éxito
    header("Location: ../HTML/asignacion.php?resultado=" . urlencode("Asignación realizada correctamente."));
    exit;
}
// Si no es POST, redirige con error
header("Location: ../HTML/asignacion.php?error=" . urlencode("Método no permitido."));
exit;
?>