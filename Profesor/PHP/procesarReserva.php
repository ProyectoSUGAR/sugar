<?php
// Endpoint para gestionar reservas desde el módulo Profesor
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once(__DIR__ . '/../../PHP/conexion.php');
require_once(__DIR__ . '/funcReservas.php');

$db = function_exists('conectar_bd') ? conectar_bd() : null;
if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// Obtener lista de espacios
if ($accion === 'obtenerEspacios') {
    $sql = "SELECT id_espacio, nombre, capacidad, ubicacion, tipo_espacio FROM espacio ORDER BY tipo_espacio, nombre";
    $res = $db->query($sql);
    $espacios = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $espacios[] = $row;
        }
    }
    echo json_encode(['success' => true, 'data' => $espacios]);
    exit;
}

// Obtener reservas y clases de un día
if ($accion === 'obtenerReservasDia') {
    $fecha = $_POST['fecha'] ?? $_GET['fecha'] ?? '';
    if (!$fecha) {
        echo json_encode(['success' => false, 'message' => 'Fecha requerida']);
        exit;
    }

    // Reservas del día
    $stmt = $db->prepare("SELECT r.*, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario, r.id_espacio FROM reserva r JOIN usuario u ON r.id_usuario = u.id_usuario WHERE r.fecha_reserva = ? AND r.estado != 'cancelada'");
    $stmt->bind_param('s', $fecha);
    $stmt->execute();
    $res = $stmt->get_result();
    $reservas = [];
    while ($row = $res->fetch_assoc()) {
        $reservas[] = $row;
    }

    // Clases (asocia) usando helper
    $clases = [];
    if (function_exists('obtenerClasesDia')) {
        $clases = obtenerClasesDia($fecha);
    }

    echo json_encode(['success' => true, 'data' => ['reservas' => $reservas, 'clases' => $clases]]);
    exit;
}

// Crear una nueva solicitud de reserva (profesor)
if ($accion === 'crearReserva') {
    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
    }

    $id_usuario = (int) $_SESSION['id_usuario'];
    // Aceptar distintos nombres de parámetros (compatibilidad)
    $id_espacio = (int) (isset($_POST['id_espacio']) ? $_POST['id_espacio'] : ($_POST['espacio'] ?? 0));
    $fecha = trim($_POST['fecha'] ?? '');
    // horario puede venir en 'horario' o en 'hora_inicio'
    $horario = trim($_POST['horario'] ?? ($_POST['hora_inicio'] ?? ''));

    if (!$id_espacio || !$fecha || !$horario) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
        exit;
    }

    // Evitar que exista una reserva aprobada para ese espacio/horario (aprobada bloquea)
    $stmtCheckApproved = $db->prepare("SELECT COUNT(*) AS c FROM reserva WHERE id_espacio = ? AND fecha_reserva = ? AND fecha_inicio = ? AND estado = 'aprobada'");
    $stmtCheckApproved->bind_param('iss', $id_espacio, $fecha, $horario);
    $stmtCheckApproved->execute();
    $rApp = $stmtCheckApproved->get_result()->fetch_assoc();
    if ($rApp && (int)$rApp['c'] > 0) {
        echo json_encode(['success' => false, 'message' => 'El espacio ya está reservado (aprobado) para ese horario']);
        exit;
    }

    // Evitar que el mismo usuario cree duplicados pendientes para el mismo espacio/horario
    $stmtCheckPendingUser = $db->prepare("SELECT COUNT(*) AS c FROM reserva WHERE id_espacio = ? AND fecha_reserva = ? AND fecha_inicio = ? AND estado = 'pendiente' AND id_usuario = ?");
    $stmtCheckPendingUser->bind_param('issi', $id_espacio, $fecha, $horario, $id_usuario);
    $stmtCheckPendingUser->execute();
    $rPend = $stmtCheckPendingUser->get_result()->fetch_assoc();
    if ($rPend && (int)$rPend['c'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya tienes una solicitud pendiente para ese espacio y horario']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO reserva (id_usuario, id_espacio, fecha_reserva, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')");
    // Mantener fecha_fin vacío (no tenemos hora de fin separada del bloque)
    $fecha_fin = '';
    $stmt->bind_param('iisss', $id_usuario, $id_espacio, $fecha, $horario, $fecha_fin);
    if ($stmt->execute()) {
        $insertId = $db->insert_id;
        // Registrar actividad
        $detalle = "Solicitud de reserva id_usuario={$id_usuario} espacio={$id_espacio} fecha={$fecha} horario={$horario}";
        $stmtAct = $db->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'reserva_solicitada', ?)");
        $stmtAct->bind_param('is', $id_usuario, $detalle);
        $stmtAct->execute();

        echo json_encode(['success' => true, 'message' => 'Solicitud registrada', 'id_reserva' => $insertId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar la solicitud: ' . $db->error]);
    }
    exit;
}

// Si llega aquí, acción desconocida
echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
exit;
