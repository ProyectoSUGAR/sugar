<?php 
// Inclusión del encabezado común que contiene configuraciones compartidas
include '../../PHP/Header.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>S.U.G.A.R.</title>
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
        <!-- Área principal de contenido -->
        <main class="contenido-principal">
            <!-- Panel que contiene el formulario de registro -->
            <section class="panel-formulario">
                <!-- Grupo de pestañas para cambiar entre login y registro -->
                <div class="grupo-pestanas">
                    <a class="pestana-inactiva" href="/Login/HTML/index.php">Ingresar</a>
                    <a class="pestana-activa" href="/Login/HTML/registro.php">Registrarse</a>
                </div>
                <!-- Formulario de registro de usuario -->
                <form id="formulario-registro" method="post" action="/Login/PHP/form.php" class="formulario-registro">
                    <!-- Fila con campos para nombre y apellido -->
                    <div class="fila-doble">
                        <div class="campo-con-icono">
                            <input type="text" name="nombre" placeholder="Nombre" required>
                        </div>
                        <div class="campo-con-icono">
                            <input type="text" name="apellido" placeholder="Apellido" required>
                        </div>
                    </div>
                    <!-- Campo para ingresar la cédula del usuario -->
                    <div class="campo-con-icono">
                        <input type="text" name="cedula" placeholder="Cédula" maxlength="8" required>
                    </div>
                    <!-- Campo para ingresar el correo electrónico -->
                    <div class="campo-con-icono">
                        <input type="email" name="correo" placeholder="Correo" required>
                    </div>
                    <!-- Campo para ingresar la contraseña -->
                    <div class="campo-con-icono">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    <!-- Campo para confirmar la contraseña -->
                    <div class="campo-con-icono">
                        <input type="password" name="confirmaPassword" placeholder="Confirmar contraseña" required>
                    </div>
                    <!-- Selector de horario preferido -->
                    <div class="campo-con-icono">
                        <select name="horario" required>
                            <option value="" disabled selected>Seleccione horario</option>
                            <option value="mañana">Mañana</option>
                            <option value="tarde">Tarde</option>
                            <option value="noche">Noche</option>
                        </select>
                    </div>
                    <!-- Selector del tipo de usuario que se está registrando -->
                    <div class="campo-con-icono">
                        <select name="tipo_usuario" id="tipo_usuario" required>
                            <option value="">Seleccione tipo de usuario</option>
                            <option value="administrador">Administrador</option>
                            <option value="adscripta">Adscripta</option>
                            <option value="alumno">Alumno</option>
                            <option value="profesor">Profesor</option>
                            <option value="secretaria">Secretaria</option>
                            <option value="direccion">Dirección</option>
                            <option value="funcionario">Funcionario</option>
                        </select>
                    </div>
                    <!-- Botón para enviar el formulario de registro -->
                    <button type="submit" class="btn-primario">Registrarse</button>
                </form>
                <!-- Script que gestiona la validación de campos del formulario -->
                <script src="/Login/JavaScript/registro_campos.js"></script>
            </section>
            <!-- Panel lateral que contiene el carrusel visual -->
            <section class="panel-carrusel">
                <div class="carrusel">
                    <!-- Imagen institucional que se muestra en el carrusel -->
                    <img src="../../Images/fondo.png" alt="Imagen institucional" class="imagen-carrusel" />
                    <!-- Texto descriptivo del carrusel -->
                    <div class="texto-carrusel">
                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde perspiciati.
                    </div>
                    <!-- Botones para navegar entre imágenes del carrusel -->
                    <button class="flecha-carrusel izquierda"><</button>
                    <button class="flecha-carrusel derecha">></button>
                </div>
            </section>
        </main>
    </div>
    <!-- Script que gestiona el comportamiento del formulario de registro -->
    <script src="/Login/JavaScript/registro.js"></script>
    <!-- Script que muestra u oculta campos según el tipo de usuario seleccionado -->
    <script src="/Login/JavaScript/mostrarCampos.js"></script>
</body>
</html>