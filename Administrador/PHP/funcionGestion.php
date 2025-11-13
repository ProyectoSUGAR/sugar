<?php
function enviar_respuesta_gestion($success, $message, $redirect = '') {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit;
}

function validar_sesion_admin() {
    session_start();
    if (!isset($_SESSION['id_usuario'])) {
        enviar_respuesta_gestion(false, "Sesión no válida");
    }
    return $_SESSION['id_usuario'];
}

function verificar_usuario_existe($id_usuario) {
    $conn = conectar_bd();
    $stmt = mysqli_prepare($conn, "SELECT id_usuario FROM usuario WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $existe = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $existe;
}

function eliminar_usuario($id_usuario, $id_usuario_admin) {
    $conn = conectar_bd();

    if (!verificar_usuario_existe($id_usuario)) {
        throw new Exception("El usuario no existe.");
    }

    mysqli_begin_transaction($conn);

    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM usuario WHERE id_usuario = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al eliminar el usuario: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);

        registrar_actividad_gestion($id_usuario_admin, 'Eliminar usuario', "Usuario ID $id_usuario eliminado");

        mysqli_commit($conn);
        return "El usuario ha sido eliminado correctamente.";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}

function cambiar_estado_usuario($id_usuario, $estado_actual, $id_usuario_admin) {
    $conn = conectar_bd();
    $nuevo_estado = $estado_actual === 'activo' ? 'inactivo' : 'activo';

    mysqli_begin_transaction($conn);

    try {
        $stmt = mysqli_prepare($conn, "UPDATE usuario SET estado_usuario = ? WHERE id_usuario = ?");
        mysqli_stmt_bind_param($stmt, 'si', $nuevo_estado, $id_usuario);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al actualizar el estado: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);

        $accion = $nuevo_estado === 'activo' ? 'Activar usuario' : 'Desactivar usuario';
        $detalle = "Usuario ID $id_usuario cambiado a estado '$nuevo_estado'";
        registrar_actividad_gestion($id_usuario_admin, $accion, $detalle);

        mysqli_commit($conn);
        return "El estado del usuario ha sido actualizado correctamente.";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}

function registrar_actividad_gestion($id_usuario, $accion, $detalle) {
    $conn = conectar_bd();
    $fecha = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($conn, "INSERT INTO actividad (id_usuario, accion, detalle, fecha) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'isss', $id_usuario, $accion, $detalle, $fecha);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function procesar_eliminacion_usuario($id_usuario_admin) {
    if (!isset($_POST['id_usuario'])) {
        throw new Exception("ID de usuario no proporcionado");
    }

    $id_usuario = intval($_POST['id_usuario']);
    return eliminar_usuario($id_usuario, $id_usuario_admin);
}

function procesar_cambio_estado($id_usuario_admin) {
    if (!isset($_POST['id_usuario']) || !isset($_POST['estado_usuario'])) {
        throw new Exception("Datos insuficientes para cambiar estado");
    }

    $id_usuario = intval($_POST['id_usuario']);
    $estado_actual = $_POST['estado_usuario'];
    return cambiar_estado_usuario($id_usuario, $estado_actual, $id_usuario_admin);
}

function procesar_gestion_usuarios() {
    header('Content-Type: application/json; charset=utf-8');
    require_once("../../PHP/conexion.php");

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        enviar_respuesta_gestion(false, "Método no permitido");
    }

    try {
        $id_usuario_admin = validar_sesion_admin();

        if (isset($_POST['eliminar_usuario'])) {
            $mensaje = procesar_eliminacion_usuario($id_usuario_admin);
            enviar_respuesta_gestion(true, $mensaje);
        }

        if (isset($_POST['cambiar_estado'])) {
            $mensaje = procesar_cambio_estado($id_usuario_admin);
            enviar_respuesta_gestion(true, $mensaje);
        }

        enviar_respuesta_gestion(false, "Acción no válida");

    } catch (Exception $e) {
        enviar_respuesta_gestion(false, $e->getMessage());
    }
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    procesar_gestion_usuarios();
}
?>