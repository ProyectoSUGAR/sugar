<?php
include '../../PHP/header.php';

require("../../PHP/conexion.php");

?>

<body>
    <main class="sugarads-main">
        <h1 class="sugarads-title">Asignaciones</h1>
        <div class="sugarads-grid registro-datos">
            <section class="sugarads-col-left">
                <form class="sugarads-form" method="post" action="../PHP/panelAsign.php">
                    <h2 class="sugarads-section-title">Asignar Directores, Adscriptas, Secretarias</h2>

                    
                    <div class="sugarads-field">
                        <label for="director" class="sugarads-label">Dirección</label>
                        <select id="director" name="id_director" required>
                            <option value="">Seleccionar Director</option>
                            <?php
                            $director = mysqli_query($con, "SELECT d.id_director, u.nombre, u.apellido FROM director d JOIN usuario u ON d.id_usuario = u.id_usuario");
                            while ($h = mysqli_fetch_object($director)) {
                                echo '<option value="' . $h->id_director . '">' . htmlspecialchars($h->nombre . ' ' . $h->apellido) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    
                    <div class="sugarads-field">
                        <label for="secretaria" class="sugarads-label">Secretaria</label>
                        <select id="secretaria" name="id_secretaria" required>
                            <option value="">Seleccionar Secreatria</option>
                            <?php
                            $secretaria = mysqli_query($con, "SELECT s.id_secretaria, u.nombre, u.apellido FROM secretaria s JOIN usuario u ON s.id_usuario = u.id_usuario");
                            while ($s = mysqli_fetch_object($secretaria)) {
                                echo '<option value="' . $s->id_secretaria . '">' . htmlspecialchars($s->nombre . ' ' . $s->apellido) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="sugarads-field">
                        <label for="adscripta" class="sugarads-label">Adscripta</label>
                        <select id="adscripta" name="id_adscripta" required>
                            <option value="">Seleccionar Adscripta</option>
                            <?php
                            $adscripta = mysqli_query($con, "SELECT a.id_adscripta, u.nombre, u.apellido FROM adscripta a JOIN usuario u ON a.id_usuario = u.id_usuario");
                            while ($a = mysqli_fetch_object($adscripta)) {
                                echo '<option value="' . $a->id_adscripta . '">' . htmlspecialchars($a->nombre . ' ' . $a->apellido) . '</option>';
                            }
                            ?>

                    </div>

                    <div class="sugarads-actions">
                        <button type="submit" class="sugarads-btn sugarads-btn-guardar">Asignar</button>
                        <button type="reset" class="sugarads-btn sugarads-btn-cancelar">Cancelar</button>

    </main>

    <script src="../JS/panelAsign.js"></script>
</body>