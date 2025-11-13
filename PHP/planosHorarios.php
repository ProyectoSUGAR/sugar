<?php
// Función para inicializar el resultado
function inicializar_resultado() {
    return [
        "0" => ["manana" => [], "tarde" => [], "noche" => []],
        "1" => ["manana" => [], "tarde" => [], "noche" => []],
        "2" => ["manana" => [], "tarde" => [], "noche" => []]
    ];
}

// Función para normalizar nombres
function normalizar($nombre) {
    return strtolower(trim(preg_replace('/\s+/', ' ', $nombre)));
}

// Función para validar y obtener el día
function obtener_dia($dias_validos) {
    $dia = isset($_GET['dia']) ? strtolower($_GET['dia']) : '';
    if (!in_array($dia, $dias_validos)) {
        http_response_code(400);
        echo json_encode(["error" => "Día no válido. Use: lunes, martes, miercoles, jueves o viernes."]);
        exit;
    }
    return $dia;
}

// Función para obtener horarios desde la base de datos
function obtener_horarios($con, $dia) {
    $sql = "SELECT a.turno, a.horario, a.dia_semana, a.id_asignatura, a.id_profesor, a.id_grupo,
        e.nombre AS espacio, e.ubicacion, asig.nombre AS materia,
        u.nombre AS nombre_profesor, u.apellido AS apellido_profesor,
        g.nombre AS grupo_nombre, g.anio AS grupo_anio
        FROM asocia a
        JOIN espacio e ON a.id_espacio = e.id_espacio
        JOIN asignatura asig ON a.id_asignatura = asig.id_asignatura
        LEFT JOIN usuario u ON a.id_profesor = u.id_usuario
        LEFT JOIN grupo g ON a.id_grupo = g.id_grupo
        WHERE a.dia_semana = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $dia);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

// Función para procesar los resultados de la consulta
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
            'profesor' => $row['nombre_profesor'] . ' ' . $row['apellido_profesor'],
            'grupo' => $row['grupo_nombre'] ? $row['grupo_nombre'] . ' (' . $row['grupo_anio'] . ')' : ''
        ];
    }
}

// Función para asegurar que todos los turnos estén presentes
function asegurar_turnos(&$resultado) {
    foreach (["0","1","2"] as $piso) {
        foreach (["manana","tarde","noche"] as $turno) {
            if (!isset($resultado[$piso][$turno])) $resultado[$piso][$turno] = new stdClass();
        }
    }
}

// Función para devolver JSON
function devolver_json($resultado) {
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
}

// Función principal para obtener planos de horarios
function obtener_planos_horarios() {
    header('Content-Type: application/json');
    require_once 'conexion.php';
    $con = conectar_bd();
    if (!$con) {
        http_response_code(500);
        echo json_encode(["error" => "Error de conexión a la base de datos"]);
        exit;
    }
    $dias_validos = ['lunes','martes','miercoles','jueves','viernes'];
    $mapa_horarios_bloques = [
        '07:00 - 07:45' => 1, '07:50 - 08:35' => 2, '08:40 - 09:25' => 3, '09:30 - 10:15' => 4,
        '10:20 - 11:05' => 5, '11:10 - 11:55' => 6, '12:00 - 12:45' => 7, '12:50 - 13:35' => 8,
        '13:40 - 14:25' => 1, '14:30 - 15:15' => 2, '15:20 - 16:05' => 3, '16:10 - 16:55' => 4,
        '17:00 - 17:45' => 5, '17:50 - 18:35' => 6, '18:40 - 19:25' => 7, '18:10 - 18:55' => 1,
        '19:00 - 19:45' => 2, '19:50 - 20:35' => 3, '20:40 - 21:25' => 4, '21:30 - 22:15' => 5,
        '22:20 - 23:05' => 6, '23:10 - 23:55' => 7
    ];
    $resultado = inicializar_resultado();

    $dia = obtener_dia($dias_validos);
    $rows = obtener_horarios($con, $dia);
    if (!$rows) {
        http_response_code(500);
        echo json_encode(["error" => "Error en la consulta: " . mysqli_error($con)]);
        exit;
    }
    procesar_resultados($rows, $mapa_horarios_bloques, $resultado);
    asegurar_turnos($resultado);
    devolver_json($resultado);
}

// Llamada principal si se ejecuta directamente
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    obtener_planos_horarios();
}
