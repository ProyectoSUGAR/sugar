<?php

// Indica al navegador que la respuesta será JSON
header('Content-Type: application/json');

// Importa la función conectar_pdo() desde conexion.php
require_once __DIR__ . '../../conexion.php';

// Objeto PDO para consultas a la base de datos
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
    return strtolower(trim(preg_replace('/\s+/', ' ', $nombre)));
}

function inicializar_resultado() {
    return [
        "0" => ["manana" => [], "tarde" => [], "noche" => []],
        "1" => ["manana" => [], "tarde" => [], "noche" => []],
        "2" => ["manana" => [], "tarde" => [], "noche" => []]
    ];
}

function obtener_dia($dias_validos) {
    $dia = isset($_GET['dia']) ? strtolower($_GET['dia']) : '';
    if (!in_array($dia, $dias_validos)) {
        http_response_code(400);
        echo json_encode(["error" => "Día no válido. Use: lunes, martes, miercoles, jueves o viernes."]);
        exit;
    }
    return $dia;
}

function obtener_horarios($pdo, $dia) {
    $sql = "SELECT a.turno, a.horario, a.dia_semana, a.id_asignatura, a.id_profesor, a.id_grupo,
        e.nombre AS espacio, e.ubicacion, asig.nombre AS materia,
        u.nombre AS nombre_profesor, u.apellido AS apellido_profesor,
        g.nombre AS grupo_nombre, g.anio AS grupo_anio
        FROM asocia a
        JOIN espacio e ON a.id_espacio = e.id_espacio
        JOIN asignatura asig ON a.id_asignatura = asig.id_asignatura
        LEFT JOIN usuario u ON a.id_profesor = u.id_usuario
        LEFT JOIN grupo g ON a.id_grupo = g.id_grupo
        WHERE a.dia_semana = :dia";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['dia' => $dia]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function procesar_resultados($rows, $mapa_horarios_bloques, &$resultado) {
    foreach ($rows as $row) {
        $piso = null;
        if ($row['ubicacion'] === 'planta baja') $piso = "0";
        elseif ($row['ubicacion'] === 'piso 1') $piso = "1";
        elseif ($row['ubicacion'] === 'piso 2') $piso = "2";
        if ($piso === null) continue;

        $turno = normalizar($row['turno']);
        $espacio = normalizar($row['espacio']);

        $bloque = isset($mapa_horarios_bloques[$row['horario']]) ? $mapa_horarios_bloques[$row['horario']] : '_sin_bloque';

        if (!isset($resultado[$piso][$turno][$espacio])) $resultado[$piso][$turno][$espacio] = [];
        if (!isset($resultado[$piso][$turno][$espacio][$bloque])) $resultado[$piso][$turno][$espacio][$bloque] = [];

        $resultado[$piso][$turno][$espacio][$bloque][] = [
            'materia' => $row['materia'],
            'profesor' => $row['nombre_profesor'] . ' ' . $row['apellido_profesor']
        ];
    }
}

function asegurar_turnos(&$resultado) {
    foreach (["0","1","2"] as $piso) {
        foreach (["manana","tarde","noche"] as $turno) {
            if (!isset($resultado[$piso][$turno])) $resultado[$piso][$turno] = new stdClass();
        }
    }
}

function devolver_json($resultado) {
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
}

$dia = obtener_dia($dias_validos);
// DEBUG: Si hay error en la consulta, mostrarlo y terminar
try {
    $rows = obtener_horarios($pdo, $dia);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
    exit;
}
procesar_resultados($rows, $mapa_horarios_bloques, $resultado);
asegurar_turnos($resultado);
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
