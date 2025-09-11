<?php
// Establece el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Importa el archivo que contiene la función de conexión a la base de datos
require '../../PHP/conexion.php';

// Ejecuta la función para conectar con la base de datos
$con = conectar_bd();

// Consulta SQL para contar el total de alumnos registrados
$qAlumnos = mysqli_query($con, "SELECT COUNT(*) AS total FROM alumno");

// Extrae el resultado de la consulta y obtiene el total de alumnos
$alumnos = mysqli_fetch_assoc($qAlumnos)['total'];

// Consulta SQL para contar el total de usuarios con rol de profesor
$qProfesores = mysqli_query($con, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario = 'profesor'");

// Extrae el resultado de la consulta y obtiene el total de profesores
$profesores = mysqli_fetch_assoc($qProfesores)['total'];

// Consulta SQL para contar el total de grupos registrados
$qGrupos = mysqli_query($con, "SELECT COUNT(*) AS total FROM grupo");

// Extrae el resultado de la consulta y obtiene el total de grupos
$grupos = mysqli_fetch_assoc($qGrupos)['total'];

// Consulta SQL para contar el total de secretarios registrados
$qSecretarios = mysqli_query($con, "SELECT COUNT(*) AS total FROM secretaria");

// Extrae el resultado de la consulta y obtiene el total de secretarios
$secretarios = mysqli_fetch_assoc($qSecretarios)['total'];

// Consulta SQL para contar los salones disponibles actualmente
// Se excluyen aquellos que están reservados en este momento con estado "aprobada"
$qSalonesLibres = mysqli_query($con, "
    SELECT COUNT(*) AS total FROM espacio 
    WHERE tipo_espacio='salon' AND id_espacio NOT IN (
        SELECT id_espacio FROM reserva 
        WHERE fecha_inicio <= NOW() AND fecha_fin >= NOW() AND estado='aprobada'
    )
");

// Extrae el resultado de la consulta y obtiene el total de salones libres
$salones_libres = mysqli_fetch_assoc($qSalonesLibres)['total'];

// Consulta SQL para contar los profesores presentes en este momento
// Se consideran reservas activas con estado 'aprobada'
$qProfesoresPresentes = mysqli_query($con, "
    SELECT COUNT(DISTINCT r.id_usuario) AS total
    FROM reserva r
    INNER JOIN profesor p ON r.id_usuario = p.id_usuario
    WHERE r.fecha_inicio <= NOW() AND r.fecha_fin >= NOW() AND r.estado='aprobada'
");

// Extrae el resultado de la consulta y obtiene el total de profesores presentes
$profesores_presentes = mysqli_fetch_assoc($qProfesoresPresentes)['total'];

// Datos simulados para el gráfico de ausencias de profesores durante la semana
$grafico = [2, 7, 5, 10, 4];

// Codifica todos los datos en formato JSON y los envía como respuesta
echo json_encode([
    "alumnos" => $alumnos,
    "profesores" => $profesores,
    "grupos" => $grupos,
    "secretarios" => $secretarios,
    "salones_libres" => $salones_libres,
    "profesores_presentes" => $profesores_presentes,
    "grafico" => $grafico
]);
?>