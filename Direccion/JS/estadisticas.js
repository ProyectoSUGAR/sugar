// Llamado a los elementos del DOM
// DOMContentLoaded es un evento que se dispara cuando el documento HTML ha sido completamente cargado y parseado
document.addEventListener("DOMContentLoaded", function() {
    // Llamada a la API para obtener los datos estadísticos desde el backend PHP
<<<<<<< HEAD
    fetch('/Secretaria/PHP/datosEstadisticos.php')
=======
    fetch('../../Secretaria/PHP/datosEstadisticos.php')
>>>>>>> dff50c8 (Act)
        // Conversión de la respuesta a formato JSON
        .then(res => res.json())
        // Manejo de los datos recibidos
        .then(data => {
            // Asignación de los valores estadísticos a los elementos correspondientes en el DOM
<<<<<<< HEAD
            document.querySelector('.estadistica-numero[data-tipo="alumnos"]').textContent = data.alumnos;                     // Total de alumnos registrados
            document.querySelector('.estadistica-numero[data-tipo="profesores"]').textContent = data.profesores;               // Total de profesores registrados
            document.querySelector('.estadistica-numero[data-tipo="grupos"]').textContent = data.grupos;                       // Total de grupos registrados
            document.querySelector('.estadistica-numero[data-tipo="secretarios"]').textContent = data.secretarios;             // Total de secretarios registrados
            document.querySelector('.estadistica-numero[data-tipo="salones_libres"]').textContent = data.salones_libres;       // Total de salones libres actualmente
            document.querySelector('.estadistica-numero[data-tipo="profesores_presentes"]').textContent = data.profesores_presentes; // Total de profesores presentes en este momento

            // Obtención del contexto del canvas donde se renderizará el gráfico
            const ctx = document.getElementById('grafico-profesores');

            // Verificación de que el elemento canvas existe antes de crear el gráfico
            if (ctx) {
                // Creación de un gráfico de línea utilizando la librería Chart.js
                new Chart(ctx, {
                    type: 'line', // Tipo de gráfico: línea
                    // Datos que se mostrarán en el gráfico
                    data: {
                        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'], // Etiquetas del eje X (días de la semana)
                        datasets: [{
                            label: 'Ausencias',                         // Etiqueta del conjunto de datos
                            data: data.grafico,                         // Datos de ausencias por día
                            borderColor: '#2d89ef',                     // Color del borde de la línea
                            backgroundColor: 'rgba(45,137,239,0.1)',    // Color de fondo bajo la línea
                            fill: true,                                 // Relleno del área bajo la línea
                            tension: 0.3                                // Curvatura de la línea
                        }]
                    },
                    // Configuración del gráfico
                    options: {
                        plugins: {
                            legend: {
                                display: false // Oculta la leyenda del gráfico
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,              // El eje Y comienza en cero
                                ticks: {
                                    stepSize: 5                   // Intervalo entre marcas del eje Y
                                }
                            }
                        }
                    }
=======
            document.querySelector('.estadistica-numero[data-tipo="alumnos"]').textContent = data.alumnos;
            document.querySelector('.estadistica-numero[data-tipo="profesores"]').textContent = data.profesores;
            document.querySelector('.estadistica-numero[data-tipo="grupos"]').textContent = data.grupos;
            document.querySelector('.estadistica-numero[data-tipo="secretarios"]').textContent = data.secretarios;
            document.querySelector('.estadistica-numero[data-tipo="salones_libres"]').textContent = data.salones_libres;
            document.querySelector('.estadistica-numero[data-tipo="profesores_presentes"]').textContent = data.profesores_presentes;

            if (typeof anychart !== 'undefined') {
                anychart.onDocumentReady(function () {
                    var labels = ['Lun','Mar','Mié','Jue','Vie'];
                    var raw = data.grafico || [0,0,0,0,0];
                    var chartData = labels.map(function(l, i){ return [l, raw[i] || 0]; });

                    var chart = anychart.column();
                    var series = chart.column(chartData);
                    chart.title('Profesores que no han asistido esta semana');
                    chart.xAxis().title('Día');
                    chart.yAxis().title('Cantidad');
                    chart.container('container');
                    chart.draw();
>>>>>>> dff50c8 (Act)
                });
            }
        });
});