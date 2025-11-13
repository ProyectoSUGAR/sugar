<?php
header('Content-Type: application/json');
require_once 'c:\xampp\htdocs\sugar\PHP\conexion.php';

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

if (!$fecha) {
    echo json_encode([]);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT id_espacio, horario FROM asocia WHERE dia_semana = DAYNAME(?)
    UNION
    SELECT id_espacio, fecha_inicio AS horario FROM reserva WHERE fecha_reserva = ? AND estado = 'aprobada';
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $fecha, $fecha);
$stmt->execute();
$result = $stmt->get_result();

$ocupados = [];
while ($row = $result->fetch_assoc()) {
    $ocupados[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($ocupados);
?>