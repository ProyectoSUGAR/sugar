<?php 
// Inclusión del encabezado común que contiene configuraciones compartidas
include '../../PHP/header1.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>S.U.G.A.R.</title>
    <link rel="stylesheet" href="../../Css/style.css" />
    <link rel="icon" href="../../Images/Logo22-removebg-preview.png" />
    <!-- Enlace a la librería de íconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Inclusión de la librería SweetAlert para mostrar alertas visuales -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="body-login">
    <!-- Contenedor general que agrupa todo el contenido -->
    <div class="contenedor-principal">
        <!-- Área principal de contenido -->
        <main class="contenido-principal">
            <!-- Panel que contiene el formulario de registro -->
            <section class="panel-formulario">
                <!-- Grupo de pestañas para cambiar entre login y registro -->
                <div class="grupo-pestanas">
                    <a class="pestana-inactiva" href="../../Login/HTML/ingreso.php">Ingresar</a>
                    <a class="pestana-activa" href="../../Login/HTML/registro.php">Registrarse</a>
                </div>
                <!-- Formulario de registro de usuario -->
                <form id="formulario-registro" method="post" action="../../Login/HTML/registro.php" class="formulario-registro">
                    <!-- Fila con campos para nombre y apellido -->
                    <div class="fila-doble">
                        <div class="campo-con-icono">
                            <input type="text" name="nombre" placeholder="Nombre" required>
                        </div>
                        <div class="campo-con-icono">
                            <input type="text" name="apellido" placeholder="Apellido" required>
                        </div>
                    </div>
                    <!-- Campo para ingresar la cédula del usuario -->
                    <div class="campo-con-icono">
                        <input type="text" name="cedula" placeholder="Cédula" maxlength="8" required>
                    </div>
                    <!-- Campo para ingresar el correo electrónico -->
                    <div class="campo-con-icono">
                        <input type="email" name="correo" placeholder="Correo" required>
                    </div>
                    <!-- Campo para ingresar la contraseña -->
                    <div class="campo-con-icono">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    <!-- Campo para confirmar la contraseña -->
                    <div class="campo-con-icono">
                        <input type="password" name="confirmaPassword" placeholder="Confirmar contraseña" required>
                    </div>
                    <!-- Selector de horario preferido -->
                    <div class="campo-con-icono">
                        <select name="horario" required>
                            <option value="" disabled selected>Seleccione horario</option>
                            <option value="mañana">Mañana</option>
                            <option value="tarde">Tarde</option>
                            <option value="noche">Noche</option>
                        </select>
                    </div>
                    <!-- Selector del tipo de usuario que se está registrando -->
                    <div class="campo-con-icono">
                        <select name="tipo_usuario" id="tipo_usuario" required>
                            <option value="">Seleccione tipo de usuario</option>
                            <option value="administrador">Administrador</option>
                            <option value="adscripta">Adscripta</option>
                            <option value="alumno">Alumno</option>
                            <option value="profesor">Profesor</option>
                            <option value="secretaria">Secretaria</option>
                            <option value="direccion">Dirección</option>
                            <option value="funcionario">Funcionario</option>
                        </select>
                    </div>
                    <!-- Botón para enviar el formulario de registro -->
                    <button type="submit" class="btn-primario">Registrarse</button>
                </form>
                <!-- Script que gestiona la validación de campos del formulario -->
                <script src="../../Login/JavaScript/registro_campos.js"></script>
            </section>
            <!-- Panel lateral que contiene el carrusel visual -->
            <section class="panel-carrusel">
                <div class="carrusel">
                    <!-- Imagen institucional que se muestra en el carrusel -->
                    <img src="../../Images/fondo.png" alt="Imagen institucional" class="imagen-carrusel" />
                    <!-- Texto descriptivo del carrusel -->
                    <div class="texto-carrusel">
                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde perspiciati.
                    </div>
                    <!-- Botones para navegar entre imágenes del carrusel -->
                    <button class="flecha-carrusel izquierda"><</button>
                    <button class="flecha-carrusel derecha">></button>
                </div>
            </section>
        </main>
    </div>
    <!-- Script que gestiona el comportamiento del formulario de registro -->
    <script src="../../Login/JavaScript/registro.js"></script>
    <!-- Script que muestra u oculta campos según el tipo de usuario seleccionado -->
    <script src="../../Login/JavaScript/mostrarCampos.js"></script>
</body>
</html>

// Este es el código de form.php de la parte PHP, se movió a esta pagina para el metodo post y las alertas.

<?php

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
    echo '<script>';
   echo ' Swal.fire({';
 echo 'title: "Se registró exitosamente!",';
 echo 'icon: "success",';
  echo'draggable: true';
echo '});';
    echo '</script>';
        } else {
            echo "Error: " . $consulta_insertar . "<br>" . mysqli_error($con);
        }
    } else {
            echo '<script>';
   echo ' Swal.fire({';
 echo 'title: "Su usuario ya está registrado",';
 echo 'icon: "error",';
  echo'draggable: true';
echo '});';
    echo '</script>';
    }


}

// Cerrar la conexión a la base de datos
if (isset($con) && $con instanceof mysqli) {
    if (@$con->ping()) {

    }
}


?>

