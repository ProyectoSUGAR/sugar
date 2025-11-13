<?php
/**
 * Funciones para gestión de profesores
 */

require_once(__DIR__ . '/../../PHP/conexion.php');

/**
 * Obtiene lista de todos los profesores con sus asignaturas
 * @return array Lista de profesores
 */
function obtenerProfesores() {
    try {
        $pdo = conectar_pdo();
        $sql = "SELECT 
                    u.id_usuario, 
                    u.nombre, 
                    u.apellido, 
                    u.correo,
                    COALESCE(GROUP_CONCAT(a.nombre SEPARATOR ', '), 'Sin asignaturas') AS asignaturas
                FROM usuario u
                INNER JOIN profesor p ON u.id_usuario = p.id_usuario
                LEFT JOIN profesor_asignatura pa ON p.id_usuario = pa.id_profesor
                LEFT JOIN asignatura a ON pa.id_asignatura = a.id_asignatura
                WHERE u.tipo_usuario = 'profesor' AND u.estado_usuario = 'activo'
                GROUP BY u.id_usuario, u.nombre, u.apellido, u.correo
                ORDER BY u.apellido, u.nombre";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $profesores;
    } catch (PDOException $e) {
        error_log("Error al obtener profesores: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene información de un profesor específico
 * @param int $id_profesor ID del profesor
 * @return array|false Datos del profesor o false si no existe
 */
function obtenerProfesor($id_profesor) {
    try {
        $pdo = conectar_pdo();
        $sql = "SELECT 
                    u.id_usuario, 
                    u.nombre, 
                    u.apellido, 
                    u.correo,
                    p.especialidad,
                    p.horas_semanales,
                    COALESCE(GROUP_CONCAT(a.id_asignatura), '') AS id_asignaturas,
                    COALESCE(GROUP_CONCAT(a.nombre SEPARATOR ', '), 'Sin asignaturas') AS asignaturas
                FROM usuario u
                INNER JOIN profesor p ON u.id_usuario = p.id_usuario
                LEFT JOIN profesor_asignatura pa ON p.id_usuario = pa.id_profesor
                LEFT JOIN asignatura a ON pa.id_asignatura = a.id_asignatura
                WHERE u.id_usuario = ? AND u.tipo_usuario = 'profesor'
                GROUP BY u.id_usuario";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_profesor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener profesor: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene todas las asignaturas disponibles
 * @return array Lista de asignaturas
 */
function obtenerAsignaturas() {
    try {
        $pdo = conectar_pdo();
        $sql = "SELECT id_asignatura, nombre FROM asignatura ORDER BY nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener asignaturas: " . $e->getMessage());
        return [];
    }
}

/**
 * Actualiza las asignaturas de un profesor
 * @param int $id_profesor ID del profesor
 * @param array $id_asignaturas Array de IDs de asignaturas
 * @return bool Éxito de la operación
 */
function actualizarAsignaturasProfesor($id_profesor, $id_asignaturas = []) {
    try {
        $pdo = conectar_pdo();
        
        // Eliminar asignaturas actuales
        $sql_delete = "DELETE FROM profesor_asignatura WHERE id_profesor = ?";
        $stmt = $pdo->prepare($sql_delete);
        $stmt->execute([$id_profesor]);
        
        // Insertar nuevas asignaturas
        if (!empty($id_asignaturas)) {
            $sql_insert = "INSERT INTO profesor_asignatura (id_profesor, id_asignatura) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql_insert);
            foreach ($id_asignaturas as $id_asignatura) {
                $stmt->execute([$id_profesor, $id_asignatura]);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error al actualizar asignaturas del profesor: " . $e->getMessage());
        return false;
    }
}
?>

