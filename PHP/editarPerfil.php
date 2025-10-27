<?php
session_start();
require_once("conexion.php");
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../Login/HTML/ingreso.php');
    exit();
}
$id_usuario = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'] ?? '';
$conn = conectar_bd();
$stmt = $conn->prepare("SELECT nombre, apellido, correo FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = '';
    $error = false;
    if (!empty($_POST['correo']) && $_POST['correo'] !== $usuario['correo']) {
        $nuevo_correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
        if (filter_var($nuevo_correo, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("UPDATE usuario SET correo = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $nuevo_correo, $id_usuario);
            if ($stmt->execute()) {
                $mensaje .= "Éxito: Correo actualizado exitosamente. ";
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
    if (!empty($_POST['contrasenia'])) {
        $nueva_contrasenia = password_hash($_POST['contrasenia'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuario SET contrasenia = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $nueva_contrasenia, $id_usuario);
            if ($stmt->execute()) {
            $mensaje .= "Éxito: Contraseña actualizada exitosamente. ";
            $detalle = "Usuario actualizó su contraseña";
            $stmt = $conn->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'actualizacion_perfil', ?)");
            $stmt->bind_param("is", $id_usuario, $detalle);
            $stmt->execute();
        } else {
            $error = true;
            $mensaje .= "Error: Al actualizar la contraseña. ";
        }
    }
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $maxBytes = 2 * 1024 * 1024;
        if (!in_array($ext, $allowed)) {
            $error = true;
            $mensaje .= "Error: Tipo de archivo no permitido. ";
        } elseif ($_FILES['imagen']['size'] > $maxBytes) {
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
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $physical_dest)) {
                @chmod($physical_dest, 0644);
                $url_web_rel = 'Images/perfiles/' . $nuevo_nombre;
                $_SESSION['avatar_url'] = '/' . $url_web_rel . '?v=' . time();
                $stmt = $conn->prepare("INSERT INTO imagen (id_recurso, tipo, url) VALUES (?, 'perfil', ?)");
                $url_db = '/' . $url_web_rel;
                $stmt->bind_param("is", $id_usuario, $url_db);
                if ($stmt->execute()) {
                    $mensaje = "Éxito: Foto de perfil actualizada exitosamente.";
                    $detalle = "Usuario actualizó su foto de perfil";
                    $stmt_act = $conn->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'actualizacion_perfil', ?)");
                    $stmt_act->bind_param("is", $id_usuario, $detalle);
                    $stmt_act->execute();
                    $foto_perfil = '/' . $url_web_rel . '?v=' . time();
                } else {
                    $mensaje = "Error: No se pudo actualizar la foto de perfil.";
                }
            } else {
                $error = true;
                $mensaje .= "Error: Al subir la imagen. ";
            }
        }
    }
    if (!empty($mensaje)) {
        $flash_message = [
            'text' => $mensaje,
            'error' => $error
        ];
    }
}
$stmt = $conn->prepare("SELECT url FROM imagen WHERE id_recurso = ? AND tipo = 'perfil' ORDER BY id_imagen DESC LIMIT 1");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$imagen = $result->fetch_assoc();
$foto_perfil = $imagen ? $imagen['url'] : '../images/perfiles/perfilpordefecto.jpg';
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
