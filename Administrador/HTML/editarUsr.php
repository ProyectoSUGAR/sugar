<?php
include '../../HEADERS/headerAA.php';
require_once("../../PHP/conexion.php");
$conn = conectar_bd();

if (!isset($_GET['id_usuario'])) {
    header("Location: gestionUsr.php");
    exit;
}

$id_usuario = intval($_GET['id_usuario']);
$query = "SELECT id_usuario, nombre, apellido, correo, tipo_usuario FROM usuario WHERE id_usuario = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

if (!$usuario) {
    header("Location: gestionUsr.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <!-- Estilos movidos a Css/style.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
</head>
<body class="body-login">
    <br>
    <div class="body-login">
        <div class="contenedor-principal">
            <main class="contenido-principal">
                <section class="panel-formulario-gestion">
                    <h2 class="titulo-panel">Editar Usuario</h2>
                    <form method="post" action="../../Administrador/PHP/editarUsr.php" class="formulario-edicion">
                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                        </div>
                        <div class="campo-formulario">
                            <label for="apellido">Apellido:</label>
                            <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                        </div>
                        <div class="campo-formulario">
                            <label for="correo">Correo:</label>
                            <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                        </div>
                        <div class="campo-formulario">
                            <label for="tipo_usuario">Tipo de Usuario:</label>
                            <select id="tipo_usuario" name="tipo_usuario" required>
                                <option value="alumno" <?= $usuario['tipo_usuario'] == 'alumno' ? 'selected' : '' ?>>Alumno</option>
                                <option value="adscripta" <?= $usuario['tipo_usuario'] == 'adscripta' ? 'selected' : '' ?>>Adscripta</option>
                                <option value="direccion" <?= $usuario['tipo_usuario'] == 'direccion' ? 'selected' : '' ?>>Director</option>
                                <option value="secretaria" <?= $usuario['tipo_usuario'] == 'secretaria' ? 'selected' : '' ?>>Secretaria</option>
                                <option value="profesor" <?= $usuario['tipo_usuario'] == 'profesor' ? 'selected' : '' ?>>Profesor</option>
                            </select>
                        </div>
                        <div class="botones-formulario">
                            <button type="submit" class="btn-accion"><i class="fa fa-save"></i> Guardar Cambios</button>
                            <a href="gestionUsr.php" class="btn-accion btn-cancelar"><i class="fa fa-times"></i> Cancelar</a>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.formulario-edicion');
            form.addEventListener('submit', function(evento) {
                evento.preventDefault();

                const formData = new FormData(form);

                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Actualizando usuario...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar solicitud mediante fetch
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message || 'Usuario actualizado correctamente',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = 'gestionUsr.php';
                        });
                    } else {
                        throw new Error(data.message || 'Error al procesar la solicitud');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Ha ocurrido un error al procesar la solicitud',
                        allowOutsideClick: false
                    });
                });
            });
        });
    </script>
</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    if (@$conn->ping()) {
    }
}
?>
