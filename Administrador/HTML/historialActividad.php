<?php
include '../../HEADERS/headerAA.php';
require_once("../PHP/funcHistorial.php");
$actividades = obtenerHistorialActividad();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Actividad</title>
    <link rel="stylesheet" href="../../Css/style.css">
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
                <?php foreach ($actividades as $actividad): ?>
                <tr>
                    <td><?= $actividad['fecha'] ?></td>
                    <td><?= htmlspecialchars($actividad['nombre'] . ' ' . $actividad['apellido']) ?></td>
                    <td><?= htmlspecialchars($actividad['accion']) ?></td>
                    <td><?= htmlspecialchars($actividad['detalle']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="volver-centrado">
            <a href="gestionUsr.php" class="btn-volver-reporte">
                <i class="fa fa-arrow-left"></i> Volver a gestión de usuarios
            </a>
        </div>
    </div>
</body>
</html>
