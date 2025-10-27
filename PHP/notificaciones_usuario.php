<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sugar";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$tipo_usuario = $_GET['tipo_usuario'] ?? null; // Ejemplo: 'adscripta', 'profesor', etc.
$id_usuario = $_GET['id_usuario'] ?? null;
$sql = "SELECT mensaje, tipo, fecha FROM notificacion WHERE (destinatario_tipo = 'todos' OR destinatario_tipo = ?) AND (id_usuario IS NULL OR id_usuario = ?) ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $tipo_usuario, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    $notificaciones[] = $row;
}
$stmt->close();
$conn->close();
header('Content-Type: application/json');
echo json_encode($notificaciones);