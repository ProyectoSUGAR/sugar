<?php 
include '../../HEADERS/headerP.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />    
<title>Dashboard Profesor</title>
    <!-- Enlace al archivo de estilos CSS principal del sistema -->
    <link rel="stylesheet" href="/Css/style.css" />
  </head>
    <!-- Material Icons CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!-- Inclusión del bloque de bienvenida personalizado para el usuario profesor -->
  <?php include '../../Profesor/HTML/bienvenida.php'; ?>
  <body>
    <!-- Inclusión del panel de navegación y funcionalidades del dashboard -->
    <?php include '../../PHP/dashboard.php'; ?>
    <!-- El header y menú hamburguesa ya están incluidos por header_profesor.php -->
    <!-- El bloque de planos y horarios ya está incluido por dashboard.php, así que el selector de día y la grilla funcionarán igual para el profesor -->
    
    <!-- Aseguramos que el script de planos_horarios se cargue correctamente para el dashboard de profesor -->
}
    <!-- Inclusión del script de estadísticas unificadas para el profesor -->
    
  </body>
</html>
