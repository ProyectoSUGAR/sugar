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

// First get image paths to delete files
$images = [];
$img_query = mysqli_query($db, "SELECT url FROM imagen WHERE id_recurso = " . $id);
while ($row = mysqli_fetch_assoc($img_query)) {
    if ($row['url']) {
        // Convert URL path to filesystem path
        $fs_path = dirname(__FILE__, 3) . str_replace('/', DIRECTORY_SEPARATOR, $row['url']);
        $images[] = $fs_path;
    }
}

// Delete resource (images with FK will cascade in DB)
$del = mysqli_query($db, "DELETE FROM recurso WHERE id_recurso = " . $id);

if ($del) {
    // Delete physical image files
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
