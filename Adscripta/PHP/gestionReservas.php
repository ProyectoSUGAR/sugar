<?php
session_start();
require_once('../../PHP/conexion.php');

// Verificar que sea Adscripta
if ($_SESSION['tipo_usuario'] !== 'adscripta') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conn = conectar_bd();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'obtenerPendientes') {
    // Listar reservas pendientes
    // Comprobar si la columna `fecha_creacion` existe; si no, usar `fecha_reserva` como alternativa
    $hasFechaCreacion = false;
    $checkColQ = "SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'reserva' AND COLUMN_NAME = 'fecha_creacion'";
    $resCheck = mysqli_query($conn, $checkColQ);
    if ($resCheck) {
        $rowCheck = mysqli_fetch_assoc($resCheck);
        if ($rowCheck && (int)$rowCheck['c'] > 0) {
            $hasFechaCreacion = true;
        }
    }

    if ($hasFechaCreacion) {
        $dateSelect = 'r.fecha_creacion';
        $orderBy = 'r.fecha_creacion';
    } else {
        $dateSelect = 'r.fecha_reserva AS fecha_creacion';
        $orderBy = 'r.fecha_reserva';
    }

    $query = "SELECT r.id_reserva, r.id_usuario, r.id_espacio, r.fecha_reserva, r.fecha_inicio, r.fecha_fin, r.estado, " . $dateSelect . ", u.nombre, u.apellido, e.nombre as espacio_nombre
              FROM reserva r
              JOIN usuario u ON r.id_usuario = u.id_usuario
              JOIN espacio e ON r.id_espacio = e.id_espacio
              WHERE r.estado = 'pendiente'
              ORDER BY " . $orderBy . " ASC";
    
    $result = mysqli_query($conn, $query);
    $reservas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reservas[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($reservas);
    exit;
}

if ($accion === 'aprobar') {
    $id_reserva = (int)$_POST['id_reserva'];
    $notas = mysqli_real_escape_string($conn, $_POST['notas'] ?? '');
    // Obtener detalles de la solicitud para validar conflictos y notificar al profesor
    $queryGet = "SELECT id_espacio, id_usuario, fecha_reserva, fecha_inicio FROM reserva WHERE id_reserva = ? LIMIT 1";
    $stmtGet = mysqli_prepare($conn, $queryGet);
    mysqli_stmt_bind_param($stmtGet, 'i', $id_reserva);
    mysqli_stmt_execute($stmtGet);
    $resGet = mysqli_stmt_get_result($stmtGet);
    $row = mysqli_fetch_assoc($resGet);
    if (!$row) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Reserva no encontrada']);
        exit;
    }
    $id_espacio_req = (int)$row['id_espacio'];
    $id_usuario_req = (int)$row['id_usuario'];
    $fecha_reserva_req = $row['fecha_reserva'];
    $fecha_inicio_req = $row['fecha_inicio'];

    // Comprobar que no exista otra reserva aprobada en el mismo espacio/fecha/horario
    $queryConflict = "SELECT COUNT(*) AS c FROM reserva WHERE id_espacio = ? AND fecha_reserva = ? AND fecha_inicio = ? AND estado = 'aprobada'";
    $stmtConf = mysqli_prepare($conn, $queryConflict);
    mysqli_stmt_bind_param($stmtConf, 'iss', $id_espacio_req, $fecha_reserva_req, $fecha_inicio_req);
    mysqli_stmt_execute($stmtConf);
    $resConf = mysqli_stmt_get_result($stmtConf);
    $confRow = mysqli_fetch_assoc($resConf);
    if ($confRow && (int)$confRow['c'] > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ya existe una reserva aprobada para ese espacio y horario']);
        exit;
    }

    // Al aprobar, marcar como 'aprobada' y fijar fecha_fin al mismo bloque
    $query = "UPDATE reserva SET estado = 'aprobada', notas = ?, fecha_fin = fecha_inicio WHERE id_reserva = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $notas, $id_reserva);

    if (mysqli_stmt_execute($stmt)) {
        // Registrar actividad
        $id_adscripta = $_SESSION['id_usuario'];
        $query_act = "INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'reserva_aprobada', ?)";
        $stmt_act = mysqli_prepare($conn, $query_act);
        $detalle = "Reserva id_reserva=$id_reserva aprobada por adscripta";
        mysqli_stmt_bind_param($stmt_act, 'is', $id_adscripta, $detalle);
        mysqli_stmt_execute($stmt_act);

        // Crear notificación para el profesor solicitante
        $mensaje = "Su solicitud de reserva (id={$id_reserva}) para el espacio {$id_espacio_req} el día {$fecha_reserva_req} a las {$fecha_inicio_req} ha sido aprobada.";
        $tipo = 'informativa';
        $fechaNow = date('Y-m-d H:i:s');
        $destinatario_tipo = 'usuario';
        $sqlNot = "INSERT INTO notificacion (mensaje, tipo, fecha, id_usuario, destinatario_tipo) VALUES (?, ?, ?, ?, ?)";
        $stmtNot = mysqli_prepare($conn, $sqlNot);
        if ($stmtNot) {
            mysqli_stmt_bind_param($stmtNot, 'ssiss', $mensaje, $tipo, $fechaNow, $id_usuario_req, $destinatario_tipo);
            mysqli_stmt_execute($stmtNot);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Reserva aprobada']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al aprobar']);
    }
    exit;
}

if ($accion === 'rechazar') {
    $id_reserva = (int)$_POST['id_reserva'];
    $motivo = mysqli_real_escape_string($conn, $_POST['motivo'] ?? '');
    
    // La tabla `reserva` no tiene columna `motivo_rechazo` en este esquema.
    // Usar la columna `notas` para almacenar el motivo de rechazo.
    $query = "UPDATE reserva SET estado = 'rechazada', notas = ? WHERE id_reserva = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $motivo, $id_reserva);
    
    if (mysqli_stmt_execute($stmt)) {
        // Registrar actividad
        $id_adscripta = $_SESSION['id_usuario'];
        $query_act = "INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, 'reserva_rechazada', ?)";
        $stmt_act = mysqli_prepare($conn, $query_act);
        $detalle = "Reserva id_reserva=$id_reserva rechazada. Motivo: $motivo";
        mysqli_stmt_bind_param($stmt_act, 'is', $id_adscripta, $detalle);
        mysqli_stmt_execute($stmt_act);
        
        // Obtener datos de la reserva para notificar al profesor
        $qGet = "SELECT id_usuario, id_espacio, fecha_reserva, fecha_inicio FROM reserva WHERE id_reserva = ? LIMIT 1";
        $sGet = mysqli_prepare($conn, $qGet);
        if ($sGet) {
            mysqli_stmt_bind_param($sGet, 'i', $id_reserva);
            mysqli_stmt_execute($sGet);
            $rGet = mysqli_stmt_get_result($sGet);
            $rowGet = mysqli_fetch_assoc($rGet);
            if ($rowGet) {
                $id_usuario_req = (int)$rowGet['id_usuario'];
                $id_esp = $rowGet['id_espacio'];
                $fecha_res = $rowGet['fecha_reserva'];
                $hora = $rowGet['fecha_inicio'];
                $mensaje = "Su solicitud de reserva (id={$id_reserva}) para el espacio {$id_esp} el día {$fecha_res} a las {$hora} ha sido rechazada. Motivo: {$motivo}";
                $tipo = 'informativa';
                $fechaNow = date('Y-m-d H:i:s');
                $destinatario_tipo = 'usuario';
                $sqlNot = "INSERT INTO notificacion (mensaje, tipo, fecha, id_usuario, destinatario_tipo) VALUES (?, ?, ?, ?, ?)";
                $stNot = mysqli_prepare($conn, $sqlNot);
                if ($stNot) {
                    mysqli_stmt_bind_param($stNot, 'ssiss', $mensaje, $tipo, $fechaNow, $id_usuario_req, $destinatario_tipo);
                    mysqli_stmt_execute($stNot);
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Reserva rechazada']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al rechazar']);
    }
    exit;
}

// Auto-finalizar reservas expiradas
$query_finalizar = "UPDATE reserva SET estado = 'finalizada' WHERE estado = 'aprobada' AND fecha_fin < NOW()";
mysqli_query($conn, $query_finalizar);

header('Content-Type: application/json');
echo json_encode(['error' => 'Acción no reconocida']);
