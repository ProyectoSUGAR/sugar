<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>S.U.G.A.R. - Recuperar Contraseña</title>
    <link rel="stylesheet" href="/Css/style.css" />
    <link rel="icon" href="/Images/Logo22-removebg-preview.png" />
    <!-- Enlace a la librería de íconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Inclusión de la librería SweetAlert para mostrar alertas visuales -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="body-login">
    <!-- Contenedor general que agrupa todo el contenido -->
    <div class="contenedor-principal">
        <!-- Inclusión del encabezado común del sistema -->
        <?php include '../../PHP/Header.php'; ?>
        <!-- Área principal de contenido -->
        <main class="contenido-principal">
            <!-- Panel que contiene el formulario de recuperación de contraseña -->
            <section class="panel-formulario">
                <!-- Pestaña activa que indica la sección actual -->
                <div class="grupo-pestanas">
                    <a class="pestana-activa" href="/Login/HTML/Recuperacion.php">Recuperar contraseña</a>
                </div>
                <!-- Formulario para solicitar recuperación de contraseña -->
                <form id="formulario-recuperar" class="formulario-registro" method="post" action="/Login/PHP/recuperacionContr.php">
                    <!-- Campo para ingresar el correo electrónico -->
                    <div class="campo-con-icono">
                        <input type="email" placeholder="Ingresa tu correo" id="correo" name="correo" required />
                        <i class="fas fa-envelope"></i> <!-- Ícono decorativo de sobre -->
                    </div>
                    <!-- Botón para enviar el formulario de recuperación -->
                    <button type="submit" class="btn-primario">Enviar enlace de recuperación</button>
                    <!-- Enlace para volver al formulario de login -->
                    <div class="recuperar-password">
                        <a href="/Login/HTML/index.php">Volver a ingresar</a>
                    </div>
                </form>
                <!-- Este div parece estar incompleto o innecesario -->
                <div class="campo-con-icono"></div>
            </section>
            <!-- Panel lateral que contiene el carrusel visual -->
            <section class="panel-carrusel">
                <div class="carrusel">
                    <!-- Imagen institucional que se muestra en el carrusel -->
                    <img src="/Login/Images/fondo.png" alt="Imagen institucional" class="imagen-carrusel" />
                    <!-- Texto descriptivo del carrusel -->
                    <div class="texto-carrusel">
                        Recupera tu acceso de forma segura y rápida.
                    </div>
                    <!-- Botones para navegar entre imágenes del carrusel -->
                    <button class="flecha-carrusel izquierda">&lt;</button>
                    <button class="flecha-carrusel derecha">&gt;</button>
                </div>
            </section>
        </main>
    </div>
    <!-- Script que gestiona funcionalidades generales de la página -->
    <script src="/Login/JavaScript/app.js"></script>
</body>
</html>