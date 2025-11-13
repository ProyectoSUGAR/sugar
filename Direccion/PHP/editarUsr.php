<?php
function validar_datos_usuario($nombre, $apellido, $correo, $tipo_usuario) {
    $errores = [];
    if (empty(trim($nombre))) $errores[] = "El nombre es obligatorio.";
    if (empty(trim($apellido))) $errores[] = "El apellido es obligatorio.";
    if (empty(trim($correo))) $errores[] = "El correo es obligatorio.";
    if (empty($tipo_usuario)) $errores[] = "El tipo de usuario es obligatorio.";
    return $errores;
}

function actualizar_usuario($id_usuario, $nombre, $apellido, $correo, $tipo_usuario) {
    require_once("../../PHP/conexion.php");
    $conexion = conectar_bd();
    $sql = "UPDATE usuario SET nombre=?, apellido=?, correo=?, tipo_usuario=? WHERE id_usuario=?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $apellido, $correo, $tipo_usuario, $id_usuario);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $resultado;
}

function procesar_edicion_usuario() {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_usuario'])) {
        $id_usuario = intval($_POST['id_usuario']);
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $correo = trim($_POST['correo']);
        $tipo_usuario = $_POST['tipo_usuario'];

        $errores = validar_datos_usuario($nombre, $apellido, $correo, $tipo_usuario);
        if (!empty($errores)) {
            echo "<script>alert('" . implode(' ', $errores) . "'); window.location.href='../HTML/gestionUsr.php';</script>";
            exit;
        }

        if (actualizar_usuario($id_usuario, $nombre, $apellido, $correo, $tipo_usuario)) {
            echo "<script>alert('Usuario actualizado correctamente.'); window.location.href='../HTML/gestionUsr.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar el usuario.'); window.location.href='../HTML/gestionUsr.php';</script>";
        }
        exit;
    } else {
        header("Location: ../HTML/gestionUsr.php");
        exit;
    }
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    procesar_edicion_usuario();
}
?>