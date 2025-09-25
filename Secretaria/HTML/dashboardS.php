<?php include '../../PHP/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Secretaria</title>
    <link rel="stylesheet" href="/Css/style.css" />
    <!-- Material Icons CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- El header y menú hamburguesa ya están incluidos por header_secretaria.php -->
</head>
<body>
    <main>
        <!-- Inclusión del bloque de bienvenida personalizado para la secretaria -->
        <?php include '../../PHP/bienvenida.php'; ?>

        <!-- Sección principal que muestra las estadísticas del sistema -->
        <section class="bloque-estadisticas">
            <!-- Título de la sección de estadísticas -->
            <h2 class="titulo-estadisticas">Estadísticas</h2>
            <!-- Contenedor en forma de cuadrícula para organizar los bloques estadísticos -->
            <div class="estadisticas-grid">
                <!-- Bloque que muestra el número de alumnos registrados -->
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="alumnos">0</div>
                    <div class="estadistica-label">alumnos<br>registrados</div>
                </div>
                <!-- Bloque que muestra el número de profesores registrados -->
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="profesores">0</div>
                    <div class="estadistica-label">Profesores<br>registrados</div>
                </div>
                <!-- Bloque que muestra el número de grupos registrados -->
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="grupos">0</div>
                    <div class="estadistica-label">Grupos<br>registrados</div>
                </div>
                <!-- Bloque gráfico que muestra las ausencias semanales de profesores -->
                <div class="estadistica-item estadistica-grafico" style="grid-column: 2 / span 2; grid-row: 1 / span 2;">
                    <div class="grafico-titulo">Profesores que no han asistido esta semana</div>
                    <!-- Canvas donde se renderiza el gráfico de ausencias -->
                    <canvas id="grafico-profesores" width="400" height="120"></canvas>
                </div>
                <!-- Bloque que muestra el número de secretarios registrados -->
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="secretarios">0</div>
                    <div class="estadistica-label">Secretarios<br>registrados</div>
                </div>
                <!-- Bloque que muestra el número de salones libres actualmente -->
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="salones_libres">0</div>
                    <div class="estadistica-label">Salones libres<br>en este momento</div>
                </div>
                <!-- Bloque que muestra el número de profesores presentes en la institución -->
                <div class="estadistica-item">
                    <div class="estadistica-numero" data-tipo="profesores_presentes">0</div>
                    <div class="estadistica-label">Profesores presentes<br>en la institución</div>
                </div>
            </div> <!-- Fin del contenedor de estadísticas -->
        </section>

        <!-- Grilla de horarios registrados -->
        <section class="bloque-planos-horarios">
            <div class="selector-piso">
                <button type="button" class="btn-piso activo" data-piso="0">Planta baja</button>
                <button type="button" class="btn-piso" data-piso="1">Piso 1</button>
                <button type="button" class="btn-piso" data-piso="2">Piso 2</button>
            </div>
            <div class="contenedor-plano">
                <img id="imagen-plano" src="/Images/PlantaBaja.jpeg" alt="Plano Planta baja" />
            </div>
            <div id="contenedor-tablas-horarios"></div>
        </section>
    </main>
    <!-- Inclusión del panel de navegación o funcionalidades adicionales del dashboard -->
    <?php include '../../PHP/dashboard.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/JS/planos_horarios.js"></script>
    <script src="/Secretaria/JS/estadisticas.js"></script>
</body>
</html>