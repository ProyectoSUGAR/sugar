<?php

// Indica al navegador que la respuesta será JSON
header('Content-Type: application/json; charset=utf-8');

// Importa la función conectar_pdo() desde conexion.php
require_once __DIR__ . '/conexion.php';

// Objeto PDO para consultas a la base de datos
// pdo sirve para hacer consultas a la base de datos de forma segura y eficiente
$pdo = conectar_pdo();

// Solo estos días se permiten en la consulta
$dias_validos = ['lunes','martes','miercoles','jueves','viernes'];

// Mapeo de bloques horarios a números de bloque según su turno y franja horaria
$mapa_horarios_bloques = [
    '07:00 - 07:45' => 1, '07:50 - 08:35' => 2, '08:40 - 09:25' => 3, '09:30 - 10:15' => 4,
    '10:20 - 11:05' => 5, '11:10 - 11:55' => 6, '12:00 - 12:45' => 7, '12:50 - 13:35' => 8,
    '13:40 - 14:25' => 1, '14:30 - 15:15' => 2, '15:20 - 16:05' => 3, '16:10 - 16:55' => 4,
    '17:00 - 17:45' => 5, '17:50 - 18:35' => 6, '18:40 - 19:35' => 7, '18:10 - 18:55' => 1,
    '19:00 - 19:45' => 2, '19:50 - 20:35' => 3, '20:40 - 21:25' => 4, '21:30 - 22:15' => 5,
    '22:20 - 23:05' => 6, '23:10 - 23:11' => 7
];

// Inicializa la estructura del resultado para todos los pisos y turnos
$resultado = inicializar_resultado();

// Normaliza nombres de espacios y turnos
function normalizar($nombre) {
    // Convierte a minúsculas y elimina espacios extra
    //strtolower convierte una cadena a minúsculas
    //trim elimina espacios en blanco al inicio y al final de una cadena
    //preg_replace realiza una búsqueda y reemplazo usando expresiones regulares
    return strtolower(trim(preg_replace('/\s+/', ' ', $nombre)));
}

// Inicializa la estructura del resultado para todos los pisos y turnos
function inicializar_resultado() {
    return [
        "0" => ["manana" => [], "tarde" => [], "noche" => []],
        "1" => ["manana" => [], "tarde" => [], "noche" => []],
        "2" => ["manana" => [], "tarde" => [], "noche" => []]
    ];
}

// Valida el día recibido por GET y lo normaliza
function obtener_dia($dias_validos) {
    // $_GET['dia'] contiene el día solicitado por el frontend
    $dia = isset($_GET['dia']) ? strtolower($_GET['dia']) : '';
    // Verifica si el día es válido
    // in_array verifica si un valor existe en un array
    if (!in_array($dia, $dias_validos)) {
        // Si el día no es válido, responde con error y termina el script
        // http_response_code establece el código de respuesta HTTP
        http_response_code(400);
        // json_encode convierte un array o objeto a formato JSON
        echo json_encode(["error" => "Día no válido. Use: lunes, martes, miercoles, jueves o viernes."]);
        exit;
    }
    return $dia;
}

// Ejecuta la consulta SQL y devuelve los resultados para el día solicitado
function obtener_horarios($pdo, $dia) {
    // Consulta SQL con joins para traer nombres de espacio, materia y profesor
    $sql = "SELECT a.turno, a.horario, a.dia_semana, a.id_asignatura, a.id_profesor, e.nombre AS espacio, e.ubicacion, asig.nombre AS materia, u.nombre AS nombre_profesor, u.apellido AS apellido_profesor
        FROM asocia a
        JOIN espacio e ON a.id_espacio = e.id_espacio
        JOIN asignatura asig ON a.id_asignatura = asig.id_asignatura
        JOIN usuario u ON a.id_profesor = u.id_usuario
        WHERE a.dia_semana = :dia";
    // Prepara la consulta
    //stmt sirve para preparar una consulta SQL para su ejecución
    //prepare prepara una consulta SQL para su ejecución
    $stmt = $pdo->prepare($sql);
    // Ejecuta la consulta con el parámetro del día
    //execute ejecuta una sentencia preparada
    $stmt->execute(['dia' => $dia]);
    // Devuelve todos los resultados como arreglo asociativo
    //fetchAll obtiene todas las filas de un conjunto de resultados
    //PDO::FETCH_ASSOC hace que las filas se devuelvan como un array asociativo
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Procesa los resultados y los normaliza para el grid
function procesar_resultados($rows, $mapa_horarios_bloques, &$resultado) {
    foreach ($rows as $row) {
        // Determina el piso según la ubicación del espacio
        $piso = null;
        if ($row['ubicacion'] === 'planta baja') $piso = "0";
        elseif ($row['ubicacion'] === 'piso 1') $piso = "1";
        elseif ($row['ubicacion'] === 'piso 2') $piso = "2";
        // Si no se reconoce el piso, omite el registro
        if ($piso === null) continue;

        // Normaliza el turno y el espacio
        $turno = normalizar($row['turno']);
        $espacio = normalizar($row['espacio']);

        // Determina el número de bloque según el horario
        $bloque = isset($mapa_horarios_bloques[$row['horario']]) ? $mapa_horarios_bloques[$row['horario']] : '_sin_bloque';

        // Inicializa el arreglo si no existe
        if (!isset($resultado[$piso][$turno][$espacio])) $resultado[$piso][$turno][$espacio] = [];
        if (!isset($resultado[$piso][$turno][$espacio][$bloque])) $resultado[$piso][$turno][$espacio][$bloque] = [];

        // Agrega la materia y profesor al bloque correspondiente
        $resultado[$piso][$turno][$espacio][$bloque][] = [
            'materia' => $row['materia'],
            'profesor' => $row['nombre_profesor'] . ' ' . $row['apellido_profesor']
        ];
    }
}

// función que asegura que cada turno esté presente como objeto aunque esté vacío 
function asegurar_turnos(&$resultado) {
    foreach (["0","1","2"] as $piso) {
        foreach (["manana","tarde","noche"] as $turno) {
            // Si el turno no existe, lo inicializa como objeto vacío
            //new stdClass crea un nuevo objeto vacío
            if (!isset($resultado[$piso][$turno])) $resultado[$piso][$turno] = new stdClass();
        }
    }
}

// Devuelve el resultado en formato JSON sin escapar caracteres Unicode
function devolver_json($resultado) {
    // json_encode convierte un array o objeto a formato JSON
    // JSON_UNESCAPED_UNICODE evita que los caracteres Unicode se escapen
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
}

// Día recibido por GET, validado
$dia = obtener_dia($dias_validos);

// Resultados de la consulta SQL
$rows = obtener_horarios($pdo, $dia);

// Procesa y normaliza los resultados para el grid
procesar_resultados($rows, $mapa_horarios_bloques, $resultado);

// Asegura que cada turno esté presente como objeto aunque esté vacío
asegurar_turnos($resultado);

// Devuelve el resultado en formato JSON
devolver_json($resultado);

/*
<?php // Consulta de entradas (prueba deshabilitada, solo para desarrollo)
$resultado = mysqli_query($con, "SELECT * FROM entrada"); // Obtener todas las entradas
while($entradas = mysqli_fetch_object($resultado)) { // Recorrer cada entrada
    ?>
    <b>(<?php echo($entradas->fecha); ?> ) </b> <!-- Fecha de la entrada -->
    <b> Anónimo dijo:</b> <br> <!-- Etiqueta de autor -->
    <b> <?php echo ($entradas->titulo); ?> </b> <br> <!-- Título de la entrada -->
    <?php echo ($entradas->mensaje); ?> <!-- Mensaje de la entrada -->
    <hr> <!-- Separador -->
<?php }
?>
*/
/*
<?php // Prueba de consulta sobre la tabla 'asocia' (deshabilitada)
$resultado = mysqli_query($con, "SELECT * FROM asocia"); // Obtener todas las asociaciones de horarios
while($asocia = mysqli_fetch_object($resultado)) { // Recorrer cada asociación
    ?>
    <b>(<?php echo($asocia->horario); ?>)</b> <!-- Horario asignado -->
    <b> Turno:</b> <?php echo($asocia->turno); ?> <br> <!-- Turno de la clase -->
    <b> Día:</b> <?php echo($asocia->dia_semana); ?> <br> <!-- Día de la semana -->
    <b> ID Asignatura:</b> <?php echo($asocia->id_asignatura); ?> <br> <!-- ID de la asignatura -->
    <b> ID Espacio:</b> <?php echo($asocia->id_espacio); ?> <br> <!-- ID del espacio -->
    <b> ID Profesor:</b> <?php echo($asocia->id_profesor); ?> <br> <!-- ID del profesor -->
    <hr>
<?php }
?>
*/
// Este bloque fue usado para pruebas rápidas de visualización y quedó deshabilitado por falta de funcionalidad real.