document.addEventListener("DOMContentLoaded", function () {

    const botonAgregar = document.querySelector(".btn-agregar");
    const modal = document.getElementById("modalMedicamento");
    const botonCerrar = document.getElementById("cerrarModal");

    console.log("JavaScript funcionando");

    if (!botonAgregar || !modal || !botonCerrar) {
        console.error("No se encontraron los elementos del formulario");
        return;
    }

    botonAgregar.addEventListener("click", function () {
        modal.style.display = "flex";
    });

    botonCerrar.addEventListener("click", function () {
        modal.style.display = "none";
    });

    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

});