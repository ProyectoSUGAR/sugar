<?php
//notificaciones de usuario
function mostrarNotificacionesUsuario($tipo_usuario) {
    require_once __DIR__ . '../../PHP/conexion.php';
    $conn = conectar_bd();

    // consulta para obtener las notificaciones relevantes
    $sql = "SELECT mensaje, tipo, fecha FROM notificacion WHERE destinatario_tipo = ? OR destinatario_tipo = 'todos' ORDER BY fecha DESC LIMIT 5";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $tipo_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo '<div class="notificaciones-usuario">';
        echo '<h3>Notificaciones Recientes</h3>';
        echo '<ul>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li>';
            echo '<strong>[' . htmlspecialchars($row['tipo']) . ']</strong> ';
            echo htmlspecialchars($row['mensaje']) . ' ';
            echo '<em>(' . htmlspecialchars($row['fecha']) . ')</em>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<p>No hay notificaciones recientes.</p>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

?>