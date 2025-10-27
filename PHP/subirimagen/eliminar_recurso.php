<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método no permitido';
    exit;
}
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if (!$id) {
    echo 'ID inválido';
    exit;
}
$db = conectar_bd();
$images = [];
$img_query = mysqli_query($db, "SELECT url FROM imagen WHERE id_recurso = " . $id);
while ($row = mysqli_fetch_assoc($img_query)) {
    if ($row['url']) {
        $fs_path = dirname(__FILE__, 3) . str_replace('/', DIRECTORY_SEPARATOR, $row['url']);
        $images[] = $fs_path;
    }
}
$del = mysqli_query($db, "DELETE FROM recurso WHERE id_recurso = " . $id);
if ($del) {
    foreach ($images as $img_path) {
        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }
    header('Location: ../../Secretaria/HTML/asignarRec.php?deleted=1');
    exit;
} else {
    echo 'Error al eliminar: ' . mysqli_error($db);
}
