<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$avatar_url = '../../images/perfiles/perfilpordefecto.jpg';
require_once(__DIR__ . '/../PHP/conexion.php');
if (!empty($_SESSION['id_usuario'])) {
    $db = function_exists('conectar_bd') ? conectar_bd() : null;
    $uid = (int) $_SESSION['id_usuario'];
    if (!empty($_SESSION['avatar_url'])) {
        $avatar_url = $_SESSION['avatar_url'];
    } elseif ($db) {
        $q = $db->prepare("SELECT url FROM imagen WHERE id_recurso = ? AND tipo = 'perfil' ORDER BY id_imagen DESC LIMIT 1");
        if ($q) {
            $q->bind_param('i', $uid);
            $q->execute();
            $res = $q->get_result();
            $row = $res->fetch_assoc();
            if ($row && !empty($row['url'])) {
                $avatar_url = $row['url'];
            }
        }
    }
    if (!empty($avatar_url) && strpos($avatar_url, '//') === false) {
        $avatar_url = preg_replace('#^\.\./+#', '/', $avatar_url);
        if ($avatar_url[0] !== '/') $avatar_url = '/' . ltrim($avatar_url, '/');
        $avatar_url = preg_replace('#/sugar-main(/sugar-main)+#', '/sugar-main', $avatar_url);
    }
}
?>
<!-- Enlace al archivo de estilos CSS principal del sistema -->
<link rel="stylesheet" href="../../Css/style.css" />
<!-- Cabecera institucional de la página -->
<header class="cabecera-institucional">
    <!-- Imagen del logo institucional ubicada a la izquierda -->
    <img src="../../Images/Logo22-removebg-preview.png" alt="Logo" class="logo-app" />
    <!-- Bloque central que muestra la información del usuario -->
    <div class="caja-usuario">
        <!-- Avatar del usuario -->
        <div class="avatar-usuario">
            <?php
            $avatarSrc = '../../images/perfiles/perfilpordefecto.jpg';
            if (!empty($_SESSION['avatar_url'])) {
                $candidate = $_SESSION['avatar_url'];
            } else {
                $candidate = $avatar_url ?? '';
            }
            if (!empty($candidate)) {
                if (strpos($candidate, 'http://') === 0 || strpos($candidate, 'https://') === 0) {
                    $avatarSrc = $candidate;
                } else {
                    $avatarSrc = '../../images/perfiles/' . basename($candidate);
                }
            }
            ?>
            <img src="<?php echo htmlspecialchars($avatarSrc); ?>" alt="Avatar" class="avatar-img" onerror="this.src='../../images/perfiles/perfilpordefecto.jpg'">
            <!-- avatarSrc: <?php echo htmlspecialchars($avatarSrc); ?> -->
            <script>document.addEventListener('DOMContentLoaded', function(){ const a = document.querySelector('.caja-usuario .avatar-img'); if (a) console.log('AVATAR SRC:', a.src); });</script>
        </div>
        <!-- Nombre del sistema o usuario actual -->
        <div class="datos-usuario">
            <strong >Director</strong>
            <br>
            <a  class="p1" href="../../PHP/editarPerfil.php">Editar perfil</a>
            <br>
            <a  class="p1" href="../../Login/HTML/ingreso.php">Cerrar sesión</a>
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
      <a class="link-item" href="../../Direccion/HTML/dashboardD.php">Inicio</a>
      <a class="link-item" href="../../Direccion/HTML/asignacion.php">Asignar clases</a>
      <a class="link-item" href="../../Direccion/HTML/asignacionAsig.php">Registro de Horarios</a>
      <a class="link-item" href="../../Direccion/HTML/asignacionGrup.php">Registrar grupos</a>
    <a class="link-item" href="../../Direccion/HTML/gestionUsr.php">Gestionar usuarios</a>
    <a class="link-item" href="#contenedor-tablas-horarios">Horarios</a>
      <a class="link-item" href="../../Direccion/HTML/anuncios.php">Anuncios</a>
            <!-- Alerta -->
           <div class="alerta">
        <H2 class="h2alerta">Comunicado oficial</H2>
        <div class="textoalerta">
            <h3 class="h3alerta">Aquí va el texto.</h3>
        </div>
    </div>
</nav>
    <!-- Inclusión del script que gestiona la funcionalidad del menú hamburguesa -->
    <script src="/JS/menuHamb.js"></script>
</header>
<script>
fetch('/PHP/notificaciones_usuario.php?tipo_usuario=direccion')
    .then(response => response.json())
    .then(data => {
        const alertaTexto = document.querySelector('.textoalerta h3');
        if (data.length > 0) {
            alertaTexto.textContent = data[0].mensaje; // Mostrar la última notificación
        } else {
            alertaTexto.textContent = 'No hay notificaciones.';
        }
    })
    .catch(error => console.error('Error al cargar notificaciones:', error));
</script>