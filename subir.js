document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", (e) => {
            const nombre = document.querySelector("input[name='nombre']").value.trim();
            const artista = document.querySelector("input[name='artista']").value.trim();
            const audio = document.querySelector("input[name='cancion']").files[0];
            const imagen = document.querySelector("input[name='imagen']").files[0];
            
            if (!nombre || !artista) {
                e.preventDefault();
                alert("Por favor, completa el nombre y artista");
            } else if (!audio) {
                e.preventDefault();
                alert("Por favor, selecciona un archivo de audio");
            } else if (!imagen) {
                e.preventDefault();
                alert("Por favor, selecciona una imagen de portada");
            }
        });
    }
});