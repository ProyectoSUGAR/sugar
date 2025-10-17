<?php 
include '../../PHP/conexion.php';
$conn = conectar_bd();
if ($_SERVER["REQUIEST_METHOD"] === "POST") {
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    $id_salon = isset($_POST['id_salon']) ? intval($_POST['id_salon']) : 0;
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $hora_inicio = isset($_POST['hora_inicio']) ? $_POST['hora_inicio'] : '';
    $hora_fin = isset($_POST['hora_fin']) ? $_POST['hora_fin'] : '';

    if ($id_usuario > 0 && $id_salon > 0 && !empty($fecha) && !empty($hora_inicio) && !empty($hora_fin)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO reservas (id_usuario, id_salon, fecha, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iisss", $id_usuario, $id_salon, $fecha, $hora_inicio, $hora_fin);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Reserva realizada con éxito.'); window.location.href='../../Profesor/HTML/profesorReservas.php';</script>";
        } else {
            echo "<script>alert('Error al realizar la reserva.'); window.location.href='../../Profesor/HTML/profesorReservas.php';</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Por favor complete todos los campos.'); window.location.href='../../Profesor/HTML/profesorReservas.php';</script>";
    }
}
?>