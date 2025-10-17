<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Configuración de codificación de caracteres -->
  <meta charset="UTF-8" />

  <!-- Configuración para diseño responsivo en dispositivos móviles -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <!-- Título de la pestaña del navegador -->
  <title>S.U.G.A.R.</title>

  <!-- Enlace al archivo de estilos principal del sistema -->
  <link rel="stylesheet" href="../../Css/style.css" />

  <!-- Ícono que se muestra en la pestaña del navegador -->
  <link rel="icon" href="../../Images/Logo22-removebg-preview.png" />

  <!-- Enlace a la librería de íconos Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <!-- Inclusión de la librería SweetAlert para mostrar alertas visuales -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Inclusión de Bootstrap para estilos y componentes interactivos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<!-- Cuerpo principal de la página con clase específica para el login -->
<body class="body-login">

  <!-- Contenedor general que agrupa todo el contenido -->
  <div class="contenedor-principal">

    <!-- Inclusión del encabezado común del sistema -->
    <?php include '../../PHP/header1.php'; ?>

    <!-- Área principal de contenido -->
    <main class="contenido-principal">

      <!-- Panel que contiene el formulario de ingreso -->
      <section class="panel-formulario">

        <!-- Grupo de pestañas para alternar entre login y registro -->
        <div class="grupo-pestanas">
          <a class="pestana-activa" href="../../Login/HTML/ingreso.php">Ingresar</a>
          <a class="pestana-inactiva" href="../../Login/HTML/registro.php">Registrarse</a>
        </div>

        <!-- Formulario de ingreso al sistema -->
        <form id="formulario-ingreso" class="formulario-registro" method="post" action="../../Login/PHP/login.php">

          <!-- Campo para ingresar cédula o correo -->
          <div class="campo-con-icono">
            <input type="text" placeholder="Cédula o correo" id="usuario" name="usuario" required />
            <i class="fas fa-id-card"></i> <!-- Ícono decorativo -->
          </div>

          <!-- Campo para ingresar la contraseña -->
          <div class="campo-con-icono">
            <input type="password" placeholder="Contraseña" id="password" name="password" required />
            <i class="fas fa-lock"></i> <!-- Ícono decorativo -->
          </div>

          <!-- Botón para enviar el formulario de ingreso -->
          <button type="submit" class="btn-primario">Ingresar</button>

          <!-- Enlace para recuperar la contraseña -->
          <div class="recuperar-password">
            <a href="../../Login/HTML/Recuperacion.php">¿Olvidaste tu contraseña?</a>
          </div>
        </form>
      </section>

      <!-- Panel lateral que contiene el carrusel visual -->
      <section class="panel-carrusel">
        <div id="carruselBD" class="carousel slide carrusel" data-bs-ride="carousel">

          <!-- Contenedor de las imágenes del carrusel -->
          <div class="carousel-inner">
          </div>

          <!-- Botón para navegar a la imagen anterior del carrusel -->
          <button class="carousel-control-prev flecha-carrusel izquierda" type="button" data-bs-target="#carruselBD" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </button>

          <!-- Botón para navegar a la imagen siguiente del carrusel -->
          <button class="carousel-control-next flecha-carrusel derecha" type="button" data-bs-target="#carruselBD" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
          </button>

        </div>
      </section>

    </main>
  </div>

  <!--<script src="/Login/JavaScript/login.js"></script>-->

</body>
</html>
