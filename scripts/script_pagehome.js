// Esperar a que el usuario haga scroll
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    
    // Si el usuario baja más de 50 pixeles
    if (window.scrollY > 50) {
        header.style.padding = "0.2rem 0"; // Encoge un poco la barra
        header.style.backgroundColor = "#07070c"; // Se vuelve más negra
        header.style.boxShadow = "0 5px 20px rgba(106, 13, 173, 0.5)"; // Resalta el neón
    } else {
        header.style.padding = "0.5rem 0"; // Vuelve a su tamaño original
        header.style.backgroundColor = "#1a1a2e"; // Vuelve al color original
        header.style.boxShadow = "0 4px 15px rgba(106, 13, 173, 0.3)";
    }
});
