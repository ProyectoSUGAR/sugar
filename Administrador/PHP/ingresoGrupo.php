<?php
function validar_datos_grupo($tipo, $nombre, $anio, $horas) {
    $errores = [];
    if (empty($tipo)) $errores[] = "El tipo es obligatorio.";
    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (!preg_match('/^[A-Za-z]{1,3}$/', $nombre)) $errores[] = "Nombre inválido (solo letras 1-3).";
    if ($anio < 1 || $anio > 6) $errores[] = "Año inválido (1-6).";
    if ($horas < 1 || $horas > 40) $errores[] = "Horas inválidas (1-40).";
    return $errores;
}
function insertar_grupo($tipo, $nombre, $anio, $horas) {
    require_once("../../PHP/conexion.php");
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "INSERT INTO grupo (tipo, nombre, anio, horas_semanales) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        return ["status" => "error", "message" => "Error en la preparación de la consulta: " . mysqli_error($conn)];
    }
    mysqli_stmt_bind_param($stmt, "ssii", $tipo, $nombre, $anio, $horas);
    mysqli_stmt_execute($stmt);
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    if ($affected_rows > 0) {
        return ["status" => "success", "message" => "Grupo guardado correctamente."];
    } else {
        return ["status" => "error", "message" => "Error al guardar el grupo: No se insertaron filas."];
    }
}
function procesar_ingreso_grupo() {
    $data = json_decode(file_get_contents("php://input"), true);
    $tipo = isset($data['tipo']) ? trim($data['tipo']) : null;
    $nombre = isset($data['nombre']) ? trim($data['nombre']) : null;
    $anio = isset($data['anio']) ? intval($data['anio']) : null;
    $horas = isset($data['horas_semanales']) ? intval($data['horas_semanales']) : null;

    $errores = validar_datos_grupo($tipo, $nombre, $anio, $horas);
    if (!empty($errores)) {
        echo json_encode(["status" => "error", "message" => implode(' ', $errores)]);
        exit;
    }

    $resultado = insertar_grupo($tipo, $nombre, $anio, $horas);
    echo json_encode($resultado);
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    procesar_ingreso_grupo();
}
