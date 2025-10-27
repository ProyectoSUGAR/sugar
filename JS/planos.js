
document.addEventListener('DOMContentLoaded', function() {
    const planos = [
        { src: "../../Images/placeholder.png", alt: "Plano Planta baja" },
        { src: "../../Images/placeholder.png", alt: "Plano Piso 1" },
        { src: "../../Images/placeholder.png", alt: "Plano Piso 2" }
    ];
    const btns = document.querySelectorAll('.btn-piso');
    const imgPlano = document.getElementById('imagen-plano');
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            btns.forEach(b => b.classList.remove('activo'));
            btn.classList.add('activo');
            const piso = parseInt(btn.getAttribute('data-piso'));
            imgPlano.src = planos[piso].src;
            imgPlano.alt = planos[piso].alt;
        });
    });
});
