<?php
require_once("../../PHP/conexion.php");

function obtenerHistorialActividad() {
    $conn = conectar_bd();
    
    $consulta = "SELECT a.*, u.nombre, u.apellido 
                FROM actividad a 
                JOIN usuario u ON a.id_usuario = u.id_usuario 
                ORDER BY a.fecha DESC 
                LIMIT 100";
    
    $resultado_actividad = mysqli_query($conn, $consulta);
    
    $actividades = array();
    while ($fila = mysqli_fetch_assoc($resultado_actividad)) {
        $fila['fecha'] = date('d/m/Y H:i', strtotime($fila['fecha']));
        $actividades[] = $fila;
    }
    
    if (isset($conn) && $conn instanceof mysqli) {
        mysqli_close($conn);
    }
    
    return $actividades;
}
?>
