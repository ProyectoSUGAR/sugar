<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../../PHP/conexion.php");
$conn = conectar_bd();
if (isset($_POST["usuario"]) && isset($_POST["password"])) {
    $usuario = $_POST["usuario"];
    $contrasenia = $_POST["password"];
    logear($conn, $usuario, $contrasenia);
}
function traer_datos_usuario($conn, $usuario) {
    $usuario = mysqli_real_escape_string($conn, $usuario);
    $sql = "SELECT * FROM usuario WHERE correo = '$usuario' OR cedula = '$usuario'";
    $resultado = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($resultado);
    if (mysqli_num_rows($resultado) > 0) {
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
        return null;
    }
}
function logear($conn, $usuario, $contrasenia) {
    $datos_usr = traer_datos_usuario($conn, $usuario);
    if ($datos_usr) {
        $password_bd = $datos_usr["contrasenia"];
        if (password_verify($contrasenia, $password_bd)) {
            session_start();
            $_SESSION["id_usuario"] = $datos_usr['id'];
            $_SESSION["email"] = $datos_usr['correo'];
            $_SESSION["usuario"] = $datos_usr['nombre'];
            $_SESSION["tipo_usuario"] = $datos_usr['tipo_usuario'];
            $_SESSION["horario"] = $datos_usr['horario'];
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
                    echo '<script>';
                    echo 'Swal.fire({';
                    echo 'icon: "error",';
                    echo 'title: "Error de inicio de sesión",';
                    echo 'text: "Tipo de usuario no definido. Contacte al administrador."';
                    echo '}).then(function(){ window.location.href = "../../HTML/index.php"; });';
                    echo '</script>';
                    exit();
            }
            exit();
        } else {
            echo '<script>';
            echo 'Swal.fire({';
            echo 'icon: "error",';
            echo 'title: "Error de inicio de sesión",';
            echo 'text: "Contraseña incorrecta."';
            echo '});';
            echo '</script>';
        }
    } else {
    echo '<!DOCTYPE html><html lang="es"><head>';
    echo '<meta charset="UTF-8" />';
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '</head><body>';
    echo '<script>';
    echo 'Swal.fire({';
    echo 'icon: "error",';
    echo 'title: "Error de inicio de sesión",';
    echo 'text: "Usuario no encontrado."';
    echo '}).then(function(){ window.location.href = "../../Login/HTML/ingreso.php"; });';
    echo '</script>';
    echo '<noscript><div style="color:red;text-align:center;margin-top:2em;">Usuario no encontrado.<br><a href="../../Login/HTML/ingreso.php">Volver</a></div></noscript>';
    echo '</body></html>';
    }
}
if (isset($conn) && $conn instanceof mysqli) {
    if (@$conn->ping()) {
    }
}
?>
