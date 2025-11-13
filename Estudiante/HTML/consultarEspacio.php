<?php include '../../HEADERS/headerE.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Espacios</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <!-- Estilos movidos a Css/style.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="body-consultas">
    <main class="consultas-main">
        <h1 class="consultas-title">Consulta de Espacios</h1>

        <div class="consultas-grid">
            <!-- Filtros y calendario -->
            <div class="filtros-container">
                <div class="search-section">
                    <h2 class="section-title">
                        <i class="fas fa-search"></i>
                        Filtros de Búsqueda
                    </h2>

                    <div class="filtros-form">
                        <div class="form-group">
                            <label for="tipo_espacio">Tipo de Espacio</label>
                            <select id="tipo_espacio" class="form-control">
                                <option value="">Todos los espacios</option>
                                <option value="aula">Aulas</option>
                                <option value="laboratorio">Laboratorios</option>
                                <option value="sala">Salas de Reuniones</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="capacidad">Capacidad Mínima</label>
                            <input type="number" id="capacidad" class="form-control" min="1">
                        </div>

                        <div class="form-group">
                            <label for="recursos">Recursos Necesarios</label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" value="proyector"> Proyector
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" value="computadoras"> Computadoras
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" value="audio"> Sistema de Audio
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="calendario-container">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Seleccionar Fecha
                    </h2>
                    <div id="calendario" class="calendario"></div>
                </div>
            </div>

            <!-- Lista de espacios -->
            <div class="espacios-container">
                <h2 class="section-title">
                    <i class="fas fa-building"></i>
                    Espacios Disponibles
                </h2>

                <div class="espacios-grid">
                    <!-- Ejemplo de tarjeta de espacio -->
                    <div class="espacio-card">
                        <div class="espacio-header">
                            <h3>Laboratorio de Informática 1</h3>
                            <span class="estado disponible">Disponible</span>
                        </div>
                        <div class="espacio-info">
                            <p><i class="fas fa-users"></i> Capacidad: 30 personas</p>
                            <p><i class="fas fa-map-marker-alt"></i> Ubicación: Edificio A, Piso 2</p>
                            <div class="recursos-disponibles">
                                <span class="recurso"><i class="fas fa-desktop"></i> 30 PCs</span>
                                <span class="recurso"><i class="fas fa-projector"></i> Proyector</span>
                                <span class="recurso"><i class="fas fa-wifi"></i> WiFi</span>
                            </div>
                        </div>
                        <div class="horarios-disponibles">
                            <h4>Horarios Disponibles Hoy:</h4>
                            <div class="horario-slots">
                                <span class="horario-slot">08:00 - 10:00</span>
                                <span class="horario-slot">14:00 - 16:00</span>
                                <span class="horario-slot">16:00 - 18:00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Más tarjetas de espacios aquí -->
                </div>
            </div>

            <!-- Horarios de profesores -->
            <div class="profesores-container">
                <h2 class="section-title">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Horarios de Profesores
                </h2>

                <div class="profesores-lista">
                    <div class="profesor-card">
                        <div class="profesor-info">
                            <img src="../../Images/perfiles/perfilpordefecto.jpg" alt="Profesor" class="profesor-imagen">
                            <div class="profesor-detalles">
                                <h3>Prof. Juan Pérez</h3>
                                <p>Departamento de Matemáticas</p>
                            </div>
                        </div>
                        <div class="profesor-horarios">
                            <h4>Horarios de Consulta:</h4>
                            <ul>
                                <li>Lunes y Miércoles: 14:00 - 16:00</li>
                                <li>Viernes: 10:00 - 12:00</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>