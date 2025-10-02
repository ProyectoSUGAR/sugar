<?php
include '../../PHP/header.php';
require_once("../../PHP/conexion.php");
$conexion = conectar_bd();

session_start();
$id_usuario = $_SESSION['id_usuario'] ?? 0;

// Consulta el tipo de usuario
$tipo_usuario = '';
if ($id_usuario > 0) {
    $resultado_tipo = mysqli_query($conexion, "SELECT tipo_usuario FROM usuario WHERE id_usuario=$id_usuario");
    if ($fila_tipo = mysqli_fetch_assoc($resultado_tipo)) {
        $tipo_usuario = $fila_tipo['tipo_usuario'];
    }
}

// Solo permiten acceso "direccion" y "administrador"
if ($tipo_usuario !== 'direccion' && $tipo_usuario !== 'administrador') {
    echo "<script>alert('No tienes permiso para ver el historial de actividad.'); window.location.href='gestionUsr.php';</script>";
    if (isset($conexion) && $conexion instanceof mysqli) {
        if (@$conexion->ping()) {

        }
    }
    exit;
}

$consulta = "SELECT a.*, u.nombre, u.apellido FROM actividad a JOIN usuario u ON a.id_usuario = u.id_usuario ORDER BY a.fecha DESC LIMIT 100";
$resultado_actividad = mysqli_query($conexion, $consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Actividad</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="../../Css/historialActividad.css">
</head>
<body class="body-login">
    <div class="contenedor-historial-actividad">
        <h2 class="titulo-panel">Historial de Actividad</h2>
        <table class="tabla-historial-actividad">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($actividad = mysqli_fetch_assoc($resultado_actividad)): ?>
                <tr>
                    <td><?= $actividad['fecha'] ?></td>
                    <td><?= htmlspecialchars($actividad['nombre'] . ' ' . $actividad['apellido']) ?></td>
                    <td><?= htmlspecialchars($actividad['accion']) ?></td>
                    <td><?= htmlspecialchars($actividad['detalle']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="text-align:center; margin-top:24px;">
            <a href="gestionUsr.php" class="btn-volver-reporte">
                <i class="fa fa-arrow-left"></i> Volver a gestión de usuarios
            </a>
        </div>
    </div>
</body>
</html>
<?php
if (isset($conexion) && $conexion instanceof mysqli) {
    if (@$conexion->ping()) {

    }
}
?>