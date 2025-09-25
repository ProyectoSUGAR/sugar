<?php
// Parámetros de conexión globales
$DB_SERVIDOR = "localhost";
$DB_NOMBRE = "sugar";
$DB_USUARIO = "root";
$DB_PASS = "";

// Función para conectar con mysqli
function conectar_bd() {
    global $DB_SERVIDOR, $DB_USUARIO, $DB_PASS, $DB_NOMBRE;
    $conn = mysqli_connect($DB_SERVIDOR, $DB_USUARIO, $DB_PASS, $DB_NOMBRE);
    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    return $conn;
}

// $con= conectar_bd();
?>
