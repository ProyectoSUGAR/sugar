<?php
include '../../HEADERS/headerAA.php';
require_once("../../PHP/conexion.php");
$conexion = conectar_bd();

// Filtros
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

$consulta = "SELECT id_usuario, nombre, apellido, correo, tipo_usuario, estado_usuario FROM usuario WHERE 1";
if ($busqueda) {
    $consulta .= " AND (nombre LIKE '%$busqueda%' OR apellido LIKE '%$busqueda%' OR correo LIKE '%$busqueda%')";
}
if ($estado) {
    $consulta .= " AND estado_usuario='$estado'";
}
if ($tipo) {
    $consulta .= " AND tipo_usuario='$tipo'";
}
$consulta .= " ORDER BY nombre, apellido";
$resultado_usuarios = mysqli_query($conexion, $consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="../../Css/gestionUsr.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="body-login">
    <div class="contenedor-gestion-usuarios">
        <main class="contenido-principal">
            <section class="panel-formulario-gestion">
                <h2 class="titulo-panel">Gestión de Usuarios</h2>
                <form method="get" class="grupo-doble-campo">
                    <input type="text" name="busqueda" placeholder="Buscar por nombre, apellido o correo" value="<?= htmlspecialchars($busqueda) ?>">
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="activo" <?= $estado=='activo'?'selected':'' ?>>Activos</option>
                        <option value="inactivo" <?= $estado=='inactivo'?'selected':'' ?>>Inactivos</option>
                    </select>
                    <select name="tipo">
                        <option value="">Todos</option>
                        <option value="alumno" <?= $tipo=='alumno'?'selected':'' ?>>Alumno</option>
                        <option value="adscripta" <?= $tipo=='adscripta'?'selected':'' ?>>Adscripta</option>
                        <option value="direccion" <?= $tipo=='direccion'?'selected':'' ?>>Director</option>
                        <option value="secretaria" <?= $tipo=='secretaria'?'selected':'' ?>>Secretaria</option>
                    </select>
                    <button type="submit" class="btn-accion"><i class="fa fa-search"></i></button>
                </form>
                <div class="tabla-usuarios-wrapper">
                    <table class="tabla-usuarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Correo</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)): ?>
                            <tr>
                                <td><?= $usuario['id_usuario'] ?></td>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                                <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                <td><?= htmlspecialchars($usuario['tipo_usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['estado_usuario']) ?></td>
                                <td>
                                    <form method="post" action="../PHP/funcionGestion.php" class="accion-form" style="display:inline;">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                        <input type="hidden" name="estado_usuario" value="<?= $usuario['estado_usuario'] ?>">
                                        <button type="submit" name="cambiar_estado" class="btn-accion" title="Cambiar estado">
                                            <i class="fa <?= $usuario['estado_usuario'] === 'activo' ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                        </button>
                                    </form>
                                    <form method="post" action="../PHP/funcionGestion.php" class="accion-form eliminar-form" style="display:inline;">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                        <button type="submit" name="eliminar_usuario" class="btn-accion btn-eliminar" title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    <form method="get" action="../HTML/editarUsr.php" style="display:inline;">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                        <button type="submit" class="btn-accion" title="Editar">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align:center; margin-top:24px;">
                    <a href="reporteUsr.php" class="btn-volver-reporte">
                        <i class="fa fa-arrow-left"></i> Volver al reporte
                    </a>
                    <a href="historialActividad.php" class="btn-historial-actividad" style="margin-left:16px;">
                        <i class="fa fa-clock-rotate-left"></i> Ver historial de cambios
                    </a>
                </div>
            </section>
        </main>
    </div>
    <script src="../JS/gestionUsr.js"></script>
</body>
</html>
<?php
if (isset($conexion) && $conexion instanceof mysqli) {
    if (@$conexion->ping()) {

    }
}
?>