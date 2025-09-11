<?php 
// Inclusión del encabezado común que contiene configuraciones y elementos compartidos
include '../../PHP/header.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />    
<title>Dashboard Profesor</title>
    
    <!-- Enlace al archivo de estilos CSS principal del sistema -->
    <link rel="stylesheet" href="/Css/style.css" />
  </head>

  <!-- Inclusión del bloque de bienvenida personalizado para el usuario profesor -->
  <?php include '../../PHP/bienvenida.php'; ?>

  <body>

    <!-- Inclusión del panel de navegación y funcionalidades del dashboard -->
    <?php include '../../PHP/dashboard.php'; ?>

  </body>
</html>
