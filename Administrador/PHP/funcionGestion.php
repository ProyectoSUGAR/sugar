<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();

session_start();
$id_usuario_admin = $_SESSION['id_usuario'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['cambiar_estado'])) {
        $id_usuario = intval($_POST['id_usuario']);
        $estado_actual = $_POST['estado_usuario'];
        $nuevo_estado = $estado_actual === 'activo' ? 'inactivo' : 'activo';
        mysqli_query($con, "UPDATE usuario SET estado_usuario='$nuevo_estado' WHERE id_usuario=$id_usuario");
        $accion = $nuevo_estado === 'activo' ? 'Activar usuario' : 'Desactivar usuario';
        $detalle = "Usuario ID $id_usuario cambiado a estado '$nuevo_estado'";
        $detalle_sql = mysqli_real_escape_string($con, $detalle);
        if ($id_usuario_admin > 0) {
            $fecha = date('Y-m-d H:i:s');
            $sql = "INSERT INTO actividad (id_usuario, accion, detalle, fecha) VALUES ($id_usuario_admin, '$accion', '$detalle_sql', '$fecha')";
            mysqli_query($con, $sql);
        }
        echo "<script>window.location.href='../HTML/gestionUsr.php';</script>";
        exit;
    }
    if (isset($_POST['eliminar_usuario'])) {
        $id_usuario = intval($_POST['id_usuario']);
        mysqli_query($con, "DELETE FROM usuario WHERE id_usuario=$id_usuario");
        $accion = 'Eliminar usuario';
        $detalle = "Usuario ID $id_usuario eliminado";
        $detalle_sql = mysqli_real_escape_string($con, $detalle);
        if ($id_usuario_admin > 0) {
            $fecha = date('Y-m-d H:i:s');
            $sql = "INSERT INTO actividad (id_usuario, accion, detalle, fecha) VALUES ($id_usuario_admin, '$accion', '$detalle_sql', '$fecha')";
            mysqli_query($con, $sql);
        }
        echo "<script>window.location.href='../HTML/gestionUsr.php';</script>";
        exit;
    }
}

if (isset($con) && $con instanceof mysqli) {
    if (@$con->ping()) {

    }
}
?>