<?php
function crearAsignatura($conn, $nombre) {
    $nombre = trim($nombre);
    if ($nombre !== '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO asignatura (nombre) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $nombre);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($ok) {
            echo "<script>alert('Asignatura registrada correctamente.'); window.location.href='asignacionAsig.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error al registrar la asignatura.');</script>";
        }
    } else {
        echo "<script>alert('El nombre de la asignatura es obligatorio.');</script>";
    }
}

function editarAsignatura($conn, $id, $nombre) {
    $id = intval($id);
    $nombre = trim($nombre);
    if ($nombre !== '') {
        $stmt = mysqli_prepare($conn, "UPDATE asignatura SET nombre=? WHERE id_asignatura=?");
        mysqli_stmt_bind_param($stmt, "si", $nombre, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($ok) {
            echo "<script>alert('Asignatura editada correctamente.'); window.location.href='asignacionAsig.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error al editar la asignatura.');</script>";
        }
    } else {
        echo "<script>alert('El nombre de la asignatura es obligatorio.');</script>";
    }
}

function eliminarAsignatura($conn, $id) {
    $id = intval($id);

    // Elimina relaciones en asocia
    $stmt_rel1 = mysqli_prepare($conn, "DELETE FROM asocia WHERE id_asignatura=?");
    mysqli_stmt_bind_param($stmt_rel1, "i", $id);
    mysqli_stmt_execute($stmt_rel1);
    mysqli_stmt_close($stmt_rel1);

    // Elimina relaciones en tiene
    $stmt_rel2 = mysqli_prepare($conn, "DELETE FROM tiene WHERE id_asignatura=?");
    mysqli_stmt_bind_param($stmt_rel2, "i", $id);
    mysqli_stmt_execute($stmt_rel2);
    mysqli_stmt_close($stmt_rel2);

    // Ahora elimina la asignatura
    $stmt = mysqli_prepare($conn, "DELETE FROM asignatura WHERE id_asignatura=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        echo "<script>alert('Asignatura eliminada correctamente.'); window.location.href='asignacionAsig.php';</script>";
        exit;
    } else {
        echo "<script>alert('No se puede eliminar la asignatura porque está relacionada con otros datos.'); window.location.href='asignacionAsig.php';</script>";
        exit;
    }
}

function obtenerAsignaturas($conn) {
    $asignaturas = [];
    $result = mysqli_query($conn, "SELECT id_asignatura, nombre FROM asignatura ORDER BY nombre ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $asignaturas[] = $row;
    }
    return $asignaturas;
}
?>