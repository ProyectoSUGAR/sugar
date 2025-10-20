<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sugar";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el tipo de usuario y su ID (esto debería ser dinámico, por ejemplo, desde la sesión)
$tipo_usuario = $_GET['tipo_usuario'] ?? null; // Ejemplo: 'adscripta', 'profesor', etc.
$id_usuario = $_GET['id_usuario'] ?? null;

// Consulta para obtener las notificaciones
$sql = "SELECT mensaje, tipo, fecha FROM notificacion WHERE (destinatario_tipo = 'todos' OR destinatario_tipo = ?) AND (id_usuario IS NULL OR id_usuario = ?) ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $tipo_usuario, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Formatear las notificaciones en un array
$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    $notificaciones[] = $row;
}

// Cerrar la conexión
$stmt->close();
$conn->close();

// Devolver las notificaciones como JSON
header('Content-Type: application/json');
echo json_encode($notificaciones);