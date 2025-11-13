<?php
header('Content-Type: application/json');
require_once('../../PHP/conexion.php');
$con = conectar_bd();
$qAlumnos = mysqli_query($con, "SELECT COUNT(*) AS total FROM alumno");
$alumnos_result = mysqli_fetch_assoc($qAlumnos);
$alumnos = (int)($alumnos_result['total'] ?? 0);
if ($alumnos == 0) {
    // Fallback: contar estudiantes desde usuario si la tabla alumno está vacía
    $qAlt = mysqli_query($con, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario IN ('alumno', 'estudiante')");
    $alumnosAlt = mysqli_fetch_assoc($qAlt);
    $alumnos = (int)($alumnosAlt['total'] ?? 0);
}
$qProfesores = mysqli_query($con, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario = 'profesor'");
$profesores = mysqli_fetch_assoc($qProfesores)['total'];
$qGrupos = mysqli_query($con, "SELECT COUNT(*) AS total FROM grupo");
$grupos = mysqli_fetch_assoc($qGrupos)['total'];
$qSecretarios = mysqli_query($con, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario = 'secretaria'");
$secretarios = mysqli_fetch_assoc($qSecretarios)['total'];
$qReservasPendientes = mysqli_query($con, "SELECT COUNT(*) AS total FROM reserva WHERE estado = 'pendiente'");
$reservas_pendientes = mysqli_fetch_assoc($qReservasPendientes)['total'];
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
    "reservas_pendientes" => $reservas_pendientes,
    "grafico" => $graficoData
]);
?>