<?php
// Configurar header para JSON
header('Content-Type: application/json; charset=utf-8');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sugar";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

// Establecer charset
$conn->set_charset("utf8mb4");

// Obtener parámetros GET
$tipo_usuario = isset($_GET['tipo_usuario']) ? trim($_GET['tipo_usuario']) : null;
$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : null;

// Validar parámetros
if (empty($tipo_usuario) || empty($id_usuario) || $id_usuario <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit();
}

// Consulta para obtener notificaciones
$sql = "SELECT id_notificacion, mensaje, tipo, fecha FROM notificacion 
        WHERE (destinatario_tipo = 'todos' 
           OR destinatario_tipo = ? 
           OR (destinatario_tipo = 'usuario' AND id_usuario = ?))
        ORDER BY fecha DESC 
        LIMIT 10";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al preparar la consulta']);
    exit();
}

$stmt->bind_param("si", $tipo_usuario, $id_usuario);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al ejecutar la consulta']);
    exit();
}

$result = $stmt->get_result();
$notificaciones = [];

while ($row = $result->fetch_assoc()) {
    $notificaciones[] = [
        'id_notificacion' => intval($row['id_notificacion']),
        'mensaje' => htmlspecialchars($row['mensaje'], ENT_QUOTES, 'UTF-8'),
        'tipo' => htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8'),
        'fecha' => $row['fecha']
    ];
}

$stmt->close();
$conn->close();

// Devolver JSON
echo json_encode($notificaciones);