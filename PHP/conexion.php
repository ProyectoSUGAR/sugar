<?php
// Función para conectar a la base de datos
function conectar_bd() {
    // Parámetros de conexión
    $servidor = "localhost";
    // base de datos
    $bd = "sugar";
    // usuario
    $usuario = "root";
    // contraseña
    $pass = "";

    // Crear conexión
    // mysqli_connect es una función que establece una conexión con la base de datos
    $conn = mysqli_connect($servidor, $usuario, $pass, $bd);

    // Verificar conexión
    if (!$conn) {
        // Si la conexión falla, mostrar un mensaje de error y terminar el script
        die("Error de conexión: " . mysqli_connect_error());
    }

    // echo "anduvooo";
    // Retornar la conexión
    return $conn;
}

// $con= conectar_bd();

?>