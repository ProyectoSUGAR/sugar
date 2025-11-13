<?php
header('Content-Type: application/json; charset=utf-8');

// Verificar sesión
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Validar que el usuario esté autenticado y sea secretaria
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
    http_response_code(401);
    echo json_encode([
        'éxito' => false,
        'mensaje' => 'No autorizado'
    ]);
    exit;
}

// Incluir conexión a base de datos
require_once '../../PHP/conexion.php';

$conn = conectar_bd();

$id_usuario = $_SESSION['id_usuario'];
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

/**
 * CRUD FUNCTION: Crear nuevo recurso
 */
function crear_recurso() {
    global $conn, $id_usuario;
    
    try {
        // Validar datos requeridos
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        
        if (empty($nombre) || empty($tipo) || empty($descripcion)) {
            throw new Exception('Nombre, tipo y descripción son requeridos');
        }
        
        // Validar tipos permitidos
        $tipos_permitidos = ['alargue', 'proyector', 'laboratorio', 'aula', 'equipo'];
        if (!in_array($tipo, $tipos_permitidos)) {
            throw new Exception('Tipo de recurso no válido');
        }
        
        // Insertar recurso
        $sql = "INSERT INTO recurso (nombre, tipo, descripcion) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Error en preparación: ' . $conn->error);
        }
        
        $stmt->bind_param('sss', $nombre, $tipo, $descripcion);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al insertar: ' . $stmt->error);
        }
        
        $id_recurso = $stmt->insert_id;
        $stmt->close();
        
        // Manejar carga de imagen si existe
        $imagen_path = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen_path = subir_imagen($_FILES['imagen'], $id_recurso);
            
            if ($imagen_path) {
                // Guardar referencia de imagen en BD
                $sql_img = "UPDATE recurso SET imagen = ? WHERE id_recurso = ?";
                $stmt_img = $conn->prepare($sql_img);
                $stmt_img->bind_param('si', $imagen_path, $id_recurso);
                $stmt_img->execute();
                $stmt_img->close();
            }
        }
        
        // Registrar en historial
        registrar_actividad($id_usuario, 'CREAR_RECURSO', "Se creó recurso: $nombre");
        
        http_response_code(201);
        echo json_encode([
            'éxito' => true,
            'mensaje' => 'Recurso creado exitosamente',
            'datos' => [
                'id' => $id_recurso,
                'nombre' => $nombre
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'éxito' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
    exit;
}

// Manejo de eliminación (continúa abajo)
/**
 * CRUD: Obtener recursos (lista o uno por id)
 */
function obtener_recursos() {
    global $conn;
    try {
        $id_filtro = isset($_GET['id']) ? intval($_GET['id']) : null;

        if ($id_filtro) {
            $sql = "SELECT id_recurso as id, nombre, tipo, descripcion, imagen FROM recurso WHERE id_recurso = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id_filtro);
        } else {
            $sql = "SELECT id_recurso as id, nombre, tipo, descripcion, imagen FROM recurso ORDER BY nombre ASC";
            $stmt = $conn->prepare($sql);
        }

        if (!$stmt->execute()) {
            throw new Exception('Error en consulta: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $recursos = [];
        while ($row = $result->fetch_assoc()) {
            $recursos[] = $row;
        }
        $stmt->close();

        echo json_encode(['éxito' => true, 'datos' => $recursos]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['éxito' => false, 'mensaje' => $e->getMessage()]);
    }
    exit;
}

/**
 * CRUD: Actualizar recurso
 */
function actualizar_recurso() {
    global $conn, $id_usuario;
    try {
        $id_recurso = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
        if ($id_recurso <= 0) throw new Exception('ID de recurso inválido');

        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

        if (empty($nombre) || empty($tipo) || empty($descripcion)) {
            throw new Exception('Todos los campos son requeridos');
        }

        $tipos_permitidos = ['alargue', 'proyector', 'laboratorio', 'aula', 'equipo'];
        if (!in_array($tipo, $tipos_permitidos)) throw new Exception('Tipo de recurso no válido');

        $sql = "UPDATE recurso SET nombre = ?, tipo = ?, descripcion = ? WHERE id_recurso = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception('Error en preparación: ' . $conn->error);
        $stmt->bind_param('sssi', $nombre, $tipo, $descripcion, $id_recurso);
        if (!$stmt->execute()) throw new Exception('Error al actualizar: ' . $stmt->error);
        $stmt->close();

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen_path = subir_imagen($_FILES['imagen'], $id_recurso);
            if ($imagen_path) {
                $sql_img = "UPDATE recurso SET imagen = ? WHERE id_recurso = ?";
                $stmt_img = $conn->prepare($sql_img);
                $stmt_img->bind_param('si', $imagen_path, $id_recurso);
                $stmt_img->execute();
                $stmt_img->close();
            }
        }

        registrar_actividad($id_usuario, 'ACTUALIZAR_RECURSO', "Se actualizó recurso ID: $id_recurso");
        echo json_encode(['éxito' => true, 'mensaje' => 'Recurso actualizado exitosamente']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['éxito' => false, 'mensaje' => $e->getMessage()]);
    }
    exit;
}

// Ahora continúa la función de eliminación
function eliminar_recurso() {
    global $conn, $id_usuario;
    
    try {
        $id_recurso = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id_recurso <= 0) {
            throw new Exception('ID de recurso inválido');
        }
        
        // Obtener datos del recurso antes de eliminar
        $sql_select = "SELECT imagen FROM recurso WHERE id_recurso = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param('i', $id_recurso);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $recurso = $result->fetch_assoc();
        $stmt_select->close();
        
        if (!$recurso) {
            throw new Exception('Recurso no encontrado');
        }
        
        // Eliminar archivo de imagen si existe
        if ($recurso['imagen'] && file_exists($recurso['imagen'])) {
            unlink($recurso['imagen']);
        }
        
        // Eliminar recurso de BD
        $sql = "DELETE FROM recurso WHERE id_recurso = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Error en preparación: ' . $conn->error);
        }
        
        $stmt->bind_param('i', $id_recurso);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar: ' . $stmt->error);
        }
        
        $stmt->close();
        
        registrar_actividad($id_usuario, 'ELIMINAR_RECURSO', "Se eliminó recurso ID: $id_recurso");
        
        http_response_code(200);
        echo json_encode([
            'éxito' => true,
            'mensaje' => 'Recurso eliminado exitosamente'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'éxito' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
}

/**
 * Función auxiliar: Subir imagen
 */
function subir_imagen($file, $id_recurso) {
    try {
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_ext, $allowed_ext)) {
            throw new Exception('Formato de imagen no permitido');
        }
        
        // Crear directorio si no existe
        $upload_dir = '../../Images/recursos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generar nombre único
        $new_filename = 'recurso_' . $id_recurso . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $new_filename;
        
        // Validar tamaño (máximo 5MB)
        if ($file['size'] > 5242880) {
            throw new Exception('La imagen es demasiado grande (máximo 5MB)');
        }
        
        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            throw new Exception('Error al mover archivo');
        }
        
        return $file_path;
        
    } catch (Exception $e) {
        throw new Exception('Error en carga de imagen: ' . $e->getMessage());
    }
}

/**
 * Función auxiliar: Registrar actividad
 */
function registrar_actividad($id_usuario, $accion, $detalle) {
    global $conn;
    
    try {
        $sql = "INSERT INTO actividad (id_usuario, accion, detalle, fecha) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iss', $id_usuario, $accion, $detalle);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        // No interrumpir operación principal si falla el historial
        error_log('Error registrando actividad: ' . $e->getMessage());
    }
}

// Enrutador CRUD
switch ($action) {
    case 'crear':
        crear_recurso();
        break;
    case 'obtener':
        obtener_recursos();
        break;
    case 'actualizar':
        actualizar_recurso();
        break;
    case 'eliminar':
        eliminar_recurso();
        break;
    default:
        http_response_code(400);
        echo json_encode([
            'éxito' => false,
            'mensaje' => 'Acción no especificada o no válida'
        ]);
}
