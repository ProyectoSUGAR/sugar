<?php
require_once(dirname(dirname(__DIR__)) . "/PHP/conexion.php");

// Debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

function obtenerEspaciosDisponibles($fecha = null, $horario = null) {
    try {
        $db = conectar_bd();
        if (!$db) {
            throw new Exception("Error de conexión a la base de datos");
        }

        // Base query
        $query = "SELECT e.id_espacio, e.nombre, e.capacidad, e.ubicacion, e.tipo_espacio FROM espacio e";
        $params = [];
        $types = '';
        $where_clauses = [];

        if ($fecha && $horario) {
            // Día de la semana para 'asocia'
            $dias = [1=>'lunes',2=>'martes',3=>'miercoles',4=>'jueves',5=>'viernes',6=>'sabado',7=>'domingo'];
            $num = (int) date('N', strtotime($fecha));
            $dia_semana = $dias[$num] ?? '';

            // Subquery for reserved spaces: only exclude spaces that are already approved
            $where_clauses[] = "e.id_espacio NOT IN (
                SELECT r.id_espacio FROM reserva r
                WHERE r.fecha_reserva = ? AND r.fecha_inicio = ? AND r.estado = 'aprobada'
            )";
            $params[] = $fecha;
            $params[] = $horario;
            $types .= 'ss';

            // Subquery for scheduled classes
            $where_clauses[] = "e.id_espacio NOT IN (
                SELECT a.id_espacio FROM asocia a
                WHERE a.dia_semana = ? AND a.horario = ?
            )";
            $params[] = $dia_semana;
            $params[] = $horario;
            $types .= 'ss';
        }

        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        }

        $query .= " ORDER BY e.tipo_espacio, e.nombre";

        $stmt = $db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Error en la consulta: " . $stmt->error);
        }

        $espacios = [];
        while ($row = $result->fetch_assoc()) {
            $espacios[] = $row;
        }
        
        return $espacios;
    } catch (Exception $e) {
        error_log("Error en obtenerEspaciosDisponibles: " . $e->getMessage());
        return false;
    }
}

/**
 * Devuelve el listado oficial de bloques/horarios usados por la institución.
 */
/* La función obtenerHorarios() se define más abajo y agrupa los turnos (mañana/tarde/noche). */

/**
 * Obtiene las clases programadas (tabla `asocia`) para la fecha dada.
 * Devuelve array de filas con campos: horario, id_espacio, nombre_espacio, nombre_asignatura, nombre_profesor, nombre_grupo, turno, dia_semana
 */
function obtenerClasesDia($fecha) {
    $db = conectar_bd();
    if (!$db) return [];

    // Mapear fecha a día de la semana en español (asocia.dia_semana usa 'lunes'..'viernes')
    $dias = [1=>'lunes',2=>'martes',3=>'miercoles',4=>'jueves',5=>'viernes',6=>'sabado',7=>'domingo'];
    $num = (int) date('N', strtotime($fecha));
    $dia_semana = $dias[$num] ?? '';

    $sql = "SELECT a.horario, a.turno, a.dia_semana, a.id_espacio,
                   e.nombre AS nombre_espacio, asig.nombre AS nombre_asignatura,
                   CONCAT(u.nombre, ' ', u.apellido) AS nombre_profesor, g.nombre AS nombre_grupo
            FROM asocia a
            JOIN espacio e ON a.id_espacio = e.id_espacio
            JOIN asignatura asig ON a.id_asignatura = asig.id_asignatura
            LEFT JOIN usuario u ON a.id_profesor = u.id_usuario
            LEFT JOIN grupo g ON a.id_grupo = g.id_grupo
            WHERE a.dia_semana = ?";

    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('s', $dia_semana);
    $stmt->execute();
    $res = $stmt->get_result();
    $clases = [];
    while ($row = $res->fetch_assoc()) {
        $clases[] = $row;
    }
    return $clases;
}

function verificarDisponibilidad($id_espacio, $fecha, $hora_inicio, $hora_fin) {
    $db = conectar_bd();
    
    // Primero verificar si el espacio está asignado
    $query_asignacion = "SELECT 1 FROM asocia WHERE id_espacio = ?";
    $stmt = $db->prepare($query_asignacion);
    $stmt->bind_param('i', $id_espacio);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return false; // El espacio está asignado
    }
    
    // Verificar si hay reservas activas para ese espacio y fecha
    $query = "SELECT * FROM reserva 
              WHERE id_espacio = ? 
              AND fecha_reserva = ? 
              AND ((fecha_inicio BETWEEN ? AND ?) 
              OR (fecha_fin BETWEEN ? AND ?))
              AND estado = 'aprobada'";
              
    $stmt = $db->prepare($query);
    $stmt->bind_param('isssss', $id_espacio, $fecha, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows === 0;
}

/**
 * Devuelve los horarios agrupados por turno (mañana/tarde/noche)
 */
function obtenerHorariosPorTurno() {
    return [
        'manana' => [
            "07:00 - 07:45","07:50 - 08:35","08:40 - 09:25","09:30 - 10:15",
            "10:20 - 11:05","11:10 - 11:55","12:00 - 12:45","12:50 - 13:35"
        ],
        'tarde' => [
            "13:40 - 14:25","14:30 - 15:15","15:20 - 16:05","16:10 - 16:55",
            "17:00 - 17:45","17:50 - 18:35","18:40 - 19:25"
        ],
        'noche' => [
            "18:10 - 18:55","19:00 - 19:45","19:50 - 20:35","20:40 - 21:25",
            "21:30 - 22:15","22:20 - 23:05","23:10 - 23:55"
        ]
    ];
}

// Mantener compatibilidad: lista simple de horarios planos (aplana los turnos)
function obtenerHorarios() {
    $turnos = obtenerHorariosPorTurno();
    $flat = [];
    foreach (['manana','tarde','noche'] as $t) {
        if (!empty($turnos[$t]) && is_array($turnos[$t])) {
            foreach ($turnos[$t] as $h) $flat[] = $h;
        }
    }
    return $flat;
}

function crearReserva($id_usuario, $id_espacio, $fecha, $hora_inicio, $hora_fin) {
    $db = conectar_bd();
    
    if (!verificarDisponibilidad($id_espacio, $fecha, $hora_inicio, $hora_fin)) {
        return ['success' => false, 'message' => 'El espacio no está disponible para el horario seleccionado'];
    }
    
    $query = "INSERT INTO reserva (id_usuario, id_espacio, fecha_reserva, fecha_inicio, fecha_fin, estado) 
              VALUES (?, ?, ?, ?, ?, 'pendiente')";
              
    $stmt = $db->prepare($query);
    $stmt->bind_param('iisss', $id_usuario, $id_espacio, $fecha, $hora_inicio, $hora_fin);
    
    if ($stmt->execute()) {
        // Registrar la actividad
        $detalle = "Solicitud de reserva id_usuario=$id_usuario espacio=$id_espacio fecha=$fecha horario=$hora_inicio - $hora_fin";
        $query_actividad = "INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'reserva_solicitada', ?)";
        $stmt_actividad = $db->prepare($query_actividad);
        $stmt_actividad->bind_param('is', $id_usuario, $detalle);
        $stmt_actividad->execute();
        
        return ['success' => true, 'message' => 'Reserva creada exitosamente'];
    }
    
    return ['success' => false, 'message' => 'Error al crear la reserva'];
}

function obtenerReservasDia($fecha) {
    $db = conectar_bd();
    $query = "SELECT r.*, e.nombre as nombre_espacio, u.nombre as nombre_usuario, u.apellido as apellido_usuario 
              FROM reserva r 
              JOIN espacio e ON r.id_espacio = e.id_espacio 
              JOIN usuario u ON r.id_usuario = u.id_usuario 
              WHERE r.fecha_reserva = ? 
              AND r.estado != 'cancelada'
              ORDER BY r.fecha_inicio";
              
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservas = [];
    
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
    
    return $reservas;
}
?>