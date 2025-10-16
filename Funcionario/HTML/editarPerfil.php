<?php 
// Inclusión del encabezado común para la interfaz de editar perfíl
include '../../HEADERS/headerF.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
        <link rel="stylesheet" href="/SUGAR/Css/style.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bodyregidat">
    <form action="/Funcionario/HTML/editarPerfil.php" enctype="multipart/form-data" method="POST">
    <div class="diveditarperfil">
<h1 class="h1editperfil">Perfil</h1>
<h2 class="h2cambiarperfil">Cambiar foto de perfil</h2>
<h2 class="h2cambiarperfil1">Cambiar contraseña</h2>
<h2 class="h2cambiarperfil2">Cambiar correo electrónico</h2>
<div class="cambiarfotoperfil">
    <div class="fotodeperfil"></div>
<img src="/images/flechatriple.png" alt="" class="imagenflecha">
   <input type="file" name="imagen" required class="imga">
</div>
<input type="text" class="inputuser" placeholder="ejemplo@gmail.com">
<input type="text" class="inputuser1" placeholder="Insertar nueva contraseña">
<input type="submit" class="botoneditaru" name="guardardatosu" value="Guardar">

    </div>
    </form>
</body>
</html>

<?php
require_once ("../../PHP/conexion.php");


$conn = conectar_bd();


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
    echo '<script>';
   echo ' Swal.fire({';
 echo 'title: "Foto de perfil actualizada exitosamente!",';
 echo 'icon: "success",';
  echo'draggable: true';
echo '});';
    echo '</script>';
    } else {
        echo "Error al subir la imagen.";
    }
}




?>
