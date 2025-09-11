<!-- Enlace al archivo de estilos CSS principal del sistema -->
<link rel="stylesheet" href="/Css/style.css" />

<!-- Cabecera institucional de la página -->
<header class="cabecera-institucional">
    <!-- Imagen del logo institucional ubicada a la izquierda -->
    <img src="/Images/Logo22-removebg-preview.png" alt="Logo" class="logo-app" />

    <!-- Bloque central que muestra la información del usuario -->
    <div class="caja-usuario">
        <!-- Avatar del usuario (decorativo, sin contenido accesible) -->
        <div class="avatar-usuario" aria-hidden="true"></div>
        <!-- Nombre del sistema o usuario actual -->
        <div class="datos-usuario">
            <strong>S.U.G.A.R</strong>
        </div>
    </div>

    <!-- Botón de menú tipo hamburguesa ubicado a la derecha -->
    <button class="boton-menu" id="btnHamburguesa" aria-label="Abrir menú principal">
        <!-- Líneas del ícono de hamburguesa -->
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Lista de opciones del menú hamburguesa (oculta por defecto) -->
    <ul id="menuOpciones" class="opciones-menu" style="display:none;">
        <li><a href="#">Inicio</a></li>         <!-- Opción para volver al inicio -->
        <li><a href="#">Horarios</a></li>       <!-- Opción para ver los horarios -->
        <li><a href="#">Anuncios</a></li>       <!-- Opción para acceder a los anuncios -->
        <li><a href="#">Profesores</a></li>     <!-- Opción para ver el listado de profesores -->
        <li><a href="#">Cerrar sesión</a></li>  <!-- Opción para cerrar la sesión actual -->
    </ul> 

    <!-- Inclusión del script que gestiona la funcionalidad del menú hamburguesa -->
    <script src="/JS/menuHamb.js"></script>
</header>