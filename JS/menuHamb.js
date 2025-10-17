const toggleButton = document.getElementById("btnHamburguesa");
const navWrapper = document.getElementById("nav");

/* Abrir / cerrar menú */
toggleButton.addEventListener("click", () => {
  toggleButton.classList.toggle("close");
  navWrapper.classList.toggle("show");
});

/* Mantiene tu comportamiento anterior: clic en el fondo del nav */
navWrapper.addEventListener("click", e => {
  if (e.target.id === "nav") { // clic en la parte vacía del nav
    navWrapper.classList.remove("show");
    toggleButton.classList.remove("close");
  }
});
