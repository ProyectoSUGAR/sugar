<?php
require("conexion.php");
$con = conectar_bd();

$salonesPorPiso = [
    0 => ["Aula 1", "Laboratorio de Electrónica", "Laboratorio de Química", "Laboratorio de Robótica", "Zoom", "Taller"],
    1 => ["Aula 2", "Salón 1", "Salón 2", "Laboratorio de Física"],
    2 => ["Aula 3", "Salón 3", "Salón 4", "Salón 5"]
];

$turnos = ["manana", "tarde", "noche"];
$bloques = [1,2,3,4,5,6,7,8];
$horarios = [];

function obtener_piso($ubicacion) {
    $ubicacion = strtolower($ubicacion);
    if (strpos($ubicacion, 'planta baja') !== false) return 0;
    if (strpos($ubicacion, 'piso 1') !== false) return 1;
    if (strpos($ubicacion, 'piso 2') !== false) return 2;
    return 0;
}

$sql = "
SELECT 
    e.nombre AS espacio,
    e.ubicacion,
    a.turno,
    a.bloque,
    s.nombre AS materia,
    u.nombre AS profesor_nombre,
    u.apellido AS profesor_apellido
FROM asocia a
JOIN espacio e ON a.id_espacio = e.id_espacio
JOIN asignatura s ON a.id_asignatura = s.id_asignatura
LEFT JOIN profesor_asignatura pa ON pa.id_asignatura = a.id_asignatura
LEFT JOIN profesor p ON pa.id_profesor = p.id_usuario
LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
";

$res = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($res)) {
    $espacio = $row['espacio'];
    $ubicacion = $row['ubicacion'];
    $turno = $row['turno'];
    $bloque = (int)$row['bloque'];
    $materia = $row['materia'];
    $profesor = trim($row['profesor_nombre'] . ' ' . $row['profesor_apellido']);
    $piso = obtener_piso($ubicacion);

    if (in_array($espacio, $salonesPorPiso[$piso])) {
        $horarios[$piso][$turno][$espacio][$bloque] = [
            'materia' => $materia,
            'profesor' => $profesor
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($horarios);
mysqli_close($con);
?>