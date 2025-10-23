<?php
require_once(__DIR__ . '/../PHP/funcProfes.php');
$profesores = obtenerProfesores();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Profesores</title>
</head>
<body>
    <?php include '../../HEADERS/headerP.php'; ?>
    <h1>Lista de Profesores</h1>
    <div class="profesores-list" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: flex-start;">
        <?php if (empty($profesores)): ?>
            <p>No hay profesores registrados.</p>
        <?php else: ?>
            <?php foreach ($profesores as $prof): ?>
                <div class="profesor-card" style="border: 1px solid #ccc; border-radius: 8px; padding: 16px; width: 260px; background: #f9f9f9; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <h2 style="margin: 0 0 8px 0; font-size: 1.2em; color: #333;">
                        <?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']) ?>
                    </h2>
                    <strong>Asignaturas:</strong>
                    <p style="margin: 8px 0 0 0; color: #555;">
                        <?= htmlspecialchars($prof['asignaturas'] ?? 'Sin asignaturas') ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>