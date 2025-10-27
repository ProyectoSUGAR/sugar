const errores = {
  "db_connection_pdo": "Error de conexión PDO: ",
  "db_connection": "Error de conexión: ",
  "reserva_fallida": "Error al realizar la reserva.",
  "registro_usuario_fallido": "Error al registrar usuario.",
  "subida_imagen_fallida": "Error al subir la imagen.",
  "horario_registro_fallido": "Error al registrar el horario: ",
  "horario_actualizacion_fallida": "Error al actualizar el horario: ",
  "nombre_invalido": "Nombre inválido (solo letras 1-3).",
  "anio_invalido": "Año inválido (1-6).",
  "horas_invalidas": "Horas inválidas (1-40).",
  "consulta_preparacion_fallida": "Error en la preparación de la consulta: ",
  "guardar_grupo_fallido": "Error al guardar el grupo: ",
  "notificacion_creacion_fallida": "Error al crear la notificación: ",
  "asignatura_registro_fallido": "Error al registrar la asignatura.",
  "asignatura_edicion_fallida": "Error al editar la asignatura.",
  "metodo_no_permitido": "Método no permitido.",
  "login_contrasena_incorrecta": "Contraseña incorrecta.",
  "login_usuario_no_encontrado": "Usuario no encontrado.",
  "login_tipo_no_definido": "Tipo de usuario no definido. Contacte al administrador.",
  "correo_actualizacion_fallida": "Error: Al actualizar el correo.",
  "correo_formato_invalido": "Error: Formato de correo inválido.",
  "contrasena_actualizacion_fallida": "Error: Al actualizar la contraseña.",
  "archivo_tipo_no_permitido": "Error: Tipo de archivo no permitido.",
  "archivo_demasiado_grande": "Error: El archivo es demasiado grande (máx 2MB).",
  "foto_perfil_actualizacion_fallida": "Error: No se pudo actualizar la foto de perfil.",
  "imagen_subida_fallida": "Error: Al subir la imagen.",
  "asignacion_realizada": "Asignación realizada correctamente."
};

function mostrarError(clave, adicional = "") {
  const mensaje = errores[clave] ? errores[clave] + adicional : "Error desconocido.";
  alert(mensaje);
}

function mostrarErrorYRedirigir(clave, url, adicional = "") {
  const mensaje = errores[clave] ? errores[clave] + adicional : "Error desconocido.";
  alert(mensaje);
  window.location.href = url;
}

function mostrarErrorSwal(clave, adicional = "") {
  const mensaje = errores[clave] ? errores[clave] + adicional : "Error desconocido.";
  Swal.fire({
    icon: 'error',
    title: 'Error',
    text: mensaje,
    confirmButtonText: 'Ok'
  });
}
