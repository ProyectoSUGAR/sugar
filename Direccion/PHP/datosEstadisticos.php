<?php
// elementos JSON 
header('Content-Type: application/json');

// llamado a la conexion con la base de datos
require '../../PHP/conexion.php';

// conexion con la base de datos
$con = conectar_bd();

// alumnos registrados
//mysql_query es una funcion que ejecuta la consulta SQL
$qAlumnos = mysqli_query($con, "SELECT COUNT(*) AS total FROM alumno");

// total de alumnos
// mysql_fetch_assoc es una funcion que obtiene el valor de la consulta SQL
$alumnos = mysqli_fetch_assoc($qAlumnos)['total'];

// profesores registrados
//mysql_query es una funcion que ejecuta la consulta SQL
$qProfesores = mysqli_query($con, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario = 'profesor'");

// total de profesores
// mysql_fetch_assoc es una funcion que obtiene el valor de la consulta SQL
$profesores = mysqli_fetch_assoc($qProfesores)['total'];

// grupos registrados
//mysql_query es una funcion que ejecuta la consulta SQL
$qGrupos = mysqli_query($con, "SELECT COUNT(*) AS total FROM grupo");

// total de grupos
//mysql_query es una funcion que ejecuta la consulta SQL
$grupos = mysqli_fetch_assoc($qGrupos)['total'];

// secretarios registrados
//mysql_query es una funcion que ejecuta la consulta SQL
$qSecretarios = mysqli_query($con, "SELECT COUNT(*) AS total FROM secretaria");

// total de secretarios
// mysql_fetch_assoc es una funcion que obtiene el valor de la consulta SQL
$secretarios = mysqli_fetch_assoc($qSecretarios)['total'];

// aalones libres 
//mysql_query es una funcion que ejecuta la consulta SQL
$qSalonesLibres = mysqli_query($con, "
    SELECT COUNT(*) AS total FROM espacio 
    WHERE tipo_espacio='salon' AND id_espacio NOT IN (
        SELECT id_espacio FROM reserva 
        WHERE fecha_inicio <= NOW() AND fecha_fin >= NOW() AND estado='aprobada'
    )
");

// total de salones libres
$salones_libres = mysqli_fetch_assoc($qSalonesLibres)['total'];

// profesores presentes en este momento
//mysql_query es una funcion que ejecuta la consulta SQL
$qProfesoresPresentes = mysqli_query($con, "
    SELECT COUNT(DISTINCT r.id_usuario) AS total
    FROM reserva r
    INNER JOIN profesor p ON r.id_usuario = p.id_usuario
    WHERE r.fecha_inicio <= NOW() AND r.fecha_fin >= NOW() AND r.estado='aprobada'
");

// total de profesores presentes
// mysql_fetch_assoc($qProfesoresPresentes)['total']; es una funcion que obtiene el valor de la consulta SQL
$profesores_presentes = mysqli_fetch_assoc($qProfesoresPresentes)['total'];

// grafico: profesores que no han asistido esta semana
$grafico = [2, 7, 5, 10, 4];

// envio de datos en formato JSON
// json_encode es una funcion que convierte un array en formato JSON
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