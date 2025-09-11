<?php
// Verifica si la sesión aún no ha sido iniciada
// session_status() devuelve el estado actual de la sesión (PHP_SESSION_NONE indica que no hay sesión activa)
if (session_status() === PHP_SESSION_NONE) {
    // Inicia una nueva sesión para el usuario
    session_start();
}

// Obtiene el nombre de usuario almacenado en la sesión
// isset() verifica si la variable 'usuario' está definida y no es null
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Usuario";

// Define el saludo predeterminado que se mostrará en pantalla
$saludo = "Bienvenido/a";
?>

<!-- Sección visual que muestra el saludo personalizado al usuario -->
<section class="seccion-saludo">
    <div class="fondo-saludo">
        <!-- Muestra el saludo seguido del nombre del usuario, escapado para evitar inyecciones -->
        <h1><?php echo $saludo; ?> <span class="resaltado"><?php echo htmlspecialchars($usuario); ?></span>!</h1>
    </div>
</section>

<!-- Barra descriptiva que presenta el nombre completo del sistema -->
<div class="barra-descriptiva">
    <p>Sistema Unificado de Gestión de Aulas y Recursos</p>
</div>

<!-- Sección principal del tablero con accesos rápidos a funcionalidades clave -->
<section class="tablero-principal">
    <div class="grupo-tarjetas">
        <!-- Tarjeta que redirige a la sección de horarios y clases -->
        <a class="tarjeta-opcion" href="#contenedor-tablas-horarios">
            <span>Horarios y<br />clases</span>
        </a>
        <!-- Tarjeta que redirige a la sección de anuncios -->
        <a class="tarjeta-opcion" href="#">
            <span>Anuncios</span>
        </a>
        <!-- Tarjeta que redirige a la sección de profesores -->
        <a class="tarjeta-opcion" href="#">
            <span>Profesores</span>
        </a>
    </div>
</section>