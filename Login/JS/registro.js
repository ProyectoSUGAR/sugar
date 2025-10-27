
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formulario-registro") || document.querySelector('form');
  const contenedor = document.getElementById("alertaContenedor") || null;
  function mostrarMensaje(texto) {
    if (!contenedor) {
      Swal.fire({ icon: 'error', title: 'Error', text: texto });
      return;
    }
    const alerta = document.createElement("p");
    alerta.textContent = texto;
    alerta.style.background = "#E3C39D";       // Color de fondo del mensaje
    alerta.style.color = "#071739";            // Color del texto
    alerta.style.padding = "10px";             // Espaciado interno
    alerta.style.borderRadius = "6px";         // Bordes redondeados
    alerta.style.marginTop = "10px";           // Separación superior
    alerta.style.fontWeight = "bold";          // Texto en negrita
    contenedor.appendChild(alerta);            // Agrega el mensaje al contenedor
    setTimeout(function () {
      if (contenedor.contains(alerta)) contenedor.removeChild(alerta);
    }, 5000);
  }
  function contarVocales(nombre) {
    let cantL = 0;
    for (let i = 0; i < nombre.length; i++) {
      if ("aeiouAEIOU".includes(nombre[i])) {
        cantL++;
      }
    }
    return cantL;
  }
  function validarPassword(pass) {
    if (!(pass.length >= 6 && /[A-Z]/.test(pass) && /[a-z]/.test(pass) && /[0-9]/.test(pass))) {
      mostrarMensaje("La contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un número");
      return false;
    }
    return true;
  }
  function obtenerValoresFormulario() {
    return {
      nombre: (document.getElementById("nombre") || {}).value ? document.getElementById("nombre").value.trim() : '',
      apellido: (document.getElementById("apellido") || {}).value ? document.getElementById("apellido").value.trim() : '',
      cedula: (document.getElementById("cedula") || {}).value ? document.getElementById("cedula").value.trim() : '',
      password: (document.getElementById("password") || {}).value || '',
      confirmar: (document.getElementById("confirmaPassword") || {}).value || '',
      fecha_nacimiento: (document.getElementById("fecha_nacimiento") || {}).value || '',
      tipoUsuario: (document.getElementById("tipo_usuario") || {}).value || ''
    };
  }
  function validarNombre(nombre) {
    let letras = nombre.match(/[a-zA-Z]/g);
    if (!letras || letras.length < 3) {
      mostrarMensaje("El nombre debe tener al menos 3 letras");
      return false;
    }
    return true;
  }
  function validarApellido(apellido) {
    let letrasApellido = apellido.match(/[a-zA-Z]/g);
    if (!letrasApellido || letrasApellido.length < 3) {
      mostrarMensaje("El apellido debe tener al menos 3 letras");
      return false;
    }
    return true;
  }
  function validarCedula(cedula) {
    if (!/^[0-9]{7,8}$/.test(cedula)) {
      mostrarMensaje("La cédula debe contener solo números y tener entre 7 y 8 dígitos");
      return false;
    }
    return true;
  }
  function validarEdad(edad) {
    if (edad === "" || Number(edad) <= 15) {
      mostrarMensaje("La edad debe ser mayor que 15");
      return false;
    }
    return true;
  }
  function validarTipoUsuario(tipoUsuario) {
    if (tipoUsuario === "") {
      mostrarMensaje("Debes seleccionar un tipo de usuario");
      return false;
    }
    return true;
  }
  function validarConfirmarPassword(password, confirmar) {
    if (password !== confirmar) {
      mostrarMensaje("Las contraseñas son diferentes");
      return false;
    }
    return true;
  }
  function mostrarExito() {
    Swal.fire({
      icon: "success",
      title: "Éxito",
      text: "Formulario enviado correctamente",
      timer: 1500,
      showConfirmButton: false
    });
  }
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
  if (!form) return;
  form.addEventListener("submit", function (e) {
    const datos = obtenerValoresFormulario();
    if (!validarFormulario(datos)) {
      e.preventDefault(); // Bloquea el envío si hay errores
    } else {
      e.preventDefault(); // Previene el envío inmediato
      mostrarExito();     // Muestra mensaje de éxito
      setTimeout(() => form.submit(), 1500); // Envía el formulario luego de 1.5 segundos
    }
  });
});
