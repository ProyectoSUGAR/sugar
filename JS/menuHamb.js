// Función para ajustar la posición del dropdown si es necesario
function ajustarPosicionDropdown() {
  const notificationsDropdown = document.getElementById("notificationsDropdown");
  const notificationsBtn = document.getElementById("notificationsBtn");
  if (!notificationsDropdown || !notificationsBtn) return;

  // Reset previous inline positioning so sizes are correct
  notificationsDropdown.style.left = '';
  notificationsDropdown.style.right = '';
  notificationsDropdown.style.top = '';
  notificationsDropdown.style.maxHeight = '';

  // Ensure the element is temporarily visible for measurement (without flashing)
  notificationsDropdown.style.visibility = 'hidden';
  notificationsDropdown.style.display = 'block';
  const dropdownRect = notificationsDropdown.getBoundingClientRect();
  const btnRect = notificationsBtn.getBoundingClientRect();
  const margin = 10; // margin from viewport edges

  // Preferred top: immediately below the button (viewport coordinates)
  let top = btnRect.bottom + 8; // 8px gap

  // Preferred left: center dropdown under the button
  let left = Math.round(btnRect.left + (btnRect.width / 2) - (dropdownRect.width / 2));

  // Clamp left within viewport margins
  const maxLeft = window.innerWidth - dropdownRect.width - margin;
  if (left < margin) {
    // If centering would go past the left edge, prefer aligning left edge with button left
    left = Math.max(margin, btnRect.left);
  }
  if (left > maxLeft) {
    // If centering would go past the right edge, prefer aligning right edge with button right
    left = Math.max(margin, Math.min(maxLeft, Math.round(btnRect.right - dropdownRect.width)));
  }

  // If dropdown would overflow bottom of viewport, try positioning above the button
  const fitsBelow = (top + dropdownRect.height) <= (window.innerHeight - margin);
  if (!fitsBelow) {
    const altTop = btnRect.top - dropdownRect.height - 8; // place above
    if (altTop >= margin) top = altTop;
    else {
      // Constrain height and keep below with maxHeight
      notificationsDropdown.style.maxHeight = `calc(100vh - ${margin * 2}px)`;
      top = Math.max(margin, btnRect.bottom + 8);
    }
  }

  // Apply fixed positioning relative to viewport
  notificationsDropdown.style.position = 'fixed';
  notificationsDropdown.style.top = top + 'px';
  notificationsDropdown.style.left = left + 'px';
  notificationsDropdown.style.right = 'auto';

  // Restore visibility and remove temporary display so CSS class controls visibility
  notificationsDropdown.style.visibility = '';
  // If the dropdown is shown (has class), let CSS handle display; otherwise clear inline display
  if (!notificationsDropdown.classList.contains('show')) {
    notificationsDropdown.style.display = '';
  } else {
    // remove temporary inline display so class .show controls it (but keep position/top/left)
    notificationsDropdown.style.display = '';
  }
}

// Función para inicializar todos los eventos
function inicializarMenu() {
  const toggleButton = document.getElementById("btnHamburguesa");
  const navWrapper = document.getElementById("nav");
  const notificationsBtn = document.getElementById("notificationsBtn");
  const notificationsDropdown = document.getElementById("notificationsDropdown");

  // Validar que todos los elementos existan
  if (!toggleButton || !navWrapper || !notificationsBtn || !notificationsDropdown) {
    console.warn("Advertencia: No se encontraron algunos elementos del menú");
    return;
  }

  

  // Evento del botón de hamburguesa
  toggleButton.addEventListener("click", () => {
    toggleButton.classList.toggle("close");
    navWrapper.classList.toggle("show");
    // Cerrar el dropdown de notificaciones si está abierto
    cerrarDropdown();
  });

  // Evento del nav wrapper
  navWrapper.addEventListener("click", e => {
    if (e.target.id === "nav") {
      navWrapper.classList.remove("show");
      toggleButton.classList.remove("close");
    }
  });

  // Toggle del dropdown de notificaciones
  notificationsBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    const isOpen = notificationsDropdown.classList.toggle("show");
    // Cerrar el menú hamburguesa si está abierto
    navWrapper.classList.remove("show");
    toggleButton.classList.remove("close");

    // Cargar notificaciones si el dropdown se abre
    if (isOpen) {
      cargarNotificaciones();
      // Ajustar posición si es necesario
      setTimeout(ajustarPosicionDropdown, 100);
    } else {
      // Si se ha cerrado, asegurar que quede oculto
      cerrarDropdown();
    }
  });

  // Evitar que clics dentro del dropdown cierren inmediatamente (propagación)
  notificationsDropdown.addEventListener('click', (e) => {
    e.stopPropagation();
  });

  // Cerrar dropdown cuando se hace clic fuera
  document.addEventListener("click", (e) => {
    if (!notificationsBtn.contains(e.target) && !notificationsDropdown.contains(e.target)) {
      cerrarDropdown();
    }
  });
}

// Función para cerrar y resetear el dropdown de notificaciones
function cerrarDropdown() {
  const notificationsDropdown = document.getElementById("notificationsDropdown");
  if (!notificationsDropdown) return;
  notificationsDropdown.classList.remove('show');
  // Limpiar estilos inline aplicados por posicionamiento
  notificationsDropdown.style.display = 'none';
  notificationsDropdown.style.left = '';
  notificationsDropdown.style.top = '';
  notificationsDropdown.style.right = '';
  notificationsDropdown.style.maxHeight = '';
  // mantener position fixed o resetear según preferencia
  // notificationsDropdown.style.position = '';
}

// Esperar a que el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", inicializarMenu);
} else {
  inicializarMenu();
}


// Función para cargar notificaciones
function cargarNotificaciones() {
  // Validar que las variables globales estén definidas
  if (typeof tipoUsuario === 'undefined' || typeof idUsuario === 'undefined') {
    console.error('Error: tipoUsuario o idUsuario no están definidos');
    const notificationsDropdown = document.getElementById("notificationsDropdown");
    if (notificationsDropdown) {
      notificationsDropdown.innerHTML = '<div class="notification-item"><p class="notification-message">Error: Usuario no autenticado.</p></div>';
    }
    return;
  }

  const notificationsDropdown = document.getElementById("notificationsDropdown");
  if (!notificationsDropdown) {
    console.error('Error: No se encontró el elemento notificationsDropdown');
    return;
  }

  // Mostrar estado de carga
  notificationsDropdown.innerHTML = '<div class="notification-item"><p class="notification-message">Cargando notificaciones...</p></div>';

  // URL dinámica según el tipo de usuario
  const rutaBase = document.location.pathname.includes('/Administrador/') ? '../../' :
                   document.location.pathname.includes('/Adscripta/') ? '../../' :
                   document.location.pathname.includes('/Profesor/') ? '../../' :
                   document.location.pathname.includes('/Direccion/') ? '../../' :
                   document.location.pathname.includes('/Estudiante/') ? '../../' :
                   document.location.pathname.includes('/Secretaria/') ? '../../' :
                   document.location.pathname.includes('/Funcionario/') ? '../../' : './';

  const urlNotificaciones = rutaBase + 'PHP/notificaciones_usuario.php?tipo_usuario=' + encodeURIComponent(tipoUsuario) + '&id_usuario=' + encodeURIComponent(idUsuario);

  fetch(urlNotificaciones)
    .then(response => {
      if (!response.ok) {
        throw new Error('Error en la respuesta del servidor: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      notificationsDropdown.innerHTML = ''; // Limpiar contenido anterior
      
      if (Array.isArray(data) && data.length > 0) {
        data.forEach(notification => {
          const item = document.createElement('div');
          item.className = 'notification-item';
          
          // Sanitizar el HTML
          const tipo = notification.tipo ? notification.tipo.replace(/</g, '&lt;').replace(/>/g, '&gt;') : 'info';
          const mensaje = notification.mensaje ? notification.mensaje.replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
          const fecha = notification.fecha ? notification.fecha.replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
          
          item.innerHTML = `
            <div class="notification-content">
              <strong class="notification-type">[${tipo}]</strong>
              <p class="notification-message">${mensaje}</p>
              <em class="notification-date">${fecha}</em>
            </div>
          `;
          notificationsDropdown.appendChild(item);
        });
      } else {
        const item = document.createElement('div');
        item.className = 'notification-item';
        item.innerHTML = '<p class="notification-message">No hay notificaciones nuevas.</p>';
        notificationsDropdown.appendChild(item);
      }
    })
    .catch(error => {
      console.error('Error al cargar notificaciones:', error);
      notificationsDropdown.innerHTML = '<div class="notification-item"><p class="notification-message">Error al cargar notificaciones. Por favor, intenta nuevamente.</p></div>';
    });
}

