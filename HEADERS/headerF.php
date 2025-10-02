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
            <strong >Funcionario</strong>
            <br>
            <a  class="p1" href="/Login/HTML/index.php">Cerrar sesión</a>
            
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
     <nav id="nav" class="main-nav">
        <div class="nav-links">
      <a class="link-item" href="/Funcionario/HTML/dashboardF.php">Inicio</a>
    <a class="link-item" href="#contenedor-tablas-horarios">Horarios</a>
      <a class="link-item" href="/Funcionario/HTML/anuncios.php">Anuncios</a>
    </div>
</nav>
    <!-- Inclusión del script que gestiona la funcionalidad del menú hamburguesa -->
    <script src="/JS/menuHamb.js"></script>
</header>