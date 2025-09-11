// Espera a que todo el contenido HTML se haya cargado completamente
document.addEventListener("DOMContentLoaded", () => {

  // Obtiene el formulario por su ID
  const form = document.getElementById("formulario");

  // Obtiene el contenedor donde se mostrarán los mensajes de alerta
  const contenedor = document.getElementById("alertaContenedor");

  // Función que crea y muestra un mensaje de alerta visual en pantalla
  function mostrarMensaje(texto) {
    const alerta = document.createElement("p");
    alerta.textContent = texto;
    alerta.style.background = "#E3C39D";       // Color de fondo del mensaje
    alerta.style.color = "#071739";            // Color del texto
    alerta.style.padding = "10px";             // Espaciado interno
    alerta.style.borderRadius = "6px";         // Bordes redondeados
    alerta.style.marginTop = "10px";           // Separación superior
    alerta.style.fontWeight = "bold";          // Texto en negrita
    contenedor.appendChild(alerta);            // Agrega el mensaje al contenedor

    // Elimina el mensaje automáticamente después de 5 segundos
    setTimeout(function () {
      contenedor.removeChild(alerta);
    }, 5000);
  }

  // Función que cuenta la cantidad de vocales en el nombre ingresado
  function contarVocales(nombre) {
    let cantL = 0;
    for (let i = 0; i < nombre.length; i++) {
      if ("aeiouAEIOU".includes(nombre[i])) {
        cantL++;
      }
    }
    return cantL;
  }

  // Función que valida si la contraseña cumple con los requisitos mínimos de seguridad
  function validarPassword(pass) {
    if (!(pass.length >= 6 && /[A-Z]/.test(pass) && /[a-z]/.test(pass) && /[0-9]/.test(pass))) {
      mostrarMensaje("La contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un número");
      return false;
    }
    return true;
  }

  // Función que obtiene y limpia los valores ingresados en el formulario
  function obtenerValoresFormulario() {
    return {
      nombre: document.getElementById("nombre").value.trim(),
      apellido: document.getElementById("apellido").value.trim(),
      cedula: document.getElementById("cedula").value.trim(),
      password: document.getElementById("password").value,
      confirmar: document.getElementById("confirmaPassword").value,
      fecha_nacimiento: document.getElementById("fecha_nacimiento").value,
      tipoUsuario: document.getElementById("tipoUsuario").value
    };
  }

  // Función que valida que el nombre tenga al menos 3 letras
  function validarNombre(nombre) {
    let letras = nombre.match(/[a-zA-Z]/g);
    if (!letras || letras.length < 3) {
      mostrarMensaje("El nombre debe tener al menos 3 letras");
      return false;
    }
    return true;
  }

  // Función que valida que el apellido tenga al menos 3 letras
  function validarApellido(apellido) {
    let letrasApellido = apellido.match(/[a-zA-Z]/g);
    if (!letrasApellido || letrasApellido.length < 3) {
      mostrarMensaje("El apellido debe tener al menos 3 letras");
      return false;
    }
    return true;
  }

  // Función que valida que la cédula tenga exactamente 8 dígitos numéricos
  function validarCedula(cedula) {
    if (cedula.length !== 8 || isNaN(cedula)) {
      mostrarMensaje("La cédula debe contener exactamente 8 números");
      return false;
    }
    return true;
  }

  // Función que valida que la edad sea mayor a 15 años
  function validarEdad(edad) {
    if (edad === "" || Number(edad) <= 15) {
      mostrarMensaje("La edad debe ser mayor que 15");
      return false;
    }
    return true;
  }

  // Función que valida que se haya seleccionado un tipo de usuario
  function validarTipoUsuario(tipoUsuario) {
    if (tipoUsuario === "") {
      mostrarMensaje("Debes seleccionar un tipo de usuario");
      return false;
    }
    return true;
  }

  // Función que valida que las contraseñas ingresadas coincidan
  function validarConfirmarPassword(password, confirmar) {
    if (password !== confirmar) {
      mostrarMensaje("Las contraseñas son diferentes");
      return false;
    }
    return true;
  }

  // Función que muestra un mensaje de éxito utilizando SweetAlert
  function mostrarExito() {
    Swal.fire({
      icon: "success",
      title: "Éxito",
      text: "Formulario enviado correctamente",
      timer: 1500,
      showConfirmButton: false
    });
  }

  // Función principal que valida todos los campos del formulario
  function validarFormulario(datos) {
    if (!validarNombre(datos.nombre)) return false;
    if (!validarApellido(datos.apellido)) return false;
    if (!validarCedula(datos.cedula)) return false;
    if (!validarPassword(datos.password)) return false;
    if (!validarConfirmarPassword(datos.password, datos.confirmar)) return false;
    if (!validarEdad(datos.edad)) return false;
    if (!validarTipoUsuario(datos.tipoUsuario)) return false;

    return true;
  }

  // Evento que se ejecuta al enviar el formulario
  form.addEventListener("submit", function (e) {
    // Obtiene los valores del formulario
    const datos = obtenerValoresFormulario();

    // Valida todos los campos antes de enviar
    if (!validarFormulario(datos)) {
      e.preventDefault(); // Bloquea el envío si hay errores
    } else {
      e.preventDefault(); // Previene el envío inmediato
      mostrarExito();     // Muestra mensaje de éxito
      setTimeout(() => form.submit(), 1500); // Envía el formulario luego de 1.5 segundos
    }
  });
});
