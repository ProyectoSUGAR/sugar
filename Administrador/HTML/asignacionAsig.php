<?php
include '../../HEADERS/headerAA.php';
require_once("../../PHP/conexion.php");
$con = conectar_bd();
$editar = false;
$nombre_editar = '';
$id_editar = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    if ($accion === 'crear' && !empty($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);
        $stmt = mysqli_prepare($con, "INSERT INTO asignatura (nombre) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $nombre);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../../Administrador/HTML/asignacionAsig.php");
        exit;
    } elseif ($accion === 'editar' && !empty($_POST['id_asignatura']) && !empty($_POST['nombre'])) {
        $id = intval($_POST['id_asignatura']);
        $nombre = trim($_POST['nombre']);
        $stmt = mysqli_prepare($con, "UPDATE asignatura SET nombre = ? WHERE id_asignatura = ?");
        mysqli_stmt_bind_param($stmt, "si", $nombre, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../../Administrador/HTML/asignacionAsig.php");
        exit;
    } elseif ($accion === 'eliminar' && !empty($_POST['id_asignatura'])) {
        $id = intval($_POST['id_asignatura']);
        $stmt = mysqli_prepare($con, "DELETE FROM asignatura WHERE id_asignatura = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../../Administrador/HTML/asignacionAsig.php");
        exit;
    }
}
if (isset($_GET['editar'])) {
    $editar = true;
    $id_editar = intval($_GET['editar']);
    $res = mysqli_query($con, "SELECT * FROM asignatura WHERE id_asignatura = $id_editar LIMIT 1");
    if ($fila = mysqli_fetch_assoc($res)) {
        $nombre_editar = $fila['nombre'];
    }
}
$asignaturas = [];
$res = mysqli_query($con, "SELECT * FROM asignatura ORDER BY nombre ASC");
while ($fila = mysqli_fetch_assoc($res)) {
    $asignaturas[] = $fila;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de asignaturas</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Styles moved to Css/style.css -->
</head>
<body class="bodyregidat">
    <main class="sugarads-main">
        <h1 class="sugarads-title">Gestión de asignaturas</h1>
        <div class="cards-horizontal">
            <section class="sugar-card">
                <form class="formasig" method="post" action="../../Administrador/HTML/asignacionAsig.php">
                    <h2 class="h2asiges"><?= $editar ? "Editar asignatura" : "Nueva asignatura" ?></h2>
                    <div class="sugarads-field">
                        <input type="text" id="nombre" name="nombre" class="inputasig" required placeholder="Ejemplo: Matemática" value="<?= htmlspecialchars($nombre_editar) ?>">
                    </div>
                    <?php if ($editar): ?>
                        <input type="hidden" name="id_asignatura" value="<?= $id_editar ?>">
                        <input type="hidden" name="accion" value="editar">
                        <div class="sugarads-field">
                            <button type="submit" class="btn-primario">Guardar Cambios</button>
                            <a href="asignacionAsig.php" class="btn-secundario btn-cancel">Cancelar</a>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="accion" value="crear">
                        <div class="sugarads-field">
                            <button type="submit" class="btn-primario">Registrar</button>
                            <button type="reset" class="btn-secundario">Cancelar</button>
                        </div>
                    <?php endif; ?>
                </form>
            </section>
            <section class="sugar-card card-half">
                <div class="sugarads-entradas">
                    <h2 class="h2asiges1">Asignaturas registradas</h2>
                    <?php if ($asignaturas): ?>
                        <ul>
                       <?php foreach ($asignaturas as $a): ?>
                            <li class="pruebads">
                                <span class="asignatura-nombre">
                                    <?= htmlspecialchars($a['nombre']) ?>
                                </span>
                                <div class="action-buttons">
                                    <a href="../../Administrador/HTML/asignacionAsig.php?editar=<?= $a['id_asignatura'] ?>"
                                       class="btn-secundario"
                                       class="btn-secundario btn-icon">
                                        <span class="material-icons icon-sm">edit</span>
                                        Editar
                                    </a>
                                    <form method="post" action="" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta asignatura?');">
                                        <input type="hidden" name="id_asignatura" value="<?= $a['id_asignatura'] ?>">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <button type="submit" class="btn-secundario btn-icon">
                                            <span class="material-icons icon-sm">delete</span>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay asignaturas registradas.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
