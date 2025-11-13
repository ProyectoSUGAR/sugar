<?php
function validar_entrada_notificacion($mensaje, $tipo, $destinatario_tipo) {
    $errores = [];

    if (empty(trim($mensaje))) {
        $errores[] = "El mensaje no puede estar vacío.";
    }

    if (empty($tipo)) {
        $errores[] = "Debe seleccionar un tipo de notificación.";
    }

    if (empty($destinatario_tipo)) {
        $errores[] = "Debe seleccionar un tipo de destinatario.";
    }

    return $errores;
}

function crear_notificacion($mensaje, $tipo, $destinatario_tipo) {
    $errores = validar_entrada_notificacion($mensaje, $tipo, $destinatario_tipo);

    if (!empty($errores)) {
        return ['exito' => false, 'mensaje' => implode(' ', $errores)];
    }

    $conn = conectar_bd();
    $fecha = date('Y-m-d H:i:s');
    $sql = "INSERT INTO notificacion (mensaje, tipo, fecha, destinatario_tipo) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssss', $mensaje, $tipo, $fecha, $destinatario_tipo);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['exito' => true, 'mensaje' => "Notificación creada exitosamente."];
        } else {
            $error = mysqli_error($conn);
            mysqli_stmt_close($stmt);
            return ['exito' => false, 'mensaje' => "Error al crear la notificación: $error"];
        }
    } else {
        return ['exito' => false, 'mensaje' => "Error al preparar la consulta."];
    }
}

function obtener_notificaciones($destinatario_tipo = null, $limit = 50) {
    $conn = conectar_bd();
    $sql = "SELECT * FROM notificacion";
    $params = [];
    $types = '';

    if ($destinatario_tipo) {
        $sql .= " WHERE destinatario_tipo = ?";
        $params[] = $destinatario_tipo;
        $types .= 's';
    }

    $sql .= " ORDER BY fecha DESC LIMIT ?";
    $params[] = $limit;
    $types .= 'i';

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $notificaciones = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $notificaciones;
    }

    return [];
}

function eliminar_notificacion($id_notificacion) {
    $conn = conectar_bd();
    $sql = "DELETE FROM notificacion WHERE id_notificacion = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id_notificacion);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    return false;
}

function procesar_creacion_notificacion() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_notificacion'])) {
        $mensaje = trim($_POST['mensaje']);
        $tipo = $_POST['tipo'];
        $destinatario_tipo = $_POST['destinatario_tipo'];

        $resultado = crear_notificacion($mensaje, $tipo, $destinatario_tipo);
        echo "<p>" . $resultado['mensaje'] . "</p>";
    }
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    procesar_creacion_notificacion();
}
?>