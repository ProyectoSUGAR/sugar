<?php
// Inclusión del encabezado común que contiene configuraciones compartidas
include '../../PHP/header1.php';

// llamado a la conexion con la base de datos
require_once("../../PHP/conexion.php");
$con = conectar_bd();

// Inicializar variables para persistencia de formulario
$valores = [
    'nombre' => '',
    'apellido' => '',
    'correo' => '',
    'cedula' => '',
    'tipo_usuario' => ''
];

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    // Recoger y sanear entradas
    $valores['nombre'] = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $valores['apellido'] = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $valores['correo'] = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $valores['cedula'] = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
    $contrasenia = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmaPassword = isset($_POST['confirmaPassword']) ? $_POST['confirmaPassword'] : '';
    $valores['tipo_usuario'] = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : '';

    // Validaciones backend
    if (mb_strlen(preg_replace('/[^a-zA-Z]/u', '', $valores['nombre'])) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 letras.';
    }
    if (empty($valores['tipo_usuario'])) {
        $errores[] = 'Debes seleccionar un tipo de usuario.';
    }
    if ($contrasenia !== $confirmaPassword) {
        $errores[] = 'Las contraseñas no coinciden.';
    }
    if (!(strlen($contrasenia) >= 6 && preg_match('/[A-Z]/', $contrasenia) && preg_match('/[a-z]/', $contrasenia) && preg_match('/[0-9]/', $contrasenia))) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un número.';
    }
    // Validar cédula: solo dígitos y longitud entre 7 y 8
    if (!preg_match('/^[0-9]{7,8}$/', $valores['cedula'])) {
        $errores[] = 'La cédula debe contener solo números y tener entre 7 y 8 dígitos.';
    }

    // Si errores, se mostrarán más abajo en JS SweetAlert y el formulario mantendrá valores
    if (empty($errores)) {
        // Verificar si el usuario ya existe
        $correo_db = mysqli_real_escape_string($con, $valores['correo']);
        $cedula_db = mysqli_real_escape_string($con, $valores['cedula']);
        $consulta = "SELECT id_usuario FROM usuario WHERE correo = '$correo_db' OR cedula = '$cedula_db'";
        $resultado = mysqli_query($con, $consulta);
        if (mysqli_num_rows($resultado) > 0) {
            $errores[] = 'El usuario ya está registrado.';
        } else {
            // Insertar
            $nombre_db = mysqli_real_escape_string($con, $valores['nombre']);
            $apellido_db = mysqli_real_escape_string($con, $valores['apellido']);
            $hash = password_hash($contrasenia, PASSWORD_DEFAULT);
            $tipo_db = mysqli_real_escape_string($con, $valores['tipo_usuario']);
            $insertar = "INSERT INTO usuario (nombre, apellido, correo, cedula, contrasenia, horario, tipo_usuario, estado_usuario) VALUES ('$nombre_db', '$apellido_db', '$correo_db', '$cedula_db', '$hash', '', '$tipo_db', 'activo')";
            if (mysqli_query($con, $insertar)) {
                // Éxito: redirigir o mostrar alerta success
                echo '<script>window.onload = function(){ Swal.fire({icon: "success", title: "Se registró exitosamente!", timer: 1600, showConfirmButton:false}).then(()=>{ window.location.href="../../Login/HTML/ingreso.php"; }); }</script>';
                // Evitar reenvío posterior
                $valores = ['nombre'=>'','apellido'=>'','correo'=>'','cedula'=>'','tipo_usuario'=>''];
            } else {
                $errores[] = 'Error al registrar usuario: ' . mysqli_error($con);
            }
        }
    }
}

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
                <form id="formulario-registro" method="post" action="" class="formulario-registro">
                    <!-- Fila con campos para nombre y apellido -->
                    <div class="fila-doble">
                        <div class="campo-con-icono">
                            <label for="nombre"></label>
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required maxlength="50" value="<?php echo htmlspecialchars($valores['nombre']); ?>">
                        </div>
                        <div class="campo-con-icono">
                            <label for="apellido"></label>
                            <input type="text" id="apellido" name="apellido" placeholder="Apellido" required maxlength="50" value="<?php echo htmlspecialchars($valores['apellido']); ?>">
                        </div>
                    </div>
                    <!-- Campo para ingresar la cédula del usuario -->
                    <div class="campo-con-icono">
                        <label for="cedula"></label>
                        <input type="text" id="cedula" name="cedula" placeholder="Cédula" maxlength="8" required pattern="[0-9]{7,8}" value="<?php echo htmlspecialchars($valores['cedula']); ?>">
                    </div>
                    <!-- Campo para ingresar el correo electrónico -->
                    <div class="campo-con-icono">
                        <label for="correo"></label>
                        <input type="email" id="correo" name="correo" placeholder="Correo" required maxlength="100" value="<?php echo htmlspecialchars($valores['correo']); ?>">
                    </div>
                    <!-- Campo para ingresar la contraseña -->
                    <div class="campo-con-icono">
                        <label for="password"></label>
                        <input type="password" id="password" name="password" placeholder="Contraseña" required maxlength="64">
                    </div>
                    <!-- Campo para confirmar la contraseña -->
                    <div class="campo-con-icono">
                        <label for="confirmaPassword"> </label>
                        <input type="password" id="confirmaPassword" name="confirmaPassword" placeholder="Confirmar contraseña" required maxlength="64">
                    </div>
                    <!-- Selector del tipo de usuario que se está registrando -->
                    <div class="campo-con-icono">
                        <select name="tipo_usuario" id="tipo_usuario" required>
                            <option value="">Seleccione tipo de usuario</option>
                            <option value="administrador" <?php echo $valores['tipo_usuario'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                            <option value="adscripta" <?php echo $valores['tipo_usuario'] === 'adscripta' ? 'selected' : ''; ?>>Adscripta</option>
                            <option value="alumno" <?php echo $valores['tipo_usuario'] === 'alumno' ? 'selected' : ''; ?>>Alumno</option>
                            <option value="profesor" <?php echo $valores['tipo_usuario'] === 'profesor' ? 'selected' : ''; ?>>Profesor</option>
                            <option value="secretaria" <?php echo $valores['tipo_usuario'] === 'secretaria' ? 'selected' : ''; ?>>Secretaria</option>
                            <option value="direccion" <?php echo $valores['tipo_usuario'] === 'direccion' ? 'selected' : ''; ?>>Dirección</option>
                            <option value="funcionario" <?php echo $valores['tipo_usuario'] === 'funcionario' ? 'selected' : ''; ?>>Funcionario</option>
                        </select>
                    </div>
                    <!-- Botón para enviar el formulario de registro -->
                    <button type="submit" class="btn-primario">Registrarse</button>
                </form>
                <!-- Script que gestiona la validación de campos del formulario -->
                <script src="../../Login/JS/registro.js"></script>
                <?php if (!empty($errores)): ?>
                    <script>
                        window.onload = function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de registro',
                                html: <?php echo json_encode(implode('<br>', array_map('htmlspecialchars', $errores))); ?>
                            });
                        };
                    </script>
                <?php endif; ?>
            </section>
            <!-- Panel lateral que contiene el carrusel visual -->
        </main>
    </div>
    <!-- Script que muestra u oculta campos según el tipo de usuario seleccionado -->
    <script src="../../Login/JS/mostrarCampos.js"></script>
</body>
</html>

