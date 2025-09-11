<?php
// Inicia la sesión para acceder a las variables de sesión
session_start();

// Verifica si el usuario ha iniciado sesión correctamente
// Si no existe la variable de sesión 'id_usuario', se redirige al login
if (!isset($_SESSION['id_usuario'])) {
    // Redirección al formulario de inicio de sesión
    header("Location: /Login/HTML/index.php");

    // Finaliza la ejecución del script para evitar que se cargue contenido protegido
    exit();
} else {
    // Si el usuario está autenticado, se recuperan sus datos desde la sesión

    // Correo electrónico del usuario
    $email = $_SESSION["email"];

    // Nombre de usuario
    $usuario = $_SESSION["usuario"];

    // Tipo de usuario (por ejemplo: profesor, secretario, administrador)
    $tipo_usuario = $_SESSION["tipo_usuario"];

    // Horario asignado al usuario
    $horario = $_SESSION["horario"];
}
?>