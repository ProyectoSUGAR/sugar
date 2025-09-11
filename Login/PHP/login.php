<?php

// Mostrar todos los errores en pantalla (modo desarrollo)
// ini_set establece el valor de una directiva de configuración en tiempo de ejecución
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// error_reporting define el nivel de errores que se mostrarán
error_reporting(E_ALL);

// Inclusión del archivo que contiene la función para conectar con la base de datos
require("../../PHP/conexion.php");

// Establece la conexión con la base de datos
$con = conectar_bd();

// Procesa el formulario de inicio de sesión si se enviaron los campos requeridos
// isset verifica si las variables 'usuario' y 'password' están definidas y no son null
if (isset($_POST["usuario"]) && isset($_POST["password"])) {
    // Obtiene los datos ingresados por el usuario en el formulario
    $usuario = $_POST["usuario"];
    $contrasenia = $_POST["password"];

    // Llama a la función que valida las credenciales del usuario
    logear($con, $usuario, $contrasenia);
}

// Función que obtiene los datos del usuario desde la base de datos
function traer_datos_usuario($con, $usuario) {
    // Escapa caracteres especiales para evitar inyecciones SQL
    $usuario = mysqli_real_escape_string($con, $usuario);

    // Consulta SQL que busca al usuario por correo o cédula
    $sql = "SELECT * FROM usuario WHERE correo = '$usuario' OR cedula = '$usuario'";

    // Ejecuta la consulta en la base de datos
    $resultado = mysqli_query($con, $sql);

    // Obtiene la primera fila del resultado como array asociativo
    $row = mysqli_fetch_array($resultado);

    // Verifica si se encontró al menos un usuario
    if (mysqli_num_rows($resultado) > 0) {
        // Retorna los datos del usuario en forma de array
        return [
            'id' => $row['id_usuario'],
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'correo' => $row['correo'],
            'cedula' => $row['cedula'],
            'contrasenia' => $row['contrasenia'],
            'tipo_usuario' => $row['tipo_usuario'],
            'horario' => $row['horario'],
            'estado_usuario' => $row['estado_usuario']
        ];
    } else {
        // Retorna null si no se encontró ningún usuario
        return null;
    }
}

// Función que valida las credenciales del usuario y redirige según su rol
function logear($con, $usuario, $contrasenia) {
    // Obtiene los datos del usuario desde la base de datos
    $datos_usr = traer_datos_usuario($con, $usuario);

    // Verifica si el usuario existe y si la contraseña es correcta
    if ($datos_usr) {
        $password_bd = $datos_usr["contrasenia"];

        // Verifica que la contraseña ingresada coincida con la almacenada
        if (password_verify($contrasenia, $password_bd)) {
            // Inicia la sesión y guarda los datos del usuario
            session_start();
            $_SESSION["id_usuario"] = $datos_usr['id'];
            $_SESSION["email"] = $datos_usr['correo'];
            $_SESSION["usuario"] = $datos_usr['nombre'];
            $_SESSION["tipo_usuario"] = $datos_usr['tipo_usuario'];
            $_SESSION["horario"] = $datos_usr['horario'];

            // Redirige al dashboard correspondiente según el tipo de usuario
            switch ($datos_usr['tipo_usuario']) {
                case "administrador":
                    header("Location: ../../Administrador/HTML/dashboardAr.php");
                    break;
                case "funcionario":
                    header("Location: ../../Funcionario/HTML/dashboardF.php");
                    break;
                case "secretaria":
                    header("Location: ../../Secretaria/HTML/dashboardS.php");
                    break;
                case "profesor":
                    header("Location: ../../Profesor/HTML/dashboardP.php");
                    break;
                case "alumno":
                    header("Location: ../../Estudiante/HTML/dashboardE.php");
                    break;
                case "direccion":
                    header("Location: ../../Direccion/HTML/dashboardD.php");
                    break;
                case "adscripta":
                    header("Location: ../../Adscripta/HTML/dashboardA.php");
                    break;
                default:
                    // Redirige al login si el tipo de usuario no está definido
                    header("Location: ../HTML/index.php");
            }

            // Finaliza la ejecución del script después de redirigir
            exit();
        } else {
            // Mensaje si la contraseña es incorrecta
            echo "Contraseña incorrecta";
        }
    } else {
        // Mensaje si no se encuentra el usuario
        echo "Usuario no encontrado";
    }
}

// Cierra la conexión con la base de datos
mysqli_close($con);
?>