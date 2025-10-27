<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['id_usuario'])) {
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
    $q = $db->prepare("SELECT url FROM imagen WHERE id_recurso = ? AND tipo = 'perfil' ORDER BY id_imagen DESC LIMIT 1");
    $q->bind_param('i', $uid);
    if ($q) {
        $q->execute();
        $res = $q->get_result();
        $row = $res->fetch_assoc();
        if ($row && !empty($row['url'])) {
            $url = $row['url'];
            if (strpos($url, '../') === 0) {
                $url = substr($url, 3); // Remover ../
            } elseif (strpos($url, '/images/') === 0) {
                $url = substr($url, 8); // Remover /images/
            }
            $avatar_url = '../../Images/' . $url;
        }
    }
}
?>
<link rel="stylesheet" href="../../Css/style.css" />
<header class="cabecera-institucional">
    <img src="../../Images/Logo22-removebg-preview.png" alt="Logo" class="logo-app" />
    <div class="caja-usuario">
        <div class="avatar-usuario">
            <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" class="avatar-img" onerror="this.src='../../Images/perfiles/perfilpordefecto.jpg'">
        </div>
        <div class="datos-usuario">
            <strong>Administrador</strong>
            <br>
            <a class="p1" href="../../PHP/editarPerfil.php">Editar perfil</a>
            <br>
            <a class="p1" href="../../Login/HTML/ingreso.php">Cerrar sesión</a>
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
            <a class="link-item" href="../../Administrador/HTML/dashboardAr.php">Inicio</a>
            <a class="link-item" href="../../Administrador/HTML/asignacionAsig.php">Registro de Asignaturas</a>
            <a class="link-item" href="../../Administrador/HTML/asignacionGrup.php">Registrar grupos</a>
            <a class="link-item" href="../../Administrador/HTML/gestionUsr.php">Gestionar usuarios</a>
            <a class="link-item" href="../../Administrador/HTML/historialActividad.php">Historial de actividad</a>
                        <a class="link-item" href="../../Administrador/HTML/registroDatos.php">Registro de Datos</a>
            <!-- Alerta -->
           <div class="alerta">
        <H2 class="h2alerta">Comunicado oficial</H2>
        <div class="textoalerta">
            <h3 class="h3alerta">Aquí va el texto.</h3>
        </div>
    </div>
</nav>
    <!-- Inclusión del script que gestiona la funcionalidad del menú hamburguesa -->
    <script src="../../JS/menuHamb.js"></script>
</header>
<script>
fetch('../../PHP/notificaciones_usuario.php?tipo_usuario=administrador&id_usuario=<?php echo $uid; ?>')
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