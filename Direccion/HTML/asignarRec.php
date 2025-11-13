<?php
include '../../HEADERS/headerD.php';
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
   <label for="hor-turno" class="sugarads-label">Turno</label>
   <select class="sasignacion1" id="hor-turno" name="turno" required>
    <option value="">Seleccionar turno</option>
    <option value="manana">Matutino</option>
    <option value="tarde">Vespertino</option>
    <option value="noche">Nocturno</option>
</select>
   <label for="hor-hora" class="sugarads-label">Bloque Horario (selecciona uno o varios)</label>
   <select id="hor-hora" name="hora[]" class="sasignacion1" multiple required>
    <option value="">Selecciona un bloque horario</option>
</select>
   <input id="guardarecurso" class="regasigboton" type="submit"></input>
  <button id="cancelarecurso" class="botoneliminar" type="button" style="margin-top: 2rem;">Cancelar</button>
</form>
</main>
</body>
<script src="../../Adscripta/JS/bloquesHorarios.js"></script>