<?php
header('Content-Type: application/json');
require_once('../../PHP/conexion.php');
$con = conectar_bd();
$qAlumnos = mysqli_query($con, "SELECT COUNT(*) AS total FROM alumno");
$alumnos = mysqli_fetch_assoc($qAlumnos)['total'];
$qProfesores = mysqli_query($con, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario = 'profesor'");
$profesores = mysqli_fetch_assoc($qProfesores)['total'];
$qGrupos = mysqli_query($con, "SELECT COUNT(*) AS total FROM grupo");
$grupos = mysqli_fetch_assoc($qGrupos)['total'];
$qSecretarios = mysqli_query($con, "SELECT COUNT(*) AS total FROM secretaria");
$secretarios = mysqli_fetch_assoc($qSecretarios)['total'];
$qSalonesLibres = mysqli_query($con, "
    SELECT COUNT(*) AS total FROM espacio 
    WHERE tipo_espacio='salon' AND id_espacio NOT IN (
        SELECT id_espacio FROM reserva 
        WHERE fecha_inicio <= NOW() AND fecha_fin >= NOW() AND estado='aprobada'
    )
");
$salones_libres = mysqli_fetch_assoc($qSalonesLibres)['total'];
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
echo json_encode([
    "alumnos" => $alumnos,
    "profesores" => $profesores,
    "grupos" => $grupos,
    "secretarios" => $secretarios,
    "salones_libres" => $salones_libres,
    "grafico" => $graficoData
]);
?>