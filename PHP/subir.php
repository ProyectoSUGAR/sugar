<?php
require_once ("../conexion.php");


$conn = conectar_bd();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $cantidad = $_POST["tipo"];
 /*   $horario = $_POST["turno"];
    $disponibilidad = $_POST["disp"]; */

    

    // insertar_datos es una funcion que inserta los datos en la base de datos 
    recurso_guardar($conn, $nombre, $cantidad);
}

function recurso_guardar($conn, $nombre, $cantidad) {
            $consulta_insertar = "INSERT INTO recurso (nombre, tipo) VALUES ('$nombre', '$cantidad')";


}

if (isset($conn) && $conn instanceof mysqli) {
    if (@$conn->ping()) {

    }
}



if (isset($_FILES['imagen'])) {
    $tipo = $_FILES['imagen']['name'];
    $tmp = $_FILES['imagen']['tmp_name'];

    // Carpeta donde se guardarán las imágenes
    $carpeta = "../../images/";

    //si la carpeta no existe se crea con permismos recursivos, o sea tambien a todos los archivos dentro de esta
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $url = $carpeta . basename($tipo);

    if (move_uploaded_file($tmp, $url)) {
        $sql = "INSERT INTO imagen (tipo, url) VALUES ('$tipo', '$url')";
        $conn->query($sql);
        echo "Imagen subida correctamente.";
    } else {
        echo "Error al subir la imagen.";
    }
}




?>
