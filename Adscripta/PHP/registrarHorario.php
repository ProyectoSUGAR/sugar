<?php
require("../../PHP/conexion.php");
$con = conectar_bd();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null;
    $dia = isset($_POST['dia']) ? $_POST['dia'] : null;
    $turno = isset($_POST['turno']) ? $_POST['turno'] : null; // Solo para filtrar, no se guarda
    $bloque = isset($_POST['hora']) ? $_POST['hora'] : null;

    // Debes obtener estos valores del formulario si quieres que sean dinámicos
    $id_asignatura = 1; // Cambia por el valor real o agrega un select en el formulario
    $id_espacio = 1;    // Cambia por el valor real o agrega un select en el formulario

    if ($id_profesor && $dia && $bloque) {
        // Formatea el horario como fecha y hora (puedes ajustar según tu necesidad)
        $fecha = date('Y-m-d'); // Fecha actual
        $hora_inicio = explode(' - ', $bloque)[0];
        $horario = $fecha . ' ' . $hora_inicio . ':00';

        // Solo guarda los campos que existen en la tabla
        $sql = "INSERT INTO asocia (id_asignatura, id_espacio, horario) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $id_asignatura, $id_espacio, $horario);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($ok) {
            echo "<script>alert('Horario registrado correctamente'); window.location.href='../HTML/registroDatos.php';</script>";
        } else {
            echo "<script>alert('Error al registrar el horario'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Datos incompletos'); window.history.back();</script>";
    }
}

mysqli_close($con);
?>