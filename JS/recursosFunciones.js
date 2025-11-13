// Gestión de Recursos - JavaScript Frontend
// Maneja AJAX para crear, leer, actualizar y eliminar recursos

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar
    cargarRecursos();
    configurarEventos();
});

/**
 * Configura los event listeners del formulario y búsqueda
 */
function configurarEventos() {
    const form = document.getElementById('recursoForm');
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const imagenInput = document.getElementById('imagen');

    // Envío del formulario
    form.addEventListener('submit', manejarFormulario);

    // Búsqueda en tiempo real
    searchInput?.addEventListener('input', filtrarRecursos);

    // Filtro por tipo
    filterType?.addEventListener('change', filtrarRecursos);

    // Vista previa de imagen
    imagenInput?.addEventListener('change', mostrarVistaPrevia);
}

/**
 * Muestra vista previa de la imagen seleccionada
 */
function mostrarVistaPrevia(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.innerHTML = `<img src="${event.target.result}" alt="Vista previa">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

/**
 * Maneja el envío del formulario
 */
async function manejarFormulario(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action', 'crear');

    try {
        const response = await fetch('../../Secretaria/PHP/funcAsignRec.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.éxito) {
            mostrarNotificacion('Recurso guardado correctamente', 'éxito');
            document.getElementById('recursoForm').reset();
            document.getElementById('preview').innerHTML = '';
            cargarRecursos();
        } else {
            mostrarNotificacion(result.mensaje || 'Error al guardar el recurso', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión: ' + error.message, 'error');
    }
}

/**
 * Carga y muestra los recursos disponibles
 */
async function cargarRecursos() {
    try {
        const response = await fetch('../../Secretaria/PHP/funcAsignRec.php?action=obtener');
        const result = await response.json();

        if (result.éxito) {
            mostrarRecursos(result.datos);
        } else {
            console.error('Error al cargar recursos:', result.mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

/**
 * Muestra los recursos en el listado
 */
function mostrarRecursos(recursos) {
    const contenedor = document.getElementById('recursosList');
    
    if (!recursos || recursos.length === 0) {
        contenedor.innerHTML = '<p class="no-recursos">No hay recursos registrados</p>';
        return;
    }

    contenedor.innerHTML = recursos.map(recurso => `
        <div class="recurso-card" data-id="${recurso.id}" data-tipo="${recurso.tipo}">
            <div class="recurso-image">
                ${recurso.imagen ? `<img src="${recurso.imagen}" alt="${recurso.nombre}">` : '<i class="fas fa-image"></i>'}
            </div>
            <div class="recurso-content">
                <h3>${recurso.nombre}</h3>
                <span class="recurso-tipo">${recurso.tipo}</span>
                <p>${recurso.descripcion}</p>
                <div class="recurso-actions">
                    <button class="btn-edit" onclick="abrirEdicion(${recurso.id})">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-delete" onclick="confirmarEliminacion(${recurso.id})">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Filtra los recursos según búsqueda y tipo
 */
function filtrarRecursos() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const typeFilter = document.getElementById('filterType').value;
    const cards = document.querySelectorAll('.recurso-card');

    cards.forEach(card => {
        const nombre = card.querySelector('h3').textContent.toLowerCase();
        const tipo = card.dataset.tipo;

        const coincideNombre = nombre.includes(searchTerm);
        const coincideTipo = !typeFilter || tipo === typeFilter;

        card.style.display = (coincideNombre && coincideTipo) ? 'block' : 'none';
    });
}

/**
 * Abre el formulario para edición de recurso
 */
async function abrirEdicion(id) {
    try {
        const response = await fetch(`../../Secretaria/PHP/funcAsignRec.php?action=obtener&id=${id}`);
        const result = await response.json();

        if (result.éxito && result.datos) {
            const recurso = result.datos[0];
            
            document.getElementById('nombre').value = recurso.nombre;
            document.getElementById('tipo').value = recurso.tipo;
            document.getElementById('descripcion').value = recurso.descripcion;

            // Mostrar imagen actual
            if (recurso.imagen) {
                document.getElementById('preview').innerHTML = `<img src="${recurso.imagen}" alt="${recurso.nombre}">`;
            }

            // Cambiar botón y acción del formulario
            const form = document.getElementById('recursoForm');
            form.action = `../../Secretaria/PHP/funcAsignRec.php?action=actualizar&id=${id}`;
            
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Actualizar';
            submitBtn.name = 'action';
            submitBtn.value = 'actualizar';

            // Scroll al formulario
            document.querySelector('.recursos-form-container').scrollIntoView({ behavior: 'smooth' });
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error al cargar el recurso', 'error');
    }
}

/**
 * Confirma la eliminación de un recurso
 */
function confirmarEliminacion(id) {
    if (confirm('¿Está seguro de que desea eliminar este recurso?')) {
        eliminarRecurso(id);
    }
}

/**
 * Elimina un recurso
 */
async function eliminarRecurso(id) {
    try {
        const response = await fetch('../../Secretaria/PHP/funcAsignRec.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=eliminar&id=${id}`
        });

        const result = await response.json();

        if (result.éxito) {
            mostrarNotificacion('Recurso eliminado correctamente', 'éxito');
            cargarRecursos();
        } else {
            mostrarNotificacion(result.mensaje || 'Error al eliminar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    }
}

/**
 * Muestra una notificación temporal
 */
function mostrarNotificacion(mensaje, tipo = 'info') {
    const div = document.createElement('div');
    div.className = `notificacion notificacion-${tipo}`;
    div.innerHTML = `
        <i class="fas fa-${tipo === 'éxito' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${mensaje}
    `;

    document.body.appendChild(div);

    setTimeout(() => div.remove(), 3000);
}
