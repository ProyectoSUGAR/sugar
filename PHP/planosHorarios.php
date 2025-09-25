<?php

// Este archivo devuelve los horarios normalizados en formato JSON para el grid de planos y horarios.

// Encabezado para indicar que la respuesta es JSON y codificada en UTF-8
header('Content-Type: application/json; charset=utf-8');

// Incluir la configuración y función de conexión a la base de datos
include 'conexion.php';

// Conexión mysqli (no se usa aquí, pero mantiene compatibilidad global)
$mysqli = conectar_bd();

// Importar los parámetros globales de conexión para usarlos con PDO
global $DB_SERVIDOR, $DB_NOMBRE, $DB_USUARIO, $DB_PASS;

// Crear el DSN para PDO usando los parámetros globales
$dsn = "mysql:host=$DB_SERVIDOR;dbname=$DB_NOMBRE;charset=utf8";

// Intentar conectar a la base de datos usando PDO
try {
    $pdo = new PDO($dsn, $DB_USUARIO, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla la conexión, devolver error HTTP 500 y mensaje JSON
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos."]);
    exit;
}

// Definir los días válidos para la consulta
$dias_validos = ['lunes','martes','miercoles','jueves','viernes'];

// Obtener el día solicitado por GET y normalizarlo a minúsculas
$dia = isset($_GET['dia']) ? strtolower($_GET['dia']) : '';

// Validar que el día sea uno de los permitidos
if (!in_array($dia, $dias_validos)) {
    http_response_code(400);
    echo json_encode(["error" => "Día no válido. Use: lunes, martes, miercoles, jueves o viernes."]);
    exit;
}

// Mapeo de bloques horarios a números de bloque (según turno y franja horaria)
$mapa_horarios_bloques = [
    '07:00 - 07:45' => 1, '07:50 - 08:35' => 2, '08:40 - 09:25' => 3, '09:30 - 10:15' => 4,
    '10:20 - 11:05' => 5, '11:10 - 11:55' => 6, '12:00 - 12:45' => 7, '12:50 - 13:35' => 8,
    '13:40 - 14:25' => 1, '14:30 - 15:15' => 2, '15:20 - 16:05' => 3, '16:10 - 16:55' => 4,
    '17:00 - 17:45' => 5, '17:50 - 18:35' => 6, '18:40 - 19:35' => 7, '18:10 - 18:55' => 1,
    '19:00 - 19:45' => 2, '19:50 - 20:35' => 3, '20:40 - 21:25' => 4, '21:30 - 22:15' => 5,
    '22:20 - 23:05' => 6, '23:10 - 23:11' => 7
];

// Estructura inicial del resultado: un arreglo para cada piso y turno
$resultado = [
    "0" => ["manana" => [], "tarde" => [], "noche" => []],
    "1" => ["manana" => [], "tarde" => [], "noche" => []],
    "2" => ["manana" => [], "tarde" => [], "noche" => []]
];

// Función para normalizar nombres de espacios y turnos (minúsculas, sin espacios extra)
function normalizar($nombre) {
    return strtolower(trim(preg_replace('/\s+/', ' ', $nombre)));
}

// Consulta SQL para obtener los horarios del día solicitado, con joins para traer nombres de espacio, materia y profesor
$sql = "SELECT a.turno, a.horario, a.dia_semana, a.id_asignatura, a.id_profesor, e.nombre AS espacio, e.ubicacion, asig.nombre AS materia, u.nombre AS nombre_profesor, u.apellido AS apellido_profesor
FROM asocia a
JOIN espacio e ON a.id_espacio = e.id_espacio
JOIN asignatura asig ON a.id_asignatura = asig.id_asignatura
JOIN usuario u ON a.id_profesor = u.id_usuario
WHERE a.dia_semana = :dia";

// Preparar y ejecutar la consulta usando PDO
$stmt = $pdo->prepare($sql);
$stmt->execute(['dia' => $dia]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recorrer los resultados y normalizar para el grid
foreach ($rows as $row) {
    // Determinar el piso según la ubicación del espacio
    $piso = null;
    if ($row['ubicacion'] === 'planta baja') $piso = "0";
    elseif ($row['ubicacion'] === 'piso 1') $piso = "1";
    elseif ($row['ubicacion'] === 'piso 2') $piso = "2";
    // Si no se reconoce el piso, omitir el registro
    if ($piso === null) continue;

    // Normalizar turno y espacio
    $turno = normalizar($row['turno']);
    $espacio = normalizar($row['espacio']);

    // Determinar el número de bloque según el horario
    $bloque = isset($mapa_horarios_bloques[$row['horario']]) ? $mapa_horarios_bloques[$row['horario']] : '_sin_bloque';

    // Inicializar el arreglo si no existe
    if (!isset($resultado[$piso][$turno][$espacio])) $resultado[$piso][$turno][$espacio] = [];
    if (!isset($resultado[$piso][$turno][$espacio][$bloque])) $resultado[$piso][$turno][$espacio][$bloque] = [];

    // Agregar la materia y profesor al bloque correspondiente
    $resultado[$piso][$turno][$espacio][$bloque][] = [
        'materia' => $row['materia'],
        'profesor' => $row['nombre_profesor'] . ' ' . $row['apellido_profesor']
    ];
}

// Asegurar que cada turno esté presente como objeto aunque esté vacío (para compatibilidad con el frontend)
foreach (["0","1","2"] as $piso) {
    foreach (["manana","tarde","noche"] as $turno) {
        if (!isset($resultado[$piso][$turno])) $resultado[$piso][$turno] = new stdClass();
    }
}

// Devolver el resultado en formato JSON sin escapar caracteres Unicode
echo json_encode($resultado, JSON_UNESCAPED_UNICODE);