<?php
require_once("../../PHP/conexion.php");
$conexion = conectar_bd();
$consulta = "SELECT tipo_usuario, COUNT(*) as cantidad FROM usuario GROUP BY tipo_usuario";
$resultado = mysqli_query($conexion, $consulta);
$lista_roles = [];
if ($resultado) {
    foreach (mysqli_fetch_all($resultado, MYSQLI_ASSOC) as $fila_rol) {
        $lista_roles[] = $fila_rol;
    }
}
$rol_seleccionado = '';
if (isset($_GET['tipo']) && is_string($_GET['tipo'])) {
    $rol_seleccionado = trim($_GET['tipo']);
}
if ($rol_seleccionado === '' && !empty($lista_roles)) {
    $rol_seleccionado = $lista_roles[0]['tipo_usuario'];
}
$export = isset($_GET['export']) ? $_GET['export'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$usuarios = [];
$total = 0;
if ($rol_seleccionado !== '') {
    $safe_rol = mysqli_real_escape_string($conexion, $rol_seleccionado);
    $offset = ($page - 1) * $perPage;
    $countQuery = "SELECT COUNT(*) as total FROM usuario WHERE tipo_usuario='" . $safe_rol . "'";
    $rCount = mysqli_query($conexion, $countQuery);
    if ($rCount) {
        $totalRow = mysqli_fetch_assoc($rCount);
        $total = (int)$totalRow['total'];
    }
    $queryUsuarios = "SELECT id_usuario, nombre, apellido, correo, tipo_usuario, estado_usuario FROM usuario WHERE tipo_usuario='" . $safe_rol . "' ORDER BY nombre, apellido LIMIT $perPage OFFSET $offset";
    $rUsuarios = mysqli_query($conexion, $queryUsuarios);
    if ($rUsuarios) {
        while ($row = mysqli_fetch_assoc($rUsuarios)) {
            $usuarios[] = $row;
        }
    }
}
if ($export === 'csv' && $rol_seleccionado !== '') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=usuarios_' . preg_replace('/[^a-z0-9_-]/i', '', $rol_seleccionado) . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Nombre', 'Apellido', 'Correo', 'Tipo', 'Estado']);
    $safe_rol = mysqli_real_escape_string($conexion, $rol_seleccionado);
    $r = mysqli_query($conexion, "SELECT id_usuario, nombre, apellido, correo, tipo_usuario, estado_usuario FROM usuario WHERE tipo_usuario='" . $safe_rol . "' ORDER BY nombre, apellido");
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            fputcsv($out, [$row['id_usuario'], $row['nombre'], $row['apellido'], $row['correo'], $row['tipo_usuario'], $row['estado_usuario']]);
        }
    }
    fclose($out);
    exit;
}
?>
<?php include '../../HEADERS/headerAA.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios por Rol</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body class="body-login">
    <div class="contenedor-reporte-usuarios">
        <h2 class="titulo-panel">Usuarios por Rol</h2>
        <?php if (empty($lista_roles)): ?>
            <p class="mensaje">No se encontraron roles o no hay usuarios registrados.</p>
        <?php else: ?>
        <form method="get" id="formRol" class="form-rol">
            <label for="tipo">Selecciona un rol:</label>
            <select name="tipo" id="tipo">
                <?php foreach ($lista_roles as $rol): ?>
                    <option value="<?= htmlspecialchars($rol['tipo_usuario']) ?>" <?= $rol['tipo_usuario'] == $rol_seleccionado ? 'selected' : '' ?> >
                        <?= htmlspecialchars(ucfirst($rol['tipo_usuario'])) ?> (<?= (int)$rol['cantidad'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <a class="btn-export" href="?tipo=<?= urlencode($rol_seleccionado) ?>&export=csv"><i class="fa fa-file-csv"></i> Exportar CSV</a>
        </form>
        <table class="tabla-reporte-usuarios">
            <thead>
                <tr>
                    <th>Rol</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista_roles as $rol): ?>
                <tr <?= $rol['tipo_usuario'] == $rol_seleccionado ? 'class="resaltado"' : '' ?> >
                    <td><?= htmlspecialchars(ucfirst($rol['tipo_usuario'])) ?></td>
                    <td><?= (int)$rol['cantidad'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <section class="listado-usuarios">
            <h3>Detalle: <?= htmlspecialchars(ucfirst($rol_seleccionado)) ?> (<?= $total ?>)</h3>
            <?php if ($total === 0): ?>
                <p class="mensaje">No hay usuarios para este rol.</p>
            <?php else: ?>
                <div class="tabla-usuarios-wrapper">
                    <table class="tabla-usuarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= (int)$u['id_usuario'] ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['apellido']) ?></td>
                                <td><?= htmlspecialchars($u['correo']) ?></td>
                                <td><?= htmlspecialchars($u['estado_usuario']) ?></td>
                                <td>
                                    <form method="get" action="gestionUsr.php" style="display:inline-block;">
                                        <input type="hidden" name="tipo" value="<?= htmlspecialchars($rol_seleccionado) ?>">
                                        <input type="hidden" name="busqueda" value="<?= urlencode($u['correo']) ?>">
                                        <button type="submit" class="btn-accion" title="Ver en gestión"><i class="fa fa-eye"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php $pages = (int)ceil($total / $perPage); ?>
                <?php if ($pages > 1): ?>
                    <div class="paginacion">
                        <?php if ($page > 1): ?>
                            <a href="?tipo=<?= urlencode($rol_seleccionado) ?>&page=<?= $page-1 ?>" class="page">&laquo; Anterior</a>
                        <?php endif; ?>
                        <span>Pagina <?= $page ?> de <?= $pages ?></span>
                        <?php if ($page < $pages): ?>
                            <a href="?tipo=<?= urlencode($rol_seleccionado) ?>&page=<?= $page+1 ?>" class="page">Siguiente &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
        <form method="get" action="gestionUsr.php" style="margin-top:16px;text-align:center;">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($rol_seleccionado) ?>">
            <button type="submit" class="btn-ver-detalle">Ir a Gestión de usuarios</button>
        </form>
        <?php endif; ?>
    </div>
    <script>
        document.getElementById('tipo')?.addEventListener('change', function() {
            document.getElementById('formRol').submit();
        });
    </script>
</body>
</html>
<?php
if (isset($conexion) && $conexion instanceof mysqli) {
    if (@$conexion->ping()) {
    }
}
?>