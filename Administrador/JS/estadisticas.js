// Llamado al cargar el DOM
// DOMContentLoaded se dispara cuando el documento HTML ha sido completamente cargado y parseado
document.addEventListener("DOMContentLoaded", function() {

  // Llamada al que devuelve los datos estadísticos en formato JSON
  fetch('/Secretaria/PHP/datosEstadisticos.php')

    // Procesa la respuesta y la convierte en objeto JavaScript
    .then(res => res.json())

    // Maneja los datos recibidos
    .then(data => {

      // Asigna los valores estadísticos a los elementos correspondientes del DOM
      document.querySelector('.estadistica-numero[data-tipo="alumnos"]').textContent = data.alumnos;                     // Total de alumnos registrados
      document.querySelector('.estadistica-numero[data-tipo="profesores"]').textContent = data.profesores;               // Total de profesores registrados
      document.querySelector('.estadistica-numero[data-tipo="grupos"]').textContent = data.grupos;                       // Total de grupos registrados
      document.querySelector('.estadistica-numero[data-tipo="secretarios"]').textContent = data.secretarios;             // Total de secretarios registrados
      document.querySelector('.estadistica-numero[data-tipo="salones_libres"]').textContent = data.salones_libres;       // Total de salones libres actualmente
      document.querySelector('.estadistica-numero[data-tipo="profesores_presentes"]').textContent = data.profesores_presentes; // Total de profesores presentes en este momento

      // Obtiene el contexto del canvas donde se renderizará el gráfico
      const ctx = document.getElementById('grafico-profesores');

      // Verifica que el canvas exista antes de crear el gráfico
      if (ctx) {

        // Crea un gráfico de línea utilizando la librería Chart.js
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
        });
      }
    });
});
