<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Usuario";
$saludo = "Bienvenido/a";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="header-left">
        <img src="img/logo.png" alt="Logo" class="logo">
        <div class="foto-perfil"></div>
        <div class="info-usuario">
            <h2>Adscrita</h2>
            <a href="#">Editar perfil</a>
            <a href="#">Cerrar sesión</a>
        </div>
    </div>
    <div class="menu-icon">&#9776;</div>
</header>

<!-- SECCIÓN DE SALUDO -->
<section class="seccion-saludo">
    <div class="fondo-saludo">
        <h1><?php echo $saludo; ?> <span class="resaltado"><?php echo htmlspecialchars($usuario); ?></span>!</h1>
    </div>
</section>

<!-- BARRA DESCRIPTIVA -->
<div class="barra-descriptiva">
    <p>Sistema Unificado de Gestión de Aulas y Recursos</p>
</div>

<!-- TABLERO PRINCIPAL -->
<section class="tablero-principal">
    <div class="grupo-tarjetas">
        <!-- 🔹 BOTÓN FUNCIONAL DE HORARIOS Y CLASES -->
        <a class="tarjeta-opcion" href="asignacion.php">
            <span>Horarios y<br>Clases</span>
        </a>

        <!-- 🔹 BOTÓN DE ANUNCIOS -->
        <a class="tarjeta-opcion" href="anuncios.php">
            <span>Anuncios</span>
        </a>

        <!-- 🔹 BOTÓN DE PROFESORES -->
        <a class="tarjeta-opcion" href="gestionUsr.php">
            <span>Profesores</span>
        </a>
    </div>
</section>

</body>
</html>
