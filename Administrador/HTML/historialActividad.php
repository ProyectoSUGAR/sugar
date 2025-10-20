<?php
include '../../HEADERS/headerAA.php';
require_once("../PHP/funcHistorial.php");
session_start();

$actividades = obtenerHistorialActividad();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Actividad</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="../../Css/historialActividad.css">
    <style>
        .contenedor-historial-actividad {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .titulo-panel {
            color: #333;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2em;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }

        .tabla-historial-actividad {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: #fff;
        }

        .tabla-historial-actividad th {
            background: #007bff;
            color: white;
            padding: 1rem;
            text-align: left;
        }

        .tabla-historial-actividad td {
            padding: 0.8rem;
            border-bottom: 1px solid #ddd;
        }

        .tabla-historial-actividad tr:hover {
            background-color: #f5f5f5;
        }

        .btn-volver-reporte {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-volver-reporte:hover {
            background-color: #0056b3;
        }

        .fa-arrow-left {
            margin-right: 0.5rem;
        }
    </style>
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
        <div style="text-align:center; margin-top:24px;">
            <a href="gestionUsr.php" class="btn-volver-reporte">
                <i class="fa fa-arrow-left"></i> Volver a gestión de usuarios
            </a>
        </div>
    </div>
</body>
</html>