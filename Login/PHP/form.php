<?php
// llamado a la conexion con la base de datos
require_once("../../PHP/conexion.php");

// conexion con la base de datos
$con = conectar_bd();

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos del formulario
    $correo = trim($_POST['correo']);
    $contraseña = trim($_POST['contraseña']);

    // Consulta para buscar el usuario por correo y contraseña
    $query = "SELECT * FROM usuario WHERE correo='$correo' AND contraseña='$contraseña'";
    $result = mysqli_query($con, $query);
    $usuario = mysqli_fetch_assoc($result);

    if ($usuario) {
        // Validar si el usuario está activo
        if ($usuario['estado_usuario'] !== 'activo') {
            // Usuario inactivo, no permitir acceso
            echo "<script>alert('Usuario inactivo. Contacte al administrador.'); window.location.href='../../HTML/ingreso.php';</script>";
            exit;
        }
        // Usuario activo, iniciar sesión
        session_start();
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
        // Redirigir al panel principal
        header("Location: ../../panel.php");
        exit;
    } else {
        // Usuario o contraseña incorrectos
        echo "<script>alert('Correo o contraseña incorrectos.'); window.location.href='../../HTML/ingreso.php';</script>";
        exit;
    }
}

if (isset($con) && $con instanceof mysqli) {
  if (@$con->ping()) {

  }
}

// llamado a la conexion con la base de datos
// require es una funcion que incluye y evalua el archivo especificado
require_once("../../PHP/conexion.php");

// conexion con la base de datos
$con = conectar_bd();

// Procesar el formulario cuando se envíe
// $_SERVER["REQUEST_METHOD"] es una variable superglobal que contiene el método de solicitud utilizado para acceder a la página
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $correo = $_POST["correo"];
    $cedula = $_POST["cedula"];
    $contrasenia = $_POST["password"];
    $confirmaPassword = $_POST["confirmaPassword"];
    $horario = $_POST["horario"];
    $tipo_usuario = $_POST["tipo_usuario"];

    // Validar que las contraseñas coincidan
    if ($contrasenia !== $confirmaPassword) {
        echo "Las contraseñas no coinciden.";
        exit();
    }

    // Verificar si el usuario ya existe
    // consultar_existe_usr es una funcion que verifica si el usuario ya existe en la base de datos
    $existe_usr = consultar_existe_usr($con, $correo, $cedula);

    // insertar_datos es una funcion que inserta los datos en la base de datos 
    insertar_datos($con, $nombre, $apellido, $correo, $cedula, $contrasenia, $horario, $tipo_usuario, $existe_usr);
}

// funcion consultar si el usuario ya existe en la base de datos
function consultar_existe_usr($con, $correo, $cedula) {
    // proteger contra inyeccion SQL
    // mysqli_real_escape_string es una funcion que escapa caracteres especiales en una cadena para usarla en una consulta SQL
    $correo = mysqli_real_escape_string($con, $correo);

    // verificar si el usuario ya existe en la base de datos
    // mysqli_real_escape_string es una funcion que escapa caracteres especiales en una cadena para usar
    $cedula = mysqli_real_escape_string($con, $cedula);

    // consulta SQL para verificar si el usuario ya existe
    // mysqli_query es una funcion que ejecuta la consulta SQL
    $consulta = "SELECT id_usuario FROM usuario WHERE correo = '$correo' OR cedula = '$cedula'";

    // ejecutar la consulta SQL
    // mysqli_query es una funcion que ejecuta la consulta SQL
    $resultado = mysqli_query($con, $consulta);

    // retornar true si el usuario ya existe, false si no existe
    //mysqli_num_rows es una funcion que obtiene el numero de filas de un resultado de una consulta SQL
    return mysqli_num_rows($resultado) > 0;
}

// funcion insertar los datos en la base de datos
function insertar_datos($con, $nombre, $apellido, $correo, $cedula, $contrasenia, $horario, $tipo_usuario, $existe_usr) {
    // si el usuario no existe, insertar los datos en la base de datos
    if (!$existe_usr) {
        // proteger contra inyeccion SQL
        // mysqli_real_escape_string es una funcion que escapa caracteres especiales en una cadena para usarla en una consulta SQL
        $correo = mysqli_real_escape_string($con, $correo);
        $cedula = mysqli_real_escape_string($con, $cedula);

        // hashear la contraseña
        // password_hash es una funcion que crea un hash de la contraseña
        $contrasenia = password_hash($contrasenia, PASSWORD_DEFAULT);
        
        // proteger contra inyeccion SQL
        $horario = mysqli_real_escape_string($con, $horario);
        $tipo_usuario = mysqli_real_escape_string($con, $tipo_usuario);

        // consulta SQL para insertar los datos en la base de datos
        // mysqli_query es una funcion que ejecuta la consulta SQL
        $consulta_insertar = "INSERT INTO usuario (nombre, apellido, correo, cedula, contrasenia, horario, tipo_usuario, estado_usuario) VALUES ('$nombre', '$apellido', '$correo', '$cedula', '$contrasenia', '$horario', '$tipo_usuario', 'activo')";

        // ejecutar la consulta SQL y verificar si se insertaron los datos correctamente
        // mysqli_query es una funcion que ejecuta la consulta SQL
        if (mysqli_query($con, $consulta_insertar)) {
            echo "Registro exitoso.";
        } else {
            echo "Error: " . $consulta_insertar . "<br>" . mysqli_error($con);
        }
    } else {
        echo "El usuario ya existe.";
    }


}

// Cerrar la conexión a la base de datos
if (isset($con) && $con instanceof mysqli) {
    if (@$con->ping()) {

    }
}

?>
