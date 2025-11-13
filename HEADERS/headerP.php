<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../Login/HTML/ingreso.php');
    exit();
}
if ($_SESSION['tipo_usuario'] !== 'profesor') {
    header('Location: ../../Login/HTML/ingreso.php');
    exit();
}
$avatar_url = '../../Images/perfiles/perfilpordefecto.jpg';
require_once(__DIR__ . '/../PHP/conexion.php');
$db = function_exists('conectar_bd') ? conectar_bd() : null;
$uid = (int) $_SESSION['id_usuario'];
if (!empty($_SESSION['avatar_url'])) {
    $avatar_url = $_SESSION['avatar_url'];
} elseif ($db) {
    $q = $db->prepare("SELECT url FROM usuario_imagen WHERE id_usuario = ? AND tipo = 'perfil' ORDER BY id_imagen DESC LIMIT 1");
    $q->bind_param('i', $uid);
    if ($q) {
        $q->execute();
        $res = $q->get_result();
        $row = $res->fetch_assoc();
        if ($row && !empty($row['url'])) {
            $url = $row['url'];
            $url = preg_replace('#^\.{1,2}/+#', '', $url);
            $url = preg_replace('#^/+#', '', $url);
            $avatar_url = '../../' . $url;
        }
    }
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="../../Css/style.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<header class="cabecera-institucional">
    <img src="../../Images/Logo22-removebg-preview.png" alt="Logo" class="logo-app" />
    <div class="caja-usuario">
        <div class="avatar-usuario">
            <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" class="avatar-img" onerror="this.src='../../Images/perfiles/perfilpordefecto.jpg'">
        </div>
        <div class="datos-usuario">
            <strong>Profesor</strong>
            <br>
            <a class="p1" href="../../PHP/editarPerfil.php">Editar perfil</a>
            <br>
            <a class="p1" href="../../Login/HTML/ingreso.php">Cerrar sesión</a>
        </div>
    </div>
    <div class="header-right">
        <button class="notifications-icon" id="notificationsBtn" aria-label="Ver notificaciones">
            <i class="fas fa-envelope"></i>
        </button>
        <button class="boton-menu" id="btnHamburguesa" aria-label="Abrir menú principal">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="notifications-dropdown" id="notificationsDropdown"></div>
    </div>
    <nav id="nav" class="main-nav">
        <div class="nav-links">
            <a class="link-item" href="../../Profesor/HTML/reservarEspacio.php">Reservar espacios</a>
            <a class="link-item" href="../../Profesor/HTML/reservarEspacio.php">Reservas</a>
            <a class="link-item" href="../../Profesor/HTML/profesores.php">Profesores</a>
        </div>
    </nav>
    <script>
        var tipoUsuario = '<?php echo $_SESSION['tipo_usuario']; ?>';
        var idUsuario = <?php echo $_SESSION['id_usuario']; ?>;
    </script>
    <script src="../../JS/menuHamb.js"></script>
</header>
