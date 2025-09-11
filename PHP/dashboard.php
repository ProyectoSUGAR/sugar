<!-- Cuerpo principal de la página con clase específica para el perfil de estudiante -->
<body class="body-estudiante">
    <main>
        <!-- Inicio del bloque que contiene los planos y horarios de la institución -->
        <section class="bloque-planos-horarios">
            <!-- Selector de piso: permite al usuario elegir entre planta baja, piso 1 o piso 2 -->
            <div class="selector-piso">
                <!-- Botón para mostrar el plano de la planta baja -->
                <button type="button" class="btn-piso activo" data-piso="0">Planta baja</button>
                <!-- Botón para mostrar el plano del primer piso -->
                <button type="button" class="btn-piso" data-piso="1">Piso 1</button>
                <!-- Botón para mostrar el plano del segundo piso -->
                <button type="button" class="btn-piso" data-piso="2">Piso 2</button>
            </div>
            <!-- Contenedor que muestra la imagen del plano correspondiente al piso seleccionado -->
            <div class="contenedor-plano">
                <img id="imagen-plano" src="/Images/PlantaBaja.jpeg" alt="Plano Planta baja" />
            </div>
            <!-- Contenedor dinámico donde se insertarán las tablas de horarios según el piso -->
            <div id="contenedor-tablas-horarios"></div>
        </section>
        <!-- Fin del bloque de planos y horarios -->
    </main>
    <!-- Pie de página institucional -->
    <footer class="pie-pagina">
        <!-- Texto o contenido adicional del pie de página (actualmente vacío) -->
    </footer>
    <!-- Franja decorativa azul ubicada al final de la página -->
    <section class="franja-textura" aria-hidden="true"></section>
    <!-- Inclusión del script principal de la aplicación -->
    <script src="/JS/app.js"></script>
    <!-- Inclusión del script que gestiona la lógica de planos y horarios -->
    <script src="/JS/planos_horarios.js"></script>
</body>
</html>