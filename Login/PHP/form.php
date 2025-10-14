<?php
// Manejador de registro de usuario
require_once("../../PHP/conexion.php");
echo "ec";
$conn = conectar_bd();

// Procesar registro
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // recoger y sanitizar campos
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $cedula = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
    $contrasenia = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmaPassword = isset($_POST['confirmaPassword']) ? $_POST['confirmaPassword'] : '';
    $horario = isset($_POST['horario']) ? $_POST['horario'] : '';
    $tipo_usuario = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : '';

    // Validaciones basicas
    if ($contrasenia !== $confirmaPassword) {
        echo "<script>alert('Las contraseñas no coinciden.'); window.location.href='../../Login/HTML/registro.php';</script>";
        exit;
    }

    if (empty($correo) || empty($contrasenia) || empty($nombre) || empty($apellido)) {
        echo "<script>alert('Complete los campos requeridos.'); window.location.href='../../Login/HTML/registro.php';</script>";
        exit;
    }

    // Verificar si el usuario ya existe
    $existe_usr = consultar_existe_usr($conn, $correo, $cedula);

    if ($existe_usr) {
        echo "<script>alert('El usuario ya existe.'); window.location.href='../../Login/HTML/registro.php';</script>";
        exit;
    }

    // Insertar usuario
    $insert_ok = insertar_datos($conn, $nombre, $apellido, $correo, $cedula, $contrasenia, $horario, $tipo_usuario);

    if ($insert_ok) {
        // Redirigir al login con mensaje de exito
        echo "<script>alert('Registro exitoso.'); window.location.href='../../Login/HTML/ingreso.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error al registrar usuario.'); window.location.href='../../Login/HTML/registro.php';</script>";
        exit;
    }
}

// Comprueba si existe usuario por correo o cédula
function consultar_existe_usr($conn, $correo, $cedula) {
    $correo = mysqli_real_escape_string($conn, $correo);
    $cedula = mysqli_real_escape_string($conn, $cedula);

    $consulta = "SELECT id_usuario FROM usuario WHERE correo = '$correo' OR cedula = '$cedula'";
    $resultado = mysqli_query($conn, $consulta);

    return $resultado && mysqli_num_rows($resultado) > 0;
}

// Inserta usuario con password hasheada
function insertar_datos($conn, $nombre, $apellido, $correo, $cedula, $contrasenia, $horario, $tipo_usuario) {
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $apellido = mysqli_real_escape_string($conn, $apellido);
    $correo = mysqli_real_escape_string($conn, $correo);
    $cedula = mysqli_real_escape_string($conn, $cedula);
    $horario = mysqli_real_escape_string($conn, $horario);
    $tipo_usuario = mysqli_real_escape_string($conn, $tipo_usuario);

    $hash = password_hash($contrasenia, PASSWORD_DEFAULT);

    $consulta_insertar = "INSERT INTO usuario (nombre, apellido, correo, cedula, contrasenia, horario, tipo_usuario, estado_usuario) VALUES ('$nombre', '$apellido', '$correo', '$cedula', '$hash', '$horario', '$tipo_usuario', 'activo')";

    return mysqli_query($conn, $consulta_insertar);
}

// cerrar conexion
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}
