<?php
session_start();
require("../../PHP/conexion.php");

$conexionBD = conectar_bd();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $grupoId       = isset($_POST['grupo']) ? intval($_POST['grupo']) : null;
    $asignaturaId  = isset($_POST['asignatura']) ? intval($_POST['asignatura']) : null;
    $profesorId    = isset($_POST['profesor']) ? intval($_POST['profesor']) : null;
    $espacioId     = isset($_POST['salon']) ? intval($_POST['salon']) : null;
    $diaSeleccion  = isset($_POST['dia']) ? trim($_POST['dia']) : null;
    $horasLista    = isset($_POST['hora']) ? $_POST['hora'] : [];
    $turnoSeleccion = isset($_POST['turno']) ? trim($_POST['turno']) : null;

    if ($grupoId && $asignaturaId && $profesorId && $espacioId && $diaSeleccion && !empty($horasLista) && $turnoSeleccion) {
        try {
            mysqli_begin_transaction($conexionBD);

            // Relación profesor - asignatura
            $sqlProfAsig = "INSERT IGNORE INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
            $stmt = mysqli_prepare($conexionBD, $sqlProfAsig);
            mysqli_stmt_bind_param($stmt, "ii", $profesorId, $asignaturaId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Relación grupo - asignatura
            $sqlRelacion = "INSERT IGNORE INTO tiene (id_asignatura, id_grupo) VALUES (?, ?)";
            $stmt = mysqli_prepare($conexionBD, $sqlRelacion);
            mysqli_stmt_bind_param($stmt, "ii", $asignaturaId, $grupoId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Relación asignatura - espacio (con horario) para cada hora seleccionada
            $sqlAsocia = "INSERT INTO asocia (id_asignatura, id_espacio, horario, turno, bloque) VALUES (?, ?, ?, ?, 1)";
            foreach ($horasLista as $hora) {
                $horario = date("Y-m-d H:i:s", strtotime("next $diaSeleccion $hora"));
                $stmt = mysqli_prepare($conexionBD, $sqlAsocia);
                mysqli_stmt_bind_param($stmt, "iiss", $asignaturaId, $espacioId, $horario, $turnoSeleccion);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            mysqli_commit($conexionBD);

            echo json_encode([
                "status" => "success",
                "message" => "Asignación guardada correctamente"
            ]);
        } catch (Exception $e) {
            mysqli_rollback($conexionBD);
            echo json_encode([
                "status" => "error",
                "message" => "Error al guardar: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Datos incompletos en el formulario"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Acceso inválido"
    ]);
}

mysqli_close($conexionBD);
?>
