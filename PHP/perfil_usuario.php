<?php
session_start();

// Cierre de sesión
if (isset($_GET['cerrar_sesion']) && $_GET['cerrar_sesion'] == '1') {
	session_unset();
	session_destroy();
	header('Location: /Login/HTML/ingreso.php');
	exit;
}

function obtenerDatosUsuario() {
	// Asume que el id del usuario está en $_SESSION['usuario_id']
	if (!isset($_SESSION['usuario_id'])) {
		return [
			'nombre' => 'Invitado',
			'imagen' => ''
		];
	}

	$id = $_SESSION['usuario_id'];
	require_once __DIR__ . '/conexion.php';
	$conn = conectar_bd();

	// Obtener nombre del usuario
	$sql = "SELECT nombre FROM usuario WHERE id_usuario = ? LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$stmt->bind_result($nombre);
	$stmt->fetch();
	$stmt->close();

	// Obtener imagen de perfil del usuario
	$sql_img = "SELECT url FROM imagen WHERE entidad='usuario' AND tipo='perfil' AND id_usuario = ? ORDER BY fecha DESC LIMIT 1";
	$stmt_img = $conn->prepare($sql_img);
	$stmt_img->bind_param('i', $id);
	$stmt_img->execute();
	$stmt_img->bind_result($url_img);
	$img = '';
	if ($stmt_img->fetch()) {
		$img = $url_img;
	}
	$stmt_img->close();
	$conn->close();

	return [
		'nombre' => $nombre ? $nombre : 'Usuario',
		'imagen' => $img
	];
}
