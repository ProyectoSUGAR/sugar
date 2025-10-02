<?php

require_once '../../PHP/conexion.php';

// Insertar notificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_noti'])) {
	$mensaje = trim($_POST['mensaje']);
	$tipo = $_POST['tipo'];
	$destinatario_tipo = $_POST['destinatario_tipo'];
	$fecha = date('Y-m-d H:i:s');

	$conn = conectar_bd();
	$sql = "INSERT INTO notificacion (mensaje, tipo, fecha, destinatario_tipo) VALUES (?, ?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('ssss', $mensaje, $tipo, $fecha, $destinatario_tipo);
	$stmt->execute();
	$stmt->close();
	$conn->close();

	// Redirigir para evitar reenvío de formulario
	header('Location: ../HTML/notificaciones.php');
	exit();
}

// Mostrar notificaciones
function mostrarNotificaciones() {
	require_once '../../PHP/conexion.php';
	$conn = conectar_bd();
	$sql = "SELECT id_notificacion, mensaje, tipo, fecha, destinatario_tipo FROM notificacion ORDER BY fecha DESC LIMIT 30";
	$result = $conn->query($sql);
	echo '<table class="tabla-noti">';
	echo '<tr><th>ID</th><th>Mensaje</th><th>Tipo</th><th>Fecha</th><th>Destinatario</th></tr>';
	while ($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>' . $row['id_notificacion'] . '</td>';
		echo '<td>' . htmlspecialchars($row['mensaje']) . '</td>';
		echo '<td>' . ucfirst($row['tipo']) . '</td>';
		echo '<td>' . $row['fecha'] . '</td>';
		echo '<td>' . ucfirst($row['destinatario_tipo']) . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	$conn->close();
}

