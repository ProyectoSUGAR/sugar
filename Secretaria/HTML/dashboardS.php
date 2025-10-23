<?php 
// Inclusión del encabezado común para la interfaz de la secretaria
include '../../HEADERS/headerS.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Secretaria</title>
    <link rel="stylesheet" href="../../Css/style.css" />
</head>
<body>
    <main>
        <!-- Inclusión del bloque de bienvenida personalizado para la secretaria -->
        <?php include '../../Secretaria/HTML/bienvenida.php'; ?>

        <!-- Sección principal que muestra las estadísticas del sistema -->
        <section class="bloque-estadisticas">
            <!-- Título de la sección de estadísticas -->
            <h2 class="titulo-estadisticas" id="estadisticas">Estadísticas</h2>
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
                <!-- Bloque gráfico que muestra la distribución de clases -->
                <div class="estadistica-item estadistica-grafico" style="grid-column: 2 / span 2; grid-row: 1 / span 2; padding: 20px; background-color: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 15px; text-align: center;">Distribución de Clases</h3>
                    <div id="container" style="width: 100%; height: 400px;"></div>
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
            </div>
            <div style="text-align: center; margin: 20px 0;">
                <p style="color: #666; font-size: 14px;">
                  Este gráfico muestra la distribución de clases por turno y día de la semana.
                  Cada barra representa la cantidad de clases programadas, diferenciadas por color según el turno (Mañana, Tarde, Noche).
                </p>
            </div>
        </section>

        <!-- Fin del bloque de estadísticas -->
        <!-- Sección de bloques de horarios (planos) -->
        <!-- SOLO UNA INSTANCIA, REPARADO -->
        <section class="bloque-horarios">
          <h2 class="titulo-horarios">Bloques de horarios</h2>
          <div style="margin-bottom: 15px;">
            <label for="selector-dia">Día:</label>
            <select id="selector-dia">
              <option value="lunes">Lunes</option>
              <option value="martes">Martes</option>
              <option value="miercoles">Miércoles</option>
              <option value="jueves">Jueves</option>
              <option value="viernes">Viernes</option>
            </select>
            <span style="margin-left: 20px;">Piso:</span>
            <button class="btn-piso activo" data-piso="0">Planta Baja</button>
            <button class="btn-piso" data-piso="1">Piso 1</button>
            <button class="btn-piso" data-piso="2">Piso 2</button>
          </div>
          <div id="contenedor-tablas-horarios"></div>
          <div style="margin-top: 20px; text-align: center;">
            <img id="imagen-plano" src="../../Images/PlantaBaja.jpeg" alt="Plano del piso" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          </div>
        </section>
        <!-- Fin sección bloques de horarios -->
    </main>
    <?php include '../../PHP/dashboard.php'; ?>
    <!-- Inclusión de las librerías de AnyChart para renderizar gráficos -->
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdn.anychart.com/releases/8.11.0/css/anychart-ui.min.css">
    <!-- Inclusión del script que actualiza dinámicamente las estadísticas -->
    <script src="../../Direccion/JS/estadisticas.js"></script>
    <!-- Inclusión del script que muestra los planos de horarios -->
    <script src="../../JS/planos_horarios.js"></script>
</body>
</html>
