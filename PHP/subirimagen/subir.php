<?php
// Handler para subir imagenes y crear recurso
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'conexion.php';

// Carpeta donde guardaremos las imagenes de recursos
$carpeta_rel = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'recursos' . DIRECTORY_SEPARATOR; // resolves to project/Images/recursos/
// But to be robust, build relative to this file
$base_dir = dirname(__FILE__, 3); // c:\xampp\htdocs\sugar-main
$images_dir = $base_dir . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'recursos' . DIRECTORY_SEPARATOR;

if (!file_exists($images_dir)) {
    mkdir($images_dir, 0777, true);
}

$conn = conectar_bd();

// Ensure tables exist with correct structure (non-destructive)
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

// Execute each creation query (log on error but don't abort to avoid wiping data)
if (!mysqli_query($conn, $create_recurso)) {
    error_log('Error creating recurso table: ' . mysqli_error($conn));
}

if (!mysqli_query($conn, $create_imagen)) {
    error_log('Error creating imagen table: ' . mysqli_error($conn));
}

// Helper safe filename
function safe_filename($name) {
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
    $cantidad = isset($_POST['tipo']) ? intval($_POST['tipo']) : null;
    $disp = isset($_POST['disp']) ? $_POST['disp'] : null;
    // Removed turno/horario — recursos no dependen de turno
    if (!$nombre || $cantidad === null) {
        http_response_code(400);
        echo "Nombre y cantidad son obligatorios";
        exit;
    }

    // Check if disponibilidad column exists, if not add it
    $result = mysqli_query($conn, "SHOW COLUMNS FROM recurso LIKE 'disponibilidad'");
    if (mysqli_num_rows($result) == 0) {
        mysqli_query($conn, "ALTER TABLE recurso ADD COLUMN disponibilidad VARCHAR(20) DEFAULT 'disponible'");
    }

    // Ensure id_recurso is AUTO_INCREMENT primary key (try to fix existing schema)
    $col = mysqli_query($conn, "SHOW COLUMNS FROM recurso LIKE 'id_recurso'");
    if ($col && $rowcol = mysqli_fetch_assoc($col)) {
        if (stripos($rowcol['Extra'], 'auto_increment') === false) {
            // Try to alter to auto_increment; this may fail if constraints exist
            $alter = mysqli_query($conn, "ALTER TABLE recurso MODIFY id_recurso INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
            if (!$alter) {
                error_log('Could not set id_recurso AUTO_INCREMENT: ' . mysqli_error($conn));
            }
        }
    }

    // Insert recurso (prepared) — only name, quantity (tipo) and disponibilidad
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
        // attempt to fetch by name as a fallback (not ideal)
        $res_f = mysqli_query($conn, "SELECT id_recurso FROM recurso WHERE nombre = '" . mysqli_real_escape_string($conn, $nombre) . "' ORDER BY id_recurso DESC LIMIT 1");
        if ($res_f && $r = mysqli_fetch_assoc($res_f)) {
            $id_recurso = $r['id_recurso'];
        }
    }

    // Si se sube imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagen']['tmp_name'];
        $orig_name = basename($_FILES['imagen']['name']);
        $safe = time() . '_' . safe_filename($orig_name);
        $dest = $images_dir . $safe;

        if (move_uploaded_file($tmp, $dest)) {
            // Store a relative URL that works from Secretaria/HTML/asignarRec.php
            // Using ../../Images/recursos/ so the image shows correctly on that page
            $rel_path = '../../Images/recursos/' . $safe;

            // Insert into imagen table
            $stmt2 = mysqli_prepare($conn, "INSERT INTO imagen (tipo, url, id_recurso) VALUES (?, ?, ?)");
            if (!$stmt2) {
                error_log('Error preparing imagen insert: ' . mysqli_error($conn));
            } else {
                mysqli_stmt_bind_param($stmt2, 'ssi', $orig_name, $rel_path, $id_recurso);
                $ok = mysqli_stmt_execute($stmt2);
                if (!$ok) {
                    error_log('Error inserting imagen: ' . mysqli_error($conn));
                }
            }
        } else {
            error_log('Error moviendo la imagen subida');
        }
    }

    // Redirect back to the asignarRec page or show success
    header('Location: ../../Secretaria/HTML/asignarRec.php?success=1');
    exit;
}

// Not POST
http_response_code(405);
echo 'Método no permitido';
?>