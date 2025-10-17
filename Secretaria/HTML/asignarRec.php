<?php
include '../../HEADERS/headerS.php';
require_once("../../PHP/conexion.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar recursos</title>
</head>
<body>
    
</body>
</html>
<body class="bodyregidat">
<main>
  <section class="sugarads-section titulo">
    <h1 class="sugarads-title">Asignar recursos</h1>
  </section>
<form action="../../PHP/subirimagen/subir.php" enctype="multipart/form-data" id="formulariorec" class="formasig" method="POST" style="max-width: 400px;">
 <label for="" class="sugarads-label">Nombre del recurso</label>
 <input type="text" class="inputasig1" id="nombre" name="nombre" required placeholder="Ingrese nombre del recurso">
  <label for="" class="sugarads-label">Cantidad</label>
 <input type="number" class="inputasig1" id="tipo" name="tipo" required placeholder="Ingrese la cantidad de recursos">
   <label for="" class="sugarads-label">Disponibilidad</label>
   <select  class="sasignacion1"  id="disp" name="disp" required>
    <option value="">Seleccionar disponibilidad</option>
    <option value="">Disponible</option>
    <option value="">No disponible</option>
</select>
   <label for="" class="sugarads-label">Turno</label>
   <select  class="sasignacion1"  id="turno" name="turno" required>
    <option value="">Seleccionar turno</option>
    <option value="">Matutino</option>
    <option value="">Vespertino</option>
    <option value="">Nocturno</option>
</select>
<select class="sasignacion1" id="disponibrec">
    <label>Seleccionar imagen:</label>
    <input type="file" name="imagen" required>

</select>


   <input id="guardarecurso" class="regasigboton" type="submit"></input>
  <button id="cancelarecurso" class="botoneliminar" type="button" style="margin-top: 2rem;">Cancelar</button>

</form>
</main>
</body>