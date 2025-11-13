<?php 
// Iniciar sesión si no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../Login/HTML/ingreso.php');
    exit();
}

// Verificar que sea secretaria
if ($_SESSION['tipo_usuario'] !== 'secretaria') {
    header('Location: ../../index.html');
    exit();
}

require_once("../../PHP/conexion.php");
include '../../HEADERS/headerS.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recursos</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="body-recursos">
    <main class="recursos-main">
        <h1 class="recursos-title">Gestión de Recursos</h1>
        <div class="recursos-grid">
            <div class="recursos-form-container">
                <form id="recursoForm" class="recursos-form" enctype="multipart/form-data" action="../../Secretaria/PHP/funcAsignRec.php" method="POST">
                    <h2 class="form-title">
                        <i class="fas fa-plus-circle"></i>
                        <span>Registrar Nuevo Recurso</span>
                    </h2>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre del Recurso</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo de Recurso</label>
                        <select id="tipo" name="tipo" class="form-control" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="alargue">Alargue</option>
                            <option value="proyector">Proyector</option>
                            <option value="laboratorio">Laboratorio</option>
                            <option value="aula">Aula Especial</option>
                            <option value="equipo">Equipo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="imagen">Imagen del Recurso</label>
                        <div class="file-upload">
                            <input type="file" id="imagen" name="imagen" accept="image/*">
                            <label for="imagen" class="file-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                Seleccionar imagen
                            </label>
                        </div>
                        <div id="preview" class="image-preview"></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="action" value="crear" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <div class="recursos-list-container">
                <h2 class="list-title">
                    <i class="fas fa-list"></i>
                    Recursos Disponibles
                </h2>
                
                <div class="recursos-filters">
                    <input type="text" placeholder="Buscar recurso..." class="search-input" id="searchInput">
                    <select class="filter-select" id="filterType">
                        <option value="">Todos los tipos</option>
                        <option value="alargue">Alargue</option>
                        <option value="proyector">Proyector</option>
                        <option value="laboratorio">Laboratorio</option>
                        <option value="aula">Aula Especial</option>
                        <option value="equipo">Equipo</option>
                    </select>
                </div>

                <div class="recursos-grid-list" id="recursosList">
                    <!-- Los recursos se cargarán aquí dinámicamente -->
                </div>
            </div>
        </div>
    </main>

    <script src="../../JS/recursosFunciones.js"></script>
</body>
</html>