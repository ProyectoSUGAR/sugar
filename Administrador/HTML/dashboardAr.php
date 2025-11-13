<?php 
include '../../HEADERS/headerAA.php'; 
// Cargar funciones de estadísticas y conexión para renderizar números desde el servidor
require_once('../PHP/datosEstadisticos.php');
require_once('../../PHP/conexion.php');
$conn = conectar_bd();
$alumnos = obtener_estadisticas_alumnos($conn);
$profesores = obtener_estadisticas_profesores($conn);
$grupos = obtener_estadisticas_grupos($conn);
$secretarios = obtener_estadisticas_secretarios($conn);
$reservas_pendientes = obtener_estadisticas_reservas_pendientes($conn);
$graficoData = obtener_datos_grafico_clases($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Secretaria</title>
    <link rel="stylesheet" href="../../Css/style.css" />
</head>
<body>
    <main>
        <?php include '../../PHP/bienvenida.php'; ?>
        <section class="bloque-estadisticas">
            <h2 class="titulo-estadisticas" id="estadisticas">Estadísticas</h2>
            <div class="estadisticas-grid">
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="alumnos"><?php echo (int)$alumnos; ?></div>
                    <div class="estadistica-label">alumnos<br>registrados</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="profesores"><?php echo (int)$profesores; ?></div>
                    <div class="estadistica-label">Profesores<br>registrados</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="grupos"><?php echo (int)$grupos; ?></div>
                    <div class="estadistica-label">Grupos<br>registrados</div>
                </div>
                <div class="estadistica-item estadistica-grafico" style="grid-column: 2 / span 2; grid-row: 1 / span 2; padding: 20px; background-color: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 15px; text-align: center;">Distribución de Clases</h3>
                    <div id="container" style="width: 100%; height: 400px;"></div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="secretarios"><?php echo (int)$secretarios; ?></div>
                    <div class="estadistica-label">Secretarios<br>registrados</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="reservas_pendientes"><?php echo (int)$reservas_pendientes; ?></div>
                    <div class="estadistica-label">Reservas<br>pendientes</div>
                </div>
            </div>
            <div style="text-align: center; margin: 20px 0;">
                <p style="color: #666; font-size: 14px;">
                  Este gráfico muestra la distribución de clases por turno y día de la semana.
                  Cada barra representa la cantidad de clases programadas, diferenciadas por color según el turno (Mañana, Tarde, Noche).
                </p>
            </div>
        </section>
    </main>
    <?php include '../../PHP/dashboard.php'; ?>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdn.anychart.com/releases/8.11.0/css/anychart-ui.min.css">
    <script>
        window.__ESTADISTICAS = <?php echo json_encode([
            'alumnos' => (int)$alumnos,
            'profesores' => (int)$profesores,
            'grupos' => (int)$grupos,
            'secretarios' => (int)$secretarios,
            'reservas_pendientes' => (int)$reservas_pendientes,
            'grafico' => $graficoData
        ], JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="../../Administrador/JS/estadisticas.js"></script>
    <script src="../../JS/planos_horarios.js"></script>
</body>
</html>
