<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Login/HTML/index.php");
    exit();
} else {
    $email = $_SESSION["email"];
    $usuario = $_SESSION["usuario"];
    $tipo_usuario = $_SESSION["tipo_usuario"];
    $horario = $_SESSION["horario"];
}
?>
