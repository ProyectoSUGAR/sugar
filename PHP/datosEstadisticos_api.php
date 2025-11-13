<?php
// API endpoint that returns unified statistics JSON for all dashboards
require_once(__DIR__ . '/../Administrador/PHP/datosEstadisticos.php');
require_once(__DIR__ . '/conexion.php');

// Prepare connection and data
$conn = conectar_bd();
$alumnos = obtener_estadisticas_alumnos($conn);
$profesores = obtener_estadisticas_profesores($conn);
$grupos = obtener_estadisticas_grupos($conn);
$secretarios = obtener_estadisticas_secretarios($conn);
$reservas_pendientes = obtener_estadisticas_reservas_pendientes($conn);
$grafico = obtener_datos_grafico_clases($conn);

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'alumnos' => (int)$alumnos,
    'profesores' => (int)$profesores,
    'grupos' => (int)$grupos,
    'secretarios' => (int)$secretarios,
    'reservas_pendientes' => (int)$reservas_pendientes,
    'grafico' => $grafico
], JSON_UNESCAPED_UNICODE);
