<?php
require_once(__DIR__ . '/../../PHP/conexion.php');

function obtenerProfesores() {
    $pdo = conectar_pdo();
    $sql = "SELECT u.id_usuario, u.nombre, u.apellido, COALESCE(GROUP_CONCAT(a.nombre SEPARATOR ', '), 'Sin asignaturas') AS asignaturas
            FROM usuario u
            INNER JOIN profesor p ON u.id_usuario = p.id_usuario
            LEFT JOIN profesor_asignatura pa ON p.id_usuario = pa.id_profesor
            LEFT JOIN asignatura a ON pa.id_asignatura = a.id_asignatura
            WHERE u.tipo_usuario = 'profesor'
            GROUP BY u.id_usuario, u.nombre, u.apellido
            ORDER BY u.apellido, u.nombre";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $profesores;
    } catch (PDOException $e) {
        return [];
    }
}
?>
