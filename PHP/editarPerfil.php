<?php
session_start();
require_once("conexion.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../Login/HTML/ingreso.php');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'] ?? '';

// Obtener datos actuales del usuario
$conn = conectar_bd();
$stmt = $conn->prepare("SELECT nombre, apellido, correo FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = '';
    $error = false;

    // Actualizar correo
    if (!empty($_POST['correo']) && $_POST['correo'] !== $usuario['correo']) {
        $nuevo_correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
        if (filter_var($nuevo_correo, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("UPDATE usuario SET correo = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $nuevo_correo, $id_usuario);
            if ($stmt->execute()) {
                $mensaje .= "Éxito: Correo actualizado exitosamente. ";
                // Registrar actividad
                $detalle = "Usuario actualizó su correo electrónico";
                $stmt = $conn->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'actualizacion_perfil', ?)");
                $stmt->bind_param("is", $id_usuario, $detalle);
                $stmt->execute();
            } else {
                $error = true;
                $mensaje .= "Error: Al actualizar el correo. ";
            }
        } else {
            $error = true;
            $mensaje .= "Error: Formato de correo inválido. ";
        }
    }

    // Actualizar contraseña
    if (!empty($_POST['contrasenia'])) {
        $nueva_contrasenia = password_hash($_POST['contrasenia'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuario SET contrasenia = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $nueva_contrasenia, $id_usuario);
            if ($stmt->execute()) {
            $mensaje .= "Éxito: Contraseña actualizada exitosamente. ";
            // Registrar actividad
            $detalle = "Usuario actualizó su contraseña";
            $stmt = $conn->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'actualizacion_perfil', ?)");
            $stmt->bind_param("is", $id_usuario, $detalle);
            $stmt->execute();
        } else {
            $error = true;
            $mensaje .= "Error: Al actualizar la contraseña. ";
        }
    }

    // Actualizar foto de perfil
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Determinar carpeta física del proyecto (un nivel arriba de PHP)
            $projectRoot = dirname(__DIR__);
            $upload_dir = $projectRoot . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'perfiles' . DIRECTORY_SEPARATOR;
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $nuevo_nombre = "perfil_" . $id_usuario . "_" . time() . "." . $ext;
            $physical_dest = $upload_dir . $nuevo_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $physical_dest)) {
                // Asegurar permisos
                @chmod($physical_dest, 0644);

                // Guardar ruta relativa para ser consistente
                $url_web_rel = 'images/perfiles/' . $nuevo_nombre;
                // Guardar en sesión para actualización inmediata (ruta relativa)
                $_SESSION['avatar_url'] = '../' . $url_web_rel;

                // Verificar si el id_recurso existe en la tabla recurso
                $stmt_check = $conn->prepare("SELECT COUNT(*) FROM recurso WHERE id_recurso = ?");
                $stmt_check->bind_param("i", $id_usuario);
                $stmt_check->execute();
                $stmt_check->bind_result($exists);
                $stmt_check->fetch();
                $stmt_check->close();

                if ($exists === 0) {
                    // Manejar el caso donde el id_recurso no existe
                    $mensaje = "Error: El recurso asociado no existe. Por favor, verifique.";
                } else {
                    // Guardar URL relativa en la DB
                    $stmt = $conn->prepare("INSERT INTO imagen (id_recurso, tipo, url) 
                                      VALUES (?, 'perfil', ?)");
                    $url_db = '../' . $url_web_rel;
                    $stmt->bind_param("is", $id_usuario, $url_db);
                    if ($stmt->execute()) {
                        $mensaje = "Éxito: Foto de perfil actualizada exitosamente.";
                        // Registrar actividad
                        $detalle = "Usuario actualizó su foto de perfil";
                        $stmt = $conn->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'actualizacion_perfil', ?)");
                        $stmt->bind_param("is", $id_usuario, $detalle);
                        $stmt->execute();

                        // Actualizar la variable para mostrar la nueva imagen de inmediato (usar la URL relativa con cache-bust)
                        $foto_perfil = $url_web_rel;
                        // Guardar en sesión para que los headers muestren la nueva imagen inmediatamente (con cache-bust y prefijo de proyecto)
                        $_SESSION['avatar_url'] = '/sugar-main' . $url_web_rel . '?v=' . time();
                    } else {
                        $mensaje = "Error: No se pudo actualizar la foto de perfil.";
                    }
                }
            } else {
                $error = true;
                $mensaje .= "Error: Al subir la imagen. ";
            }
        } else {
            $error = true;
            $mensaje .= "Error: Tipo de archivo no permitido. ";
        }
    }

    // Guardar mensaje para mostrarlo después (evitar usar Swal antes de que se cargue la librería)
    if (!empty($mensaje)) {
        $flash_message = [
            'text' => $mensaje,
            'error' => $error
        ];
    }
}

// Obtener la foto de perfil actual
$stmt = $conn->prepare("SELECT url FROM imagen WHERE id_recurso = ? AND tipo = 'perfil' ORDER BY id_imagen DESC LIMIT 1");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$imagen = $result->fetch_assoc();
$foto_perfil = $imagen ? $imagen['url'] : '../images/perfiles/perfilpordefecto.jpg';

// Asegurar que la ruta sea accesible desde el navegador


// No normalizar a URL absoluta, dejar la ruta relativa para el HTML

// Incluir el header correspondiente según el tipo de usuario
$header_file = match($tipo_usuario) {
    'administrador' => '../HEADERS/headerAA.php',
    'adscripta' => '../HEADERS/headerA.php',
    'alumno' => '../HEADERS/headerE.php',
    'profesor' => '../HEADERS/headerP.php',
    'secretaria' => '../HEADERS/headerS.php',
    'direccion' => '../HEADERS/headerD.php',
    'funcionario' => '../HEADERS/headerF.php',
    default => '../HEADERS/headerS.php'
};
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
            <input type="email" name="correo" class="inputuser" placeholder="ejemplo@gmail.com" value="<?php echo htmlspecialchars($usuario['correo']); ?>">
            <input type="password" name="contrasenia" class="inputuser1" placeholder="Insertar nueva contraseña">
            <input type="submit" class="botoneditaru" name="guardardatosu" value="Guardar">
        </div>
    </form>

    <script>
    // Preview de imagen antes de subir
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
