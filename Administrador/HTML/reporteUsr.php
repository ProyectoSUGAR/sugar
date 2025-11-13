<?php
include '../../HEADERS/headerAA.php';
require_once("../../PHP/conexion.php");
$conn = conectar_bd();
$tipo_usuario = isset($_GET['tipo_usuario']) ? $_GET['tipo_usuario'] : '';
$estado_usuario = isset($_GET['estado_usuario']) ? $_GET['estado_usuario'] : '';
$query = "SELECT id_usuario, nombre, apellido, correo, tipo_usuario, estado_usuario, fecha_registro FROM usuario WHERE 1";
if ($tipo_usuario) {
    $query .= " AND tipo_usuario = '$tipo_usuario'";
}
if ($estado_usuario) {
    $query .= " AND estado_usuario = '$estado_usuario'";
}
$query .= " ORDER BY fecha_registro DESC";
$resultado = mysqli_query($conn, $query);
$usuarios = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $usuarios[] = $fila;
}
$total_usuarios = count($usuarios);
$activos = array_filter($usuarios, function($u) { return $u['estado_usuario'] == 'activo'; });
$inactivos = array_filter($usuarios, function($u) { return $u['estado_usuario'] == 'inactivo'; });
$por_tipo = [];
foreach ($usuarios as $u) {
    $tipo = $u['tipo_usuario'];
    if (!isset($por_tipo[$tipo])) {
        $por_tipo[$tipo] = 0;
    }
    $por_tipo[$tipo]++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Usuarios</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="body-login">
    <div class="contenedor-reporte">
        <h1 class="titulo-reporte">Reporte de Usuarios</h1>
        <form method="get" class="filtros-reporte">
            <div class="filtro-grupo">
                <label for="tipo_usuario">Tipo de Usuario:</label>
                <select name="tipo_usuario" id="tipo_usuario">
                    <option value="">Todos</option>
                    <option value="alumno" <?= $tipo_usuario == 'alumno' ? 'selected' : '' ?>>Alumno</option>
                    <option value="profesor" <?= $tipo_usuario == 'profesor' ? 'selected' : '' ?>>Profesor</option>
                    <option value="adscripta" <?= $tipo_usuario == 'adscripta' ? 'selected' : '' ?>>Adscripta</option>
                    <option value="direccion" <?= $tipo_usuario == 'direccion' ? 'selected' : '' ?>>Dirección</option>
                    <option value="secretaria" <?= $tipo_usuario == 'secretaria' ? 'selected' : '' ?>>Secretaría</option>
                </select>
            </div>
            <div class="filtro-grupo">
                <label for="estado_usuario">Estado:</label>
                <select name="estado_usuario" id="estado_usuario">
                    <option value="">Todos</option>
                    <option value="activo" <?= $estado_usuario == 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $estado_usuario == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <button type="submit" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="reporteUsr.php" class="btn-limpiar"><i class="fas fa-times"></i> Limpiar</a>
        </form>
        <div class="estadisticas-reporte">
            <div class="estadistica-card">
                <h3>Total de Usuarios</h3>
                <p class="numero"><?= $total_usuarios ?></p>
            </div>
            <div class="estadistica-card">
                <h3>Usuarios Activos</h3>
                <p class="numero"><?= count($activos) ?></p>
            </div>
            <div class="estadistica-card">
                <h3>Usuarios Inactivos</h3>
                <p class="numero"><?= count($inactivos) ?></p>
            </div>
        </div>
        <div class="grafico-reporte">
            <h2>Distribución por Tipo de Usuario</h2>
            <canvas id="chartTipoUsuario"></canvas>
        </div>
        <div class="tabla-reporte">
            <h2>Lista de Usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['correo']) ?></td>
                        <td><?= htmlspecialchars($usuario['tipo_usuario']) ?></td>
                        <td><span class="estado <?= $usuario['estado_usuario'] ?>"><?= htmlspecialchars($usuario['estado_usuario']) ?></span></td>
                        <td><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="acciones-reporte">
            <a href="gestionUsr.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver a Gestión de Usuarios</a>
            <button onclick="window.print()" class="btn-imprimir"><i class="fas fa-print"></i> Imprimir Reporte</button>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('chartTipoUsuario').getContext('2d');
        const data = {
            labels: <?= json_encode(array_keys($por_tipo)) ?>,
            datasets: [{
                label: 'Número de Usuarios',
                data: <?= json_encode(array_values($por_tipo)) ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribución por Tipo de Usuario'
                    }
                }
            }
        };
        new Chart(ctx, config);
    </script>
</body>
</html>
