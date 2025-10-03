<?php
require_once '/PHP/conexion.php';

function mostrarNotificacionesUsuario($tipo_usuario) {
    $conn = conectar_bd();
    $sql = "SELECT mensaje, tipo, fecha FROM notificacion WHERE destinatario_tipo = ? OR destinatario_tipo = 'todos' ORDER BY fecha DESC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $tipo_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    echo '<div class="notificaciones-usuario">';
    echo '<h3>Notificaciones recientes</h3>';
    if ($result->num_rows > 0) {
        echo '<ul class="lista-notificaciones">';
        while ($row = $result->fetch_assoc()) {
            echo '<li><b>' . ucfirst($row['tipo']) . ':</b> ' . htmlspecialchars($row['mensaje']) . '<br><span class="fecha-noti">' . $row['fecha'] . '</span></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No hay notificaciones recientes.</p>';
    }
    echo '</div>';
    $stmt->close();
    $conn->close();
}
?>
