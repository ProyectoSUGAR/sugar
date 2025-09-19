<?php
include '../../PHP/header.php';
require("../../PHP/conexion.php");
require("../PHP/asignaturaFunciones.php");

$conn = conectar_bd();

// Procesa acciones si hay POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case "crear":
                crearAsignatura($conn, $_POST['nombre']);
                break;
            case "editar":
                editarAsignatura($conn, $_POST['id_asignatura'], $_POST['nombre']);
                break;
            case "eliminar":
                eliminarAsignatura($conn, $_POST['id_asignatura']);
                break;
        }
    }
}

$asignaturas = obtenerAsignaturas($conn);

$editar = false;
$nombre_editar = '';
$id_editar = '';
if (isset($_GET['editar']) && $_GET['editar'] != '') {
    $editar = true;
    $id_editar = intval($_GET['editar']);
    foreach ($asignaturas as $a) {
        if ($a['id_asignatura'] == $id_editar) {
            $nombre_editar = $a['nombre'];
            break;
        }
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asignaturas</title>
    <link rel="stylesheet" href="../CSS/asignacion.css">
</head>
<body>
<main class="sugarads-main">
    <h1 class="sugarads-title">Gestión de Asignaturas</h1>
    <div class="sugarads-grid registro-datos">
        <section class="sugarads-col-left">
            <!-- Formulario para crear o editar asignaturas -->
            <form class="sugarads-form" method="post" action="">
                <h2 class="sugarads-section-title"><?= $editar ? "Editar Asignatura" : "Nueva Asignatura" ?></h2>
                <div class="sugarads-field">
                    <label for="nombre" class="sugarads-label">Nombre de la asignatura</label>
                    <input type="text" id="nombre" name="nombre" class="input-text" required placeholder="Ejemplo: Matemática" value="<?= htmlspecialchars($nombre_editar) ?>">
                </div>
                <?php if ($editar): ?>
                    <input type="hidden" name="id_asignatura" value="<?= $id_editar ?>">
                    <input type="hidden" name="accion" value="editar">
                    <div class="sugarads-field">
                        <button type="submit" class="sugarads-btn sugarads-btn-guardar">Guardar Cambios</button>
                        <a href="asignacionAsig.php" class="sugarads-btn sugarads-btn-cancelar">Cancelar</a>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="accion" value="crear">
                    <div class="sugarads-field">
                        <button type="submit" class="sugarads-btn sugarads-btn-guardar">Registrar</button>
                        <button type="reset" class="sugarads-btn sugarads-btn-cancelar">Cancelar</button>
                    </div>
                <?php endif; ?>
            </form>
            <br>
            <!-- Listado y acciones -->
            <div class="sugarads-entradas sombra">
                <h2>Asignaturas registradas</h2>
                <?php if ($asignaturas): ?>
                    <ul>
                    <?php foreach ($asignaturas as $a): ?>
                        <li>
                            <?= htmlspecialchars($a['nombre']) ?>
                            <a href="asignacionAsig.php?editar=<?= $a['id_asignatura'] ?>" class="sugarads-btn sugarads-btn-editar">Editar</a>
                            <form method="post" action="" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta asignatura?');">
                                <input type="hidden" name="id_asignatura" value="<?= $a['id_asignatura'] ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="sugarads-btn sugarads-btn-eliminar">Eliminar</button>
                            </form>
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