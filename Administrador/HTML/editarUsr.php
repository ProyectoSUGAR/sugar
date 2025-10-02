<?php
require_once("../../PHP/conexion.php");
$conexion = conectar_bd();

$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
$usuario = null;

if ($id_usuario > 0) {
    $resultado = mysqli_query($conexion, "SELECT * FROM usuario WHERE id_usuario=$id_usuario");
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);
    }
}

if (!$usuario) {
    echo "<h2 style='color:red;text-align:center;'>Usuario no encontrado.</h2>";
    if (isset($conexion) && $conexion instanceof mysqli) {
        if (@$conexion->ping()) {

        }
    }
    exit;
}
?>
<?php include '../../PHP/header_admin.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../../Css/style.css">
</head>
<body class="body-login">
    <div class="contenedor-principal">
        <main class="contenido-principal">
            <section class="panel-formulario" style="max-width:400px;">
                <h2 class="titulo-panel">Editar Usuario</h2>
                <form method="post" action="../PHP/editarUsr.php">
                    <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                    <div class="campo-con-icono">
                        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required placeholder="Nombre">
                    </div>
                    <div class="campo-con-icono">
                        <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required placeholder="Apellido">
                    </div>
                    <div class="campo-con-icono">
                        <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required placeholder="Correo">
                    </div>
                    <div class="campo-con-icono">
                        <select name="tipo_usuario" required>
                            <option value="alumno" <?= $usuario['tipo_usuario']=='alumno'?'selected':'' ?>>Alumno</option>
                            <option value="adscripta" <?= $usuario['tipo_usuario']=='adscripta'?'selected':'' ?>>Adscripta</option>
                            <option value="direccion" <?= $usuario['tipo_usuario']=='direccion'?'selected':'' ?>>Director</option>
                            <option value="secretaria" <?= $usuario['tipo_usuario']=='secretaria'?'selected':'' ?>>Secretaria</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-accion btn-primario" style="width:100%;">Guardar cambios</button>
                </form>
            </section>
        </main>
    </div>
    <script src="/JS/menuHamburguesa.js"></script>
</body>
</html>
<?php
if (isset($conexion) && $conexion instanceof mysqli) {
    if (@$conexion->ping()) {

    }
}
?>