<?php
function obtener_estadisticas_alumnos($conn) {
    // Contar de tabla alumno primero; si no hay, contar de usuario
    $qAlumnos = mysqli_query($conn, "SELECT COUNT(*) AS total FROM alumno");
    $result = mysqli_fetch_assoc($qAlumnos);
    $count = (int)($result['total'] ?? 0);
    
    if ($count == 0) {
        // Fallback: contar usuarios que sean estudiantes
        $qAlt = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario IN ('alumno', 'estudiante')");
        $countAlt = mysqli_fetch_assoc($qAlt);
        return (int)($countAlt['total'] ?? 0);
    }
    return $count;
}

function obtener_estadisticas_profesores($conn) {
    $qProfesores = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario = 'profesor'");
    return mysqli_fetch_assoc($qProfesores)['total'];
}

function obtener_estadisticas_grupos($conn) {
    $qGrupos = mysqli_query($conn, "SELECT COUNT(*) AS total FROM grupo");
    return mysqli_fetch_assoc($qGrupos)['total'];
}

function obtener_estadisticas_secretarios($conn) {
    // Contar secretarios desde la tabla de usuarios según el tipo registrado
    $qSecretarios = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario WHERE tipo_usuario = 'secretaria'");
    return mysqli_fetch_assoc($qSecretarios)['total'];
}

function obtener_estadisticas_reservas_pendientes($conn) {
    // Contar reservas con estado 'pendiente' (esperando aprobación)
    $qReservasPendientes = mysqli_query($conn, "
        SELECT COUNT(*) AS total FROM reserva
        WHERE estado = 'pendiente'
    ");
    return (int)mysqli_fetch_assoc($qReservasPendientes)['total'];
}

function obtener_datos_grafico_clases($conn) {
    $qClasesPorDia = mysqli_query($conn, "
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
        $dia = ucfirst($row['dia_semana']);
        $turno = $row['turno'] === 'manana' ? 'Mañana' : ucfirst($row['turno']);
        $graficoData[] = [
            'dia' => $dia,
            'turno' => $turno,
            'clases' => (int)$row['total']
        ];
    }
    return $graficoData;
}

function obtener_estadisticas_completas() {
    header('Content-Type: application/json');
    require_once('../../PHP/conexion.php');
    $conn = conectar_bd();

    $alumnos = obtener_estadisticas_alumnos($conn);
    $profesores = obtener_estadisticas_profesores($conn);
    $grupos = obtener_estadisticas_grupos($conn);
    $secretarios = obtener_estadisticas_secretarios($conn);
    $reservas_pendientes = obtener_estadisticas_reservas_pendientes($conn);
    $graficoData = obtener_datos_grafico_clases($conn);

    echo json_encode([
        "alumnos" => (int)$alumnos,
        "profesores" => (int)$profesores,
        "grupos" => (int)$grupos,
        "secretarios" => (int)$secretarios,
        "reservas_pendientes" => (int)$reservas_pendientes,
        "grafico" => $graficoData
    ]);
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    obtener_estadisticas_completas();
}
