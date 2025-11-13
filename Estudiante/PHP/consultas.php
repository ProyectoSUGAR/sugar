<?php
// Función para consultar espacios de tipo despacho
function consultar_espacios_despacho() {
    require_once("../../PHP/conexion.php");
    $conn = conectar_bd();
    $sql = "SELECT * FROM espacio WHERE tipo_espacio = 'despacho'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $despachos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $despachos[] = $row;
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $despachos;
}

// Llamada principal si se ejecuta directamente
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    // Si se llama directamente, devolver JSON
    header('Content-Type: application/json');
    echo json_encode(consultar_espacios_despacho());
}
