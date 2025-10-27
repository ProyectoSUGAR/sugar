<?php
require_once __DIR__ . '/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = conectar_pdo();
    try {
        $id_profesor = $_POST['id_profesor'] ?? null;
        $id_asignatura = $_POST['id_asignatura'] ?? null;
        $id_espacio = $_POST['id_espacio'] ?? null;
        $dia = $_POST['dia'] ?? null;
        $turno = $_POST['turno'] ?? null;
        $horario = $_POST['hora'] ?? null;
        $id_asocia = isset($_POST['id_asocia']) ? intval($_POST['id_asocia']) : null;
        if (!$id_profesor || !$id_asignatura || !$id_espacio || !$dia || !$turno || !$horario) {
            throw new Exception("Todos los campos son obligatorios");
        }
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM asocia WHERE 
            dia_semana = ? AND 
            horario = ? AND 
            id_espacio = ? AND
            id_asocia != COALESCE(?, 0)");
        $stmt->execute([$dia, $horario, $id_espacio, $id_asocia]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0) {
            throw new Exception("Ya existe una clase programada en ese horario y espacio");
        }
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM asocia WHERE 
            dia_semana = ? AND 
            horario = ? AND 
            id_profesor = ? AND
            id_asocia != COALESCE(?, 0)");
        $stmt->execute([$dia, $horario, $id_profesor, $id_asocia]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0) {
            throw new Exception("El profesor ya tiene una clase asignada en ese horario");
        }
        $pdo->beginTransaction();
        if ($id_asocia) {
            $stmt = $pdo->prepare("UPDATE asocia SET 
                id_profesor = ?,
                id_asignatura = ?,
                id_espacio = ?,
                dia_semana = ?,
                turno = ?,
                horario = ?
                WHERE id_asocia = ?");
            $stmt->execute([$id_profesor, $id_asignatura, $id_espacio, $dia, $turno, $horario, $id_asocia]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO asocia 
                (id_profesor, id_asignatura, id_espacio, dia_semana, turno, horario) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_profesor, $id_asignatura, $id_espacio, $dia, $turno, $horario]);
        }
        $accion = $id_asocia ? 'actualización_horario' : 'registro_horario';
        $detalle = "Horario " . ($id_asocia ? "actualizado" : "registrado") . 
                   " para profesor ID=$id_profesor, asignatura ID=$id_asignatura";
        $stmt = $pdo->prepare("INSERT INTO actividad (id_usuario, accion, detalle) VALUES (?, ?, ?)");
        $stmt->execute([$id_profesor, $accion, $detalle]);
        $pdo->commit();
        header("Location: ../Adscripta/HTML/registroDatos.php?success=1");
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Error: " . $e->getMessage());
    }
} else {
    die("Método no permitido");
}
?>