<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'conexion.php';
$carpeta_rel = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'recursos' . DIRECTORY_SEPARATOR; // resolves to project/Images/recursos/
$base_dir = dirname(__FILE__, 3); // c:\xampp\htdocs\sugar-main
$images_dir = $base_dir . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'recursos' . DIRECTORY_SEPARATOR;
if (!file_exists($images_dir)) {
    mkdir($images_dir, 0777, true);
}
$conn = conectar_bd();
$create_recurso = "CREATE TABLE IF NOT EXISTS recurso (
    id_recurso INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) DEFAULT NULL,
    disponibilidad VARCHAR(20) DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$create_imagen = "CREATE TABLE IF NOT EXISTS imagen (
    id_imagen INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) DEFAULT NULL,
    url VARCHAR(255) NOT NULL,
    id_recurso INT(11),
    FOREIGN KEY (id_recurso) REFERENCES recurso(id_recurso) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (!mysqli_query($conn, $create_recurso)) {
    error_log('Error creating recurso table: ' . mysqli_error($conn));
}
if (!mysqli_query($conn, $create_imagen)) {
    error_log('Error creating imagen table: ' . mysqli_error($conn));
}
function safe_filename($name) {
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
    return $name;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
    $cantidad = isset($_POST['tipo']) ? intval($_POST['tipo']) : null;
    $disp = isset($_POST['disp']) ? $_POST['disp'] : null;
    if (!$nombre || $cantidad === null) {
        http_response_code(400);
        echo "Nombre y cantidad son obligatorios";
        exit;
    }
    $result = mysqli_query($conn, "SHOW COLUMNS FROM recurso LIKE 'disponibilidad'");
    if (mysqli_num_rows($result) == 0) {
        mysqli_query($conn, "ALTER TABLE recurso ADD COLUMN disponibilidad VARCHAR(20) DEFAULT 'disponible'");
    }
    $col = mysqli_query($conn, "SHOW COLUMNS FROM recurso LIKE 'id_recurso'");
    if ($col && $rowcol = mysqli_fetch_assoc($col)) {
        if (stripos($rowcol['Extra'], 'auto_increment') === false) {
            $alter = mysqli_query($conn, "ALTER TABLE recurso MODIFY id_recurso INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
            if (!$alter) {
                error_log('Could not set id_recurso AUTO_INCREMENT: ' . mysqli_error($conn));
            }
        }
    }
    $stmt = mysqli_prepare($conn, "INSERT INTO recurso (nombre, tipo, disponibilidad) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sis', $nombre, $cantidad, $disp);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log('Error insertando recurso: ' . mysqli_error($conn));
        die('Error al guardar el recurso');
    }
    $id_recurso = mysqli_insert_id($conn);
    if (!$id_recurso || $id_recurso == 0) {
        error_log('Insert did not return a valid insert id for recurso; last error: ' . mysqli_error($conn));
        $res_f = mysqli_query($conn, "SELECT id_recurso FROM recurso WHERE nombre = '" . mysqli_real_escape_string($conn, $nombre) . "' ORDER BY id_recurso DESC LIMIT 1");
        if ($res_f && $r = mysqli_fetch_assoc($res_f)) {
            $id_recurso = $r['id_recurso'];
        }
    }
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagen']['tmp_name'];
        $orig_name = basename($_FILES['imagen']['name']);
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','avif'];
        $maxBytes = 5 * 1024 * 1024; // 5MB max for recursos
        if (!in_array($ext, $allowed)) {
            error_log('Tipo de imagen no permitido: ' . $ext);
        } elseif ($_FILES['imagen']['size'] > $maxBytes) {
            error_log('Imagen demasiado grande: ' . $_FILES['imagen']['size']);
        } else {
            $safe = time() . '_' . safe_filename($orig_name);
            $dest = $images_dir . $safe;
            if (move_uploaded_file($tmp, $dest)) {
                $rel_path = '../../Images/recursos/' . $safe;
                $tipo_img = $ext;
                $stmt2 = mysqli_prepare($conn, "INSERT INTO imagen (tipo, url, id_recurso) VALUES (?, ?, ?)");
                if (!$stmt2) {
                    error_log('Error preparing imagen insert: ' . mysqli_error($conn));
                } else {
                    mysqli_stmt_bind_param($stmt2, 'ssi', $tipo_img, $rel_path, $id_recurso);
                    $ok = mysqli_stmt_execute($stmt2);
                    if (!$ok) {
                        error_log('Error inserting imagen: ' . mysqli_error($conn));
                    }
                }
            } else {
                error_log('Error moviendo la imagen subida');
            }
        }
    }
    header('Location: ../../Secretaria/HTML/asignarRec.php?success=1');
    exit;
}
http_response_code(405);
echo 'Método no permitido';
?>