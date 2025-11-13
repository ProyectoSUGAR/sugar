<?php
session_start();
require_once("conexion.php");

// Función para validar sesión
function validar_sesion_editar_perfil() {
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ../Login/HTML/ingreso.php');
        exit();
    }
}

// Función para obtener datos del usuario
function obtener_datos_usuario($id_usuario) {
    $conn = conectar_bd();
    $stmt = $conn->prepare("SELECT nombre, apellido, correo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Función para procesar actualización de correo
function procesar_actualizacion_correo($nuevo_correo, $correo_actual, $id_usuario) {
    $mensaje = '';
    $error = false;
    if (!empty($nuevo_correo) && $nuevo_correo !== $correo_actual) {
        $nuevo_correo = filter_var($nuevo_correo, FILTER_SANITIZE_EMAIL);
        if (filter_var($nuevo_correo, FILTER_VALIDATE_EMAIL)) {
            $conn = conectar_bd();
            $stmt = $conn->prepare("UPDATE usuario SET correo = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $nuevo_correo, $id_usuario);
            if ($stmt->execute()) {
                $mensaje .= "Éxito: Correo actualizado exitosamente. ";
                registrar_actividad_editar_perfil($id_usuario, "Usuario actualizó su correo electrónico");
            } else {
                $error = true;
                $mensaje .= "Error: Al actualizar el correo. ";
            }
        } else {
            $error = true;
            $mensaje .= "Error: Formato de correo inválido. ";
        }
    }
    return ['mensaje' => $mensaje, 'error' => $error];
}

// Función para procesar actualización de contraseña
function procesar_actualizacion_contrasenia($nueva_contrasenia, $id_usuario) {
    $mensaje = '';
    $error = false;
    if (!empty($nueva_contrasenia)) {
        $hashed_contrasenia = password_hash($nueva_contrasenia, PASSWORD_DEFAULT);
        $conn = conectar_bd();
        $stmt = $conn->prepare("UPDATE usuario SET contrasenia = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $hashed_contrasenia, $id_usuario);
        if ($stmt->execute()) {
            $mensaje .= "Éxito: Contraseña actualizada exitosamente. ";
            registrar_actividad_editar_perfil($id_usuario, "Usuario actualizó su contraseña");
        } else {
            $error = true;
            $mensaje .= "Error: Al actualizar la contraseña. ";
        }
    }
    return ['mensaje' => $mensaje, 'error' => $error];
}

// Función para procesar subida de imagen
function procesar_subida_imagen($file, $id_usuario) {
    $mensaje = '';
    $error = false;
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $maxBytes = 2 * 1024 * 1024;
    if (!in_array($ext, $allowed)) {
        $error = true;
        $mensaje .= "Error: Tipo de archivo no permitido. ";
    } elseif ($file['size'] > $maxBytes) {
        $error = true;
        $mensaje .= "Error: El archivo es demasiado grande (máx 2MB). ";
    } else {
        $projectRoot = dirname(__DIR__);
        $upload_dir = $projectRoot . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'perfiles' . DIRECTORY_SEPARATOR;
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $nuevo_nombre = "perfil_" . $id_usuario . "_" . time() . "." . $ext;
        $physical_dest = $upload_dir . $nuevo_nombre;
        if (move_uploaded_file($file['tmp_name'], $physical_dest)) {
            @chmod($physical_dest, 0644);
            $url_web_rel = 'Images/perfiles/' . $nuevo_nombre;
            $_SESSION['avatar_url'] = '/' . $url_web_rel . '?v=' . time();
            $conn = conectar_bd();
            // Store URL in DB without leading slash to keep paths consistent
            $stmt = $conn->prepare("INSERT INTO usuario_imagen (id_usuario, tipo, url) VALUES (?, ?, ?)");
            $tipo_img = 'perfil';
            $url_db = 'Images/perfiles/' . $nuevo_nombre;
            if ($stmt) {
                $stmt->bind_param("iss", $id_usuario, $tipo_img, $url_db);
            }
            if ($stmt->execute()) {
                $mensaje = "Éxito: Foto de perfil actualizada exitosamente.";
                registrar_actividad_editar_perfil($id_usuario, "Usuario actualizó su foto de perfil");
                $foto_perfil = '/' . $url_web_rel . '?v=' . time();
            } else {
                $mensaje = "Error: No se pudo actualizar la foto de perfil.";
            }
        } else {
            $error = true;
            $mensaje .= "Error: Al subir la imagen. ";
        }
    }
    return ['mensaje' => $mensaje, 'error' => $error, 'foto_perfil' => $foto_perfil ?? null];
}

// Función para registrar actividad
function registrar_actividad_editar_perfil($id_usuario, $detalle) {
    $conn = conectar_bd();
    $stmt = $conn->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'actualizacion_perfil', ?)");
    $stmt->bind_param("is", $id_usuario, $detalle);
    $stmt->execute();
}

// Función para obtener imagen de perfil
function obtener_imagen_perfil($id_usuario) {
    $conn = conectar_bd();
    $stmt = $conn->prepare("SELECT url FROM usuario_imagen WHERE id_usuario = ? AND tipo = 'perfil' ORDER BY id_imagen DESC LIMIT 1");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $imagen = $result->fetch_assoc();
    return $imagen ? $imagen['url'] : '../images/perfiles/perfilpordefecto.jpg';
}

// Función para obtener header según tipo de usuario
function obtener_header_editar_perfil($tipo_usuario) {
    return match($tipo_usuario) {
        'administrador' => '../HEADERS/headerAA.php',
        'adscripta' => '../HEADERS/headerA.php',
        'alumno' => '../HEADERS/headerE.php',
        'profesor' => '../HEADERS/headerP.php',
        'secretaria' => '../HEADERS/headerS.php',
        'direccion' => '../HEADERS/headerD.php',
        'funcionario' => '../HEADERS/headerF.php',
        default => '../HEADERS/headerS.php'
    };
}

// Función principal para procesar el formulario
function procesar_formulario_editar_perfil($usuario, $id_usuario) {
    $mensaje_total = '';
    $error_total = false;
    $foto_perfil = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Procesar correo
        $correo_result = procesar_actualizacion_correo($_POST['correo'] ?? '', $usuario['correo'], $id_usuario);
        $mensaje_total .= $correo_result['mensaje'];
        $error_total = $error_total || $correo_result['error'];

        // Procesar contraseña
        $contrasenia_result = procesar_actualizacion_contrasenia($_POST['contrasenia'] ?? '', $id_usuario);
        $mensaje_total .= $contrasenia_result['mensaje'];
        $error_total = $error_total || $contrasenia_result['error'];

        // Procesar imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $imagen_result = procesar_subida_imagen($_FILES['imagen'], $id_usuario);
            $mensaje_total = $imagen_result['mensaje']; // Sobrescribe porque es el mensaje principal para imagen
            $error_total = $error_total || $imagen_result['error'];
            $foto_perfil = $imagen_result['foto_perfil'];
        }

        if (!empty($mensaje_total)) {
            return [
                'text' => $mensaje_total,
                'error' => $error_total,
                'foto_perfil' => $foto_perfil
            ];
        }
    }
    return null;
}

// Función principal para editar perfil
function editar_perfil() {
    validar_sesion_editar_perfil();
    $id_usuario = $_SESSION['id_usuario'];
    $tipo_usuario = $_SESSION['tipo_usuario'] ?? '';
    $usuario = obtener_datos_usuario($id_usuario);
    $flash_message = procesar_formulario_editar_perfil($usuario, $id_usuario);
    $foto_perfil = obtener_imagen_perfil($id_usuario);
    $header_file = obtener_header_editar_perfil($tipo_usuario);
    include $header_file;
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Perfil</title>
        <link rel="stylesheet" href="../Css/style.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="bodyregidat">
        <form action="" enctype="multipart/form-data" method="POST">
            <div class="diveditarperfil">
                <h1 class="h1editperfil">Perfil</h1>
                <h2 class="h2cambiarperfil">Cambiar foto de perfil</h2>
                <h2 class="h2cambiarperfil1">Cambiar contraseña</h2>
                <h2 class="h2cambiarperfil2">Cambiar correo electrónico</h2>
                <div class="cambiarfotoperfil">
                    <div class="fotodeperfil">
                        <!-- Avatar src: <?php echo htmlspecialchars($foto_perfil); ?> -->
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" class="perfil-img" id="previewAvatar">
                    </div>
                    <img src="../images/flechatriple.png" alt="" class="imagenflecha">
                    <input type="file" name="imagen" id="imagenInput" class="imga" accept="image/*">
                </div>
                <input type="email" name="correo" class="inputuser" placeholder="ejemplo@gmail.com" maxlength="100" value="<?php echo htmlspecialchars($usuario['correo']); ?>">
                <input type="password" name="contrasenia" class="inputuser1" placeholder="Insertar nueva contraseña" maxlength="64">
                <input type="submit" class="botoneditaru" name="guardardatosu" value="Guardar">
            </div>
        </form>
        <script>
        const input = document.getElementById('imagenInput');
        const preview = document.getElementById('previewAvatar');
        if (input && preview) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                }
                reader.readAsDataURL(file);
            });
        }
        </script>
        <?php if (!empty($flash_message)) : ?>
        <script>
            Swal.fire({
                title: '<?php echo $flash_message['error'] ? "Error" : "Foto actualizada"; ?>',
                text: '<?php echo addslashes($flash_message['text']); ?>',
                icon: '<?php echo $flash_message['error'] ? "error" : "success"; ?>',
                confirmButtonText: 'Ok'
            });
        </script>
        <?php endif; ?>
    </body>
    </html>
    <?php
}

// Llamada principal si se ejecuta directamente
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    editar_perfil();
}
