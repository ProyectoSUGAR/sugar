<!-- Cuerpo principal de la página con clase específica para el perfil de estudiante -->
<body class="body-estudiante">
    <main>
    
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Planos y Horarios</title>
    <link rel="stylesheet" href="/Css/style.css" />
</head>
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
            <!-- Selector de día: permite al usuario elegir el día de la semana para ver el horario correspondiente -->
            <select id="selector-dia">
                <option value="lunes">Lunes</option>
                <option value="martes">Martes</option>
                <option value="miercoles">Miércoles</option>
                <option value="jueves">Jueves</option>
                <option value="viernes">Viernes</option>
            </select>
            <!-- Contenedor dinámico donde se insertarán las tablas de horarios según el piso y día seleccionados -->
            <div id="contenedor-tablas-horarios"></div>
        </section>
        <!-- Fin del bloque de planos y horarios -->
    </main>
    <!-- Pie de página institucional -->
    <!-- Franja decorativa azul ubicada al final de la página -->
    <section class="franja-textura" aria-hidden="true"></section>
    <!-- Inclusión del script principal de la aplicación -->
    <script src="/JS/app.js"></script>
    <!-- Inclusión del script que gestiona la lógica de planos y horarios -->
    <script src="/JS/planos_horarios.js"></script>
</body>
</html>
