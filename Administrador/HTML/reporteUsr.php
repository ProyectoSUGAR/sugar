<?php
include '../../PHP/header.php';
require_once("../../PHP/conexion.php");
$conexion = conectar_bd();

// Consulta para contar usuarios por rol
$consulta = "SELECT tipo_usuario, COUNT(*) as cantidad FROM usuario GROUP BY tipo_usuario";
$resultado = mysqli_query($conexion, $consulta);

$lista_roles = [];
foreach (mysqli_fetch_all($resultado, MYSQLI_ASSOC) as $fila_rol) {
    $lista_roles[] = $fila_rol;
}

// Rol seleccionado
$rol_seleccionado = isset($_GET['tipo']) ? $_GET['tipo'] : ($lista_roles[0]['tipo_usuario'] ?? '');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios por Rol</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="../../Css/reporteUsr.css">
</head>
<body class="body-login">
    <div class="contenedor-reporte-usuarios">
        <h2 class="titulo-panel">Usuarios por Rol</h2>
        <form method="get" id="formRol" style="margin-bottom:20px;">
            <label for="tipo" style="color:#fff;">Selecciona un rol:</label>
            <select name="tipo" id="tipo" style="margin-left:10px;">
                <?php foreach ($lista_roles as $rol): ?>
                    <option value="<?= $rol['tipo_usuario'] ?>" <?= $rol['tipo_usuario'] == $rol_seleccionado ? 'selected' : '' ?> >
                        <?= ucfirst($rol['tipo_usuario']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
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
                <tr <?= $rol['tipo_usuario'] == $rol_seleccionado ? 'style="font-weight:bold;background:#e3c39d;color:#071739;"' : '' ?> >
                    <td><?= ucfirst($rol['tipo_usuario']) ?></td>
                    <td><?= $rol['cantidad'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="get" action="gestionUsr.php" style="margin-top:24px;text-align:center;">
            <input type="hidden" name="tipo" value="<?= $rol_seleccionado ?>">
            <button type="submit" class="btn-ver-detalle">Ver detalles de este rol</button>
        </form>
    </div>
    <script>
        // Cambia el rol seleccionado y recarga la tabla
        document.getElementById('tipo').addEventListener('change', function() {
            document.getElementById('formRol').submit();
        });
    </script>
</body>
</html>
<?php
if (isset($con) && $con instanceof mysqli) {
    if (@$con->ping()) {

    }
}
?>