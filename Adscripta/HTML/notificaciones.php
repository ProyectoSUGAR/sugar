<?php include '../../HEADERS/headerAA.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="../../Css/style.css">
</head>
<body>
    <main class="sugarads-main">
        <div class="sugarads-section titulo">
            <h2>Gestión de Notificaciones</h2>
            <form id="formNoti" method="POST" action="../../Adscripta/PHP/funcNotifi.php">
                <label for="mensaje">Mensaje:</label><br>
                <textarea name="mensaje" id="mensaje" required></textarea><br>
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" required>
                    <option value="alerta">Alerta</option>
                    <option value="recordatorio">Recordatorio</option>
                    <option value="informativa">Informativa</option>
                </select>
                <label for="destinatario_tipo">Destinatario:</label>
                <select name="destinatario_tipo" id="destinatario_tipo" required>
                    <option value="todos">Todos</option>
                    <option value="alumnos">Alumnos</option>
                    <option value="profesores">Profesores</option>
                    <option value="funcionarios">Funcionarios</option>
                    <option value="adscripta">Adscripta</option>
                    <option value="direccion">Dirección</option>
                    <option value="secretaria">Secretaría</option>
                </select>
                <button type="submit" name="crear_notificacion">Crear Notificación</button>
            </form>
            <hr>
            <h3>Historial de Notificaciones</h3>
            <div id="tabla-notificaciones">
                <?php include '../../Adscripta/PHP/funcNotifi.php'; ?>
            </div>
        </div>
    </main>
</body>
</html>
