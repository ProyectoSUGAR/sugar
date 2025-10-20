<?php 
// Inclusión del encabezado común que contiene configuraciones compartidas
include '../../HEADERS/headerD.php'; 
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

    <!-- Inclusión del bloque de bienvenida personalizado para el usuario secretaria -->
    <?php include '../../Direccion/HTML/bienvenida.php'; ?>

    <!-- Bloque visual que muestra las estadísticas institucionales -->
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

        <!-- Bloque gráfico que muestra la distribución de clases por día -->
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

      </div> <!-- Fin del contenedor de estadísticas -->
      
      <!-- Descripción del gráfico -->
      <div style="text-align: center; margin: 20px 0;">
        <p style="color: #666; font-size: 14px;">
          Este gráfico muestra la distribución de clases por turno y día de la semana.
          Cada barra representa la cantidad de clases programadas, diferenciadas por color según el turno (Mañana, Tarde, Noche).
        </p>
      </div>
    </section>
    <!-- Fin del bloque de estadísticas -->

    
    <!-- Fin sección bloques de horarios -->

    <!-- Aquí va el contenido específico para secretaria -->
    
  </main>

  <!-- Inclusión del panel de navegación y funcionalidades del dashboard -->
  <?php include '../../PHP/dashboard.php'; ?>

  <!-- Inclusión de la librería AnyChart para renderizar gráficos -->
  <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-base.min.js"></script>
  <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-ui.min.js"></script>
  <link rel="stylesheet" href="https://cdn.anychart.com/releases/8.11.0/css/anychart-ui.min.css">

  <!-- Inclusión del script que actualiza dinámicamente las estadísticas -->
  <script src="../../Direccion/JS/estadisticas.js"></script>
  <script src="../../JS/planos_horarios.js"></script>

</body>
</html>
