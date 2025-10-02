<?php
require_once("../../PHP/conexion.php");
$conn = conectar_bd();

$data = json_decode(file_get_contents("php://input"), true);

$tipo = isset($data['tipo']) ? trim($data['tipo']) : null;
$nombre = isset($data['nombre']) ? trim($data['nombre']) : null;
$anio = isset($data['anio']) ? intval($data['anio']) : null;
$grupo = isset($data['grupo']) ? trim($data['grupo']) : null;
$horas = isset($data['horas_semanales']) ? intval($data['horas_semanales']) : null;

if ($tipo && $nombre && $anio && $grupo && $horas) {
    $stmt = mysqli_prepare($conn, "INSERT INTO grupo (tipo, nombre, anio, grupo, horas_semanales) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "Error en la preparación de la consulta: " . mysqli_error($conn)
        ]);

        exit;
    }
    mysqli_stmt_bind_param($stmt, "ssisi", $tipo, $nombre, $anio, $grupo, $horas);
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