<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'conexion.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if (!$id) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}
$conn = conectar_bd();
$check = mysqli_query($conn, "SHOW COLUMNS FROM recurso LIKE 'disponibilidad'");
if (!$check || mysqli_num_rows($check) === 0) {
    $alter = "ALTER TABLE recurso ADD COLUMN disponibilidad ENUM('disponible','no_disponible') DEFAULT 'disponible'";
    mysqli_query($conn, $alter);
}
$stmt = mysqli_prepare($conn, "SELECT disponibilidad FROM recurso WHERE id_recurso = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
if (!$row) {
    echo json_encode(['ok' => false, 'error' => 'Recurso no encontrado']);
    exit;
}
$current = $row['disponibilidad'];
$new = ($current === 'disponible') ? 'no_disponible' : 'disponible';
$u = mysqli_prepare($conn, "UPDATE recurso SET disponibilidad = ? WHERE id_recurso = ?");
mysqli_stmt_bind_param($u, 'si', $new, $id);
$ok = mysqli_stmt_execute($u);
if ($ok) {
    echo json_encode(['ok' => true, 'disponibilidad' => $new]);
} else {
    echo json_encode(['ok' => false, 'error' => 'No se pudo actualizar']);
}
?>