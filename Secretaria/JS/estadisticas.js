// Evento que se ejecuta cuando el contenido del DOM ha sido completamente cargado

document.addEventListener("DOMContentLoaded", function() {
    // Solicitud HTTP para obtener los datos estadísticos desde el archivo PHP
    fetch('/Secretaria/PHP/datosEstadisticos.php')
        // Conversión de la respuesta a formato JSON
        .then(res => res.json())
        // Manejo de los datos recibidos
        .then(data => {
            // Asignación del número total de alumnos al elemento correspondiente
            document.querySelector('.estadistica-numero[data-tipo="alumnos"]').textContent = data.alumnos;

            // Asignación del número total de profesores al elemento correspondiente
            document.querySelector('.estadistica-numero[data-tipo="profesores"]').textContent = data.profesores;

            // Asignación del número total de grupos al elemento correspondiente
            document.querySelector('.estadistica-numero[data-tipo="grupos"]').textContent = data.grupos;

            // Asignación del número total de secretarios al elemento correspondiente
            document.querySelector('.estadistica-numero[data-tipo="secretarios"]').textContent = data.secretarios;

            // Asignación del número total de salones libres al elemento correspondiente
            document.querySelector('.estadistica-numero[data-tipo="salones_libres"]').textContent = data.salones_libres;

            // Asignación del número total de profesores presentes al elemento correspondiente
            document.querySelector('.estadistica-numero[data-tipo="profesores_presentes"]').textContent = data.profesores_presentes;

            // Obtención del contexto del canvas donde se mostrará el gráfico
            const ctx = document.getElementById('grafico-profesores');

            // Verificación de que el elemento canvas existe antes de crear el gráfico
            if (ctx) {
                // Creación de un gráfico de línea utilizando la librería Chart.js
                new Chart(ctx, {
                    type: 'line', // Tipo de gráfico: línea
                    data: {
                        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'], // Etiquetas del eje X (días de la semana)
                        datasets: [{
                            label: 'Ausencias', // Etiqueta del conjunto de datos
                            data: data.grafico, // Datos de ausencias por día
                            borderColor: '#2d89ef', // Color del borde de la línea
                            backgroundColor: 'rgba(45,137,239,0.1)', // Color de fondo bajo la línea
                            fill: true, // Relleno del área bajo la línea
                            tension: 0.3 // Curvatura de la línea
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false // Oculta la leyenda del gráfico
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true, // El eje Y comienza en cero
                                ticks: {
                                    stepSize: 5 // Intervalo entre marcas del eje Y
                                }
                            }
                        }
                    }
                });
            }
        });
});