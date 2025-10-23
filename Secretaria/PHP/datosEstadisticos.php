<?php
// elementos JSON 
header('Content-Type: application/json');

// llamado a la conexion con la base de datos
require_once('../../PHP/conexion.php');

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

// salones libres en este momento
$qSalonesLibres = mysqli_query($con, "
    SELECT COUNT(*) AS total FROM espacio 
    WHERE tipo_espacio='salon' AND id_espacio NOT IN (
        SELECT id_espacio FROM reserva 
        WHERE fecha_inicio <= NOW() AND fecha_fin >= NOW() AND estado='aprobada'
    )
");

// total de salones libres
$salones_libres = mysqli_fetch_assoc($qSalonesLibres)['total'];

// gráfico: distribución de asignaturas por turno y día
$qClasesPorDia = mysqli_query($con, "
    SELECT 
        dia_semana,
        turno,
        COUNT(*) as total
    FROM asocia 
    GROUP BY dia_semana, turno
    ORDER BY FIELD(dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes'), 
             FIELD(turno, 'manana', 'tarde', 'noche')
");

$graficoData = [];
while ($row = mysqli_fetch_assoc($qClasesPorDia)) {
    $dia = ucfirst($row['dia_semana']); // Primera letra en mayúscula
    $turno = $row['turno'] === 'manana' ? 'Mañana' : ucfirst($row['turno']); // Corregir "manana" a "Mañana"
    $graficoData[] = [
        'dia' => $dia,
        'turno' => $turno,
        'clases' => (int)$row['total']
    ];
}

// envio de datos en formato JSON
echo json_encode([
    "alumnos" => $alumnos,
    "profesores" => $profesores,
    "grupos" => $grupos,
    "secretarios" => $secretarios,
    "salones_libres" => $salones_libres,
    "grafico" => $graficoData
]);
?>