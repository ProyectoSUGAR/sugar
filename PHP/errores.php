<?php
function die_error($type, $message) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $type, 'message' => $message]);
    exit;
}
?>
