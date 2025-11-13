<?php
include '../../HEADERS/headerA.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <style>
        .reservas-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        .reserva-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .reserva-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .reserva-info {
            flex: 1;
        }
        .reserva-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .reserva-info strong {
            color: #333;
        }
        .reserva-actions {
            display: flex;
            gap: 10px;
        }
        .btn-aprobar {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-aprobar:hover {
            background: #45a049;
        }
        .btn-rechazar {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-rechazar:hover {
            background: #da190b;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: Arial;
            resize: vertical;
            min-height: 80px;
        }
        .btn-confirm {
            background: #2196F3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .btn-confirm:hover {
            background: #0b7dda;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <main>
        <div class="reservas-container">
            <h1>Gestión de Reservas Pendientes</h1>
            <div id="reservas-list"></div>
        </div>
    </main>

    <!-- Modal para aprobar -->
    <div id="modalAprobar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modalAprobar')">&times;</span>
            <h3>Aprobar Reserva</h3>
            <div class="form-group">
                <label for="notasAprobar">Notas (opcional):</label>
                <textarea id="notasAprobar"></textarea>
            </div>
            <button class="btn-confirm" onclick="confirmarAprobar()">Aprobar</button>
        </div>
    </div>

    <!-- Modal para rechazar -->
    <div id="modalRechazar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modalRechazar')">&times;</span>
            <h3>Rechazar Reserva</h3>
            <div class="form-group">
                <label for="motivoRechazo">Motivo del rechazo:</label>
                <textarea id="motivoRechazo" required></textarea>
            </div>
            <button class="btn-confirm" onclick="confirmarRechazar()">Rechazar</button>
        </div>
    </div>

    <script>
        let currentReservaId = null;

        function cargarReservas() {
            fetch('../PHP/gestionReservas.php?accion=obtenerPendientes', { credentials: 'same-origin' })
                        .then(res => {
                            console.log('gestionReservas response status:', res.status);
                            return res.text().then(text => {
                                console.log('Raw response text:', text);
                                try {
                                    return JSON.parse(text);
                                } catch (e) {
                                    return { __parseError: true, rawText: text };
                                }
                            });
                        })
                        .then(data => {
                            console.log('gestionReservas parsed data:', data);
                            const container = document.getElementById('reservas-list');
                            if (!data) {
                                container.innerHTML = '<div class="empty-state">Respuesta vacía del servidor</div>';
                                return;
                            }
                            if (data.__parseError) {
                                container.innerHTML = `<div class="empty-state">Respuesta inválida del servidor. Revisa la consola (texto crudo).</div>`;
                                return;
                            }
                            // Manejar errores del backend
                            if (data.error) {
                                container.innerHTML = `<div class="empty-state">Error: ${data.error}</div>`;
                                return;
                            }
                            if (!Array.isArray(data)) {
                                // Si el backend devuelve un objeto con success/message
                                if (data.success === false && data.message) {
                                    container.innerHTML = `<div class="empty-state">${data.message}</div>`;
                                    return;
                                }
                                // Fallback
                                container.innerHTML = '<div class="empty-state">Formato inesperado de respuesta</div>';
                                return;
                            }

                            if (data.length === 0) {
                                container.innerHTML = '<div class="empty-state">No hay reservas pendientes</div>';
                                return;
                            }

                            container.innerHTML = data.map(r => `
                                <div class="reserva-card">
                                    <div class="reserva-header">
                                        <div class="reserva-info">
                                            <p><strong>Profesor:</strong> ${r.nombre} ${r.apellido}</p>
                                            <p><strong>Espacio:</strong> ${r.espacio_nombre}</p>
                                            <p><strong>Fecha:</strong> ${r.fecha_reserva} | <strong>Hora:</strong> ${r.fecha_inicio} - ${r.fecha_fin}</p>
                                            <p><strong>Solicitado:</strong> ${r.fecha_creacion ? new Date(r.fecha_creacion).toLocaleString('es-ES') : ''}</p>
                                        </div>
                                        <div class="reserva-actions">
                                            <button class="btn-aprobar" onclick="abrirAprobar(${r.id_reserva})">Aprobar</button>
                                            <button class="btn-rechazar" onclick="abrirRechazar(${r.id_reserva})">Rechazar</button>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                        })
                        .catch(err => {
                            console.error('Error al cargar reservas pendientes:', err);
                            const container = document.getElementById('reservas-list');
                            container.innerHTML = '<div class="empty-state">Error al conectar con el servidor</div>';
                        });
        }

        function abrirAprobar(idReserva) {
            currentReservaId = idReserva;
            document.getElementById('notasAprobar').value = '';
            document.getElementById('modalAprobar').style.display = 'block';
        }

        function abrirRechazar(idReserva) {
            currentReservaId = idReserva;
            document.getElementById('motivoRechazo').value = '';
            document.getElementById('modalRechazar').style.display = 'block';
        }

        function confirmarAprobar() {
            const notas = document.getElementById('notasAprobar').value;
            const formData = new FormData();
            formData.append('accion', 'aprobar');
            formData.append('id_reserva', currentReservaId);
            formData.append('notas', notas);
            console.log('Enviar aprobar', currentReservaId, notas);
            fetch('../PHP/gestionReservas.php', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(res => {
                console.log('aprobar response status:', res.status);
                return res.text().then(text => {
                    console.log('aprobar raw text:', text);
                    try { return JSON.parse(text); } catch(e) { return { __parseError: true, rawText: text }; }
                });
            })
            .then(data => {
                console.log('aprobar parsed:', data);
                if (!data || data.__parseError) {
                    alert('Respuesta inesperada del servidor al aprobar. Revisa la consola.');
                    return;
                }
                if (data.success) {
                    alert('Reserva aprobada correctamente');
                    cerrarModal('modalAprobar');
                    cargarReservas();
                } else {
                    alert('Error: ' + (data.message || data.error || 'Respuesta inválida'));
                }
            })
            .catch(err => { console.error('Error al aprobar:', err); alert('Error al comunicarse con el servidor'); });
        }

        function confirmarRechazar() {
            const motivo = document.getElementById('motivoRechazo').value;
            if (!motivo.trim()) {
                alert('Debe indicar el motivo del rechazo');
                return;
            }

            const formData = new FormData();
            formData.append('accion', 'rechazar');
            formData.append('id_reserva', currentReservaId);
            formData.append('motivo', motivo);
            console.log('Enviar rechazar', currentReservaId, motivo);
            fetch('../PHP/gestionReservas.php', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(res => {
                console.log('rechazar response status:', res.status);
                return res.text().then(text => {
                    console.log('rechazar raw text:', text);
                    try { return JSON.parse(text); } catch(e) { return { __parseError: true, rawText: text }; }
                });
            })
            .then(data => {
                console.log('rechazar parsed:', data);
                if (!data || data.__parseError) {
                    alert('Respuesta inesperada del servidor al rechazar. Revisa la consola.');
                    return;
                }
                if (data.success) {
                    alert('Reserva rechazada correctamente');
                    cerrarModal('modalRechazar');
                    cargarReservas();
                } else {
                    alert('Error: ' + (data.message || data.error || 'Respuesta inválida'));
                }
            })
            .catch(err => { console.error('Error al rechazar:', err); alert('Error al comunicarse con el servidor'); });
        }

        function cerrarModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = event.target;
            if (modal.classList.contains('modal')) {
                modal.style.display = 'none';
            }
        }

        // Cargar reservas al abrir
        cargarReservas();
        // Recargar cada 30 segundos
        setInterval(cargarReservas, 30000);
    </script>
</body>
</html>
