<?php

require_once __DIR__ . '/../../PHP/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_notificacion'])) {
	$mensaje = trim($_POST['mensaje']);
	$tipo = $_POST['tipo'];
	$destinatario_tipo = $_POST['destinatario_tipo'];
	$fecha = date('Y-m-d H:i:s');

	$conn = conectar_bd();
	$sql = "INSERT INTO notificacion (mensaje, tipo, fecha, destinatario_tipo) VALUES (?, ?, ?, ?)";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'ssss', $mensaje, $tipo, $fecha, $destinatario_tipo);
	if (mysqli_stmt_execute($stmt)) {
		echo "<p>Notificación creada exitosamente.</p>";
	} else {
		echo "<p>Error al crear la notificación: " . mysqli_error($conn) . "</p>";
	}
}
?>