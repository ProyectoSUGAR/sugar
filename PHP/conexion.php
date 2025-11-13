<?php
$DB_SERVIDOR = "localhost";
$DB_NOMBRE = "db_sugar";
$DB_USUARIO = "sugar";
$DB_PASS = "sugar";
function conectar_pdo() {
    global $DB_SERVIDOR, $DB_USUARIO, $DB_PASS, $DB_NOMBRE;
    $dsn = "mysql:host=$DB_SERVIDOR;dbname=$DB_NOMBRE;charset=utf8";
    try {
        $pdo = new PDO($dsn, $DB_USUARIO, $DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        require_once("errores.php");
        die_error("db_connection_pdo", $e->getMessage());
    }
}
function conectar_bd() {
    $conn = mysqli_connect("localhost", "db_sugar", "sugar", "sugar");
    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, "utf8");
    return $conn;
}
?>
