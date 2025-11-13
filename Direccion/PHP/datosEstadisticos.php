<?php
function obtener_estadisticas_alumnos($conn) {
    $qAlumnos = mysqli_query($conn, "SELECT COUNT(*) AS total FROM alumno");
    return mysqli_fetch_assoc($qAlumnos)['total'];
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
    $qSecretarios = mysqli_query($conn, "SELECT COUNT(*) AS total FROM secretaria");
    return mysqli_fetch_assoc($qSecretarios)['total'];
}

function obtener_estadisticas_salones_libres($conn) {
    $qSalonesLibres = mysqli_query($conn, "
        SELECT COUNT(*) AS total FROM espacio
        WHERE tipo_espacio='salon' AND id_espacio NOT IN (
            SELECT id_espacio FROM reserva
            WHERE fecha_inicio <= NOW() AND fecha_fin >= NOW() AND estado='aprobada'
        )
    ");
    return mysqli_fetch_assoc($qSalonesLibres)['total'];
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
    $salones_libres = obtener_estadisticas_salones_libres($conn);
    $graficoData = obtener_datos_grafico_clases($conn);

    echo json_encode([
        "alumnos" => $alumnos,
        "profesores" => $profesores,
        "grupos" => $grupos,
        "secretarios" => $secretarios,
        "salones_libres" => $salones_libres,
        "grafico" => $graficoData
    ]);
}

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    obtener_estadisticas_completas();
}
