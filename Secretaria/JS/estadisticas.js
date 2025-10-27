
document.addEventListener("DOMContentLoaded", function() {
    console.log('Iniciando carga de estadísticas...');
    if (typeof anychart === 'undefined') {
        console.error('AnyChart no está cargado. Por favor, verifica las dependencias.');
        return;
    }
    fetch('../PHP/datosEstadisticos.php')
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Datos recibidos del servidor:', data);
            document.querySelector('.estadistica-numero[data-tipo="alumnos"]').textContent = data.alumnos || '0';
            document.querySelector('.estadistica-numero[data-tipo="profesores"]').textContent = data.profesores || '0';
            document.querySelector('.estadistica-numero[data-tipo="grupos"]').textContent = data.grupos || '0';
            document.querySelector('.estadistica-numero[data-tipo="secretarios"]').textContent = data.secretarios || '0';
            document.querySelector('.estadistica-numero[data-tipo="salones_libres"]').textContent = data.salones_libres || '0';
            try {
                var chart = anychart.column();
                var datosManana = [];
                var datosTarde = [];
                var datosNoche = [];
                if (data.grafico && Array.isArray(data.grafico)) {
                    data.grafico.forEach(function(item) {
                        if (item.turno === 'Mañana') {
                            datosManana.push({x: item.dia, value: item.clases});
                        } else if (item.turno === 'Tarde') {
                            datosTarde.push({x: item.dia, value: item.clases});
                        } else if (item.turno === 'Noche') {
                            datosNoche.push({x: item.dia, value: item.clases});
                        }
                    });
                }
                var serieManana = chart.column(datosManana);
                var serieTarde = chart.column(datosTarde);
                var serieNoche = chart.column(datosNoche);
                serieManana.name('Mañana').fill('#FFD700', 0.7);  // Dorado
                serieTarde.name('Tarde').fill('#4CAF50', 0.7);    // Verde
                serieNoche.name('Noche').fill('#2196F3', 0.7);    // Azul
                chart.title({
                    text: 'Distribución de Clases por Turno y Día',
                    fontSize: 18,
                    fontWeight: 'bold',
                    padding: {bottom: 20}
                });
                chart.xAxis().title({
                    text: 'Días de la Semana',
                    fontSize: 14
                });
                chart.yAxis().title({
                    text: 'Cantidad de Clases',
                    fontSize: 14
                });
                chart.legend(true);
                chart.legend().position('top');
                chart.legend().itemsLayout('horizontal');
                chart.legend().align('center');
                chart.legend().padding({top: 0, bottom: 10});
                chart.legend().fontSize(12);
                chart.tooltip()
                    .titleFormat('{%x}')
                    .format('Turno: {%seriesName}\nClases: {%value}')
                    .fontSize(12);
                var container = document.getElementById('container');
                if (container) {
                    chart.container(container);
                    chart.draw();
                    console.log('Gráfico dibujado correctamente');
                } else {
                    console.error('No se encontró el contenedor del gráfico');
                }
            } catch (error) {
                console.error('Error al crear el gráfico:', error);
            }
        })
        .catch(error => {
            console.error('Error al obtener datos:', error);
        });
});