<?php
require_once("../../PHP/conexion.php");
$conn = conectar_bd();

$data = json_decode(file_get_contents("php://input"), true);

$tipo = isset($data['tipo']) ? trim($data['tipo']) : null;
$nombre = isset($data['nombre']) ? trim($data['nombre']) : null;
$anio = isset($data['anio']) ? intval($data['anio']) : null;
$horas = isset($data['horas_semanales']) ? intval($data['horas_semanales']) : null;

// Validaciones servidor
if ($nombre !== null) {
    if (!preg_match('/^[A-Za-z]{1,3}$/', $nombre)) {
        echo json_encode(["status"=>"error","message"=>"Nombre inválido (solo letras 1-3)."]);
        exit;
    }
}
if ($anio !== null && ($anio < 1 || $anio > 6)) {
    echo json_encode(["status"=>"error","message"=>"Año inválido (1-6)."]);
    exit;
}
if ($horas !== null && ($horas < 1 || $horas > 40)) {
    echo json_encode(["status"=>"error","message"=>"Horas inválidas (1-40)."]);
    exit;
}

if ($tipo && $nombre && $anio && $horas) {
    $stmt = mysqli_prepare($conn, "INSERT INTO grupo (tipo, nombre, anio, horas_semanales) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "Error en la preparación de la consulta: " . mysqli_error($conn)
        ]);

        exit;
    }
    mysqli_stmt_bind_param($stmt, "ssii", $tipo, $nombre, $anio, $horas);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            "status" => "success",
            "message" => "Grupo guardado correctamente."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Error al guardar el grupo: " . mysqli_stmt_error($stmt)
        ]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Todos los campos son obligatorios. Recibido: " . json_encode($data)
    ]);
}

if (isset($conn) && $conn instanceof mysqli) {
    if (@$conn->ping()) {

    }
}
?>