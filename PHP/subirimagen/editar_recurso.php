<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método no permitido';
    exit;
}
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$cantidad = isset($_POST['tipo']) ? intval($_POST['tipo']) : null;
$disp = isset($_POST['disp']) ? $_POST['disp'] : null;
if (!$id || !$nombre || $cantidad === null) {
    echo 'Datos incompletos';
    exit;
}
$db = conectar_bd();
$upd = mysqli_query($db, "UPDATE recurso SET nombre = '" . mysqli_real_escape_string($db, $nombre) . "', tipo = '" . mysqli_real_escape_string($db, (string)$cantidad) . "', disponibilidad = '" . mysqli_real_escape_string($db, $disp) . "' WHERE id_recurso = " . $id);
if ($upd) {
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $base_dir = dirname(__FILE__, 3);
        $images_dir = $base_dir . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'recursos' . DIRECTORY_SEPARATOR;
        if (!file_exists($images_dir)) mkdir($images_dir, 0777, true);
        $tmp = $_FILES['imagen']['tmp_name'];
        $orig_name = basename($_FILES['imagen']['name']);
        $safe = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $orig_name);
        $dest = $images_dir . $safe;
        if (move_uploaded_file($tmp, $dest)) {
            $rel_path = '../../Images/recursos/' . $safe;
            mysqli_query($db, "INSERT INTO imagen (tipo, url, id_recurso) VALUES ('" . mysqli_real_escape_string($db, $orig_name) . "', '" . mysqli_real_escape_string($db, $rel_path) . "', " . $id . ")");
        }
    }
    header('Location: ../../Secretaria/HTML/asignarRec.php?edited=1');
    exit;
} else {
    echo 'Error al actualizar: ' . mysqli_error($db);
}
