<?php
// Parámetros de conexión globales
$DB_SERVIDOR = "localhost";
$DB_NOMBRE = "sugar";
$DB_USUARIO = "root";
$DB_PASS = "";

// Función para conectar con PDO
function conectar_pdo() {
    // variables globales para la conexión
    global $DB_SERVIDOR, $DB_USUARIO, $DB_PASS, $DB_NOMBRE;
    // Crear el DSN (Data Source Name)
    $dsn = "mysql:host=$DB_SERVIDOR;dbname=$DB_NOMBRE;charset=utf8";
    try {
        $pdo = new PDO($dsn, $DB_USUARIO, $DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión PDO: " . $e->getMessage());
    }
}

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
