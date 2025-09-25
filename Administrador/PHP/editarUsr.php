<?php
require_once("../../PHP/conexion.php");
$conexion = conectar_bd();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_usuario'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $tipo_usuario = $_POST['tipo_usuario'];
    mysqli_query($conexion, "UPDATE usuario SET nombre='$nombre', apellido='$apellido', correo='$correo', tipo_usuario='$tipo_usuario' WHERE id_usuario=$id_usuario");

    echo "<script>window.location.href='../HTML/gestionUsr.php';</script>";
    exit;
} else {

    header("Location: ../HTML/gestionUsr.php");
    exit;
}
?>