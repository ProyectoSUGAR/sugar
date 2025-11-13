<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Usuario";
$saludo = "Bienvenido/a";
?>
<section class="seccion-saludo">
    <div class="fondo-saludo">
        <h1><?php echo $saludo; ?> <span class="resaltado"><?php echo htmlspecialchars($usuario); ?></span>!</h1>
    </div>
</section>
<div class="barra-descriptiva">
    <p>Sistema Unificado de Gestión de Aulas y Recursos</p>
</div>
<section class="tablero-principal">
    <div class="grupo-tarjetas">
        <a class="tarjeta-opcion" href="#contenedor-tablas-horarios">
            <span>Horarios y<br />clases</span>
        </a>
        <a class="tarjeta-opcion" href="../../Administrador/HTML/anuncios.php">
            <span>Anuncios</span>
        </a>
        <a class="tarjeta-opcion" href="#">
            <span>Profesores</span>
        </a>
    </div>
</section>
