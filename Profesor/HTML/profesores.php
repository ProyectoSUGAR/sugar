<?php
// Iniciar sesión si no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../Login/HTML/ingreso.php');
    exit();
}

// Verificar que sea profesor
if ($_SESSION['tipo_usuario'] !== 'profesor') {
    header('Location: ../../index.html');
    exit();
}

require_once(__DIR__ . '/../PHP/funcProfes.php');
$profesores = obtenerProfesores();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Profesores</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
    <?php include '../../HEADERS/headerP.php'; ?>
    <main class="container-profesores">
        <div class="profesores-header">
            <h1 class="profesores-title">
                <i class="fas fa-chalkboard-user"></i>
                Lista de Profesores
            </h1>
            <p class="profesores-subtitle">Directorio completo del personal docente</p>
        </div>

        <div class="profesores-content">
            <div class="profesores-list">
                <?php if (empty($profesores)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No hay profesores registrados en el sistema.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($profesores as $prof): ?>
                        <div class="profesor-card">
                            <div class="profesor-card-header">
                                <div class="profesor-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="profesor-info">
                                    <h2 class="profesor-nombre">
                                        <?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']) ?>
                                    </h2>
                                    <span class="profesor-badge">Docente</span>
                                </div>
                            </div>
                            <div class="profesor-card-body">
                                <div class="profesor-section">
                                    <label>Asignaturas:</label>
                                    <p class="profesor-asignaturas">
                                        <i class="fas fa-book"></i>
                                        <?= htmlspecialchars($prof['asignaturas'] ?? 'Sin asignaturas asignadas') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>