
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
    //mysql:host=localhost;dbname=sugar;charset=utf8 es para conexión con MySQL usando PDO
    $dsn = "mysql:host=$DB_SERVIDOR;dbname=$DB_NOMBRE;charset=utf8";
    // Intentar la conexión y manejar errores
    try {
        // pdo sirvve para acceder a bases de datos en PHP
        $pdo = new PDO($dsn, $DB_USUARIO, $DB_PASS);
        // Configurar el modo de error de PDO a excepción
        //setAttribute establece un atributo en el objeto PDO
        //pdo::ATTR_ERRMODE es un atributo que define el modo de error
        //pdo::ERRMODE_EXCEPTION es un valor que indica que se lanzará una excepción en caso de error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
        // Si la conexión falla, se captura la excepción y se muestra un mensaje de error
        //PDOException es una clase que maneja errores de PDO
    } catch (PDOException $e) {
        //getMessage devuelve un mensaje de error de la excepción
        die("Error de conexión PDO: " . $e->getMessage());
    }
}

// Función para conectar con mysqli
function conectar_bd() {
    // variables globales para la conexión
    global $DB_SERVIDOR, $DB_USUARIO, $DB_PASS, $DB_NOMBRE;
    //mysqli_connect establece una conexión con un servidor MySQL
    $conn = mysqli_connect($DB_SERVIDOR, $DB_USUARIO, $DB_PASS, $DB_NOMBRE);
    // condiciponal para verificar la conexión
    if (!$conn) {
        //mysqli_connect_error devuelve una cadena con la descripción del último error de conexión
        die("Error de conexión: " . mysqli_connect_error());
    }
    return $conn;
}

// $con= conectar_bd();
?>
