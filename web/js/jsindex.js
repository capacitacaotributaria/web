// Função para iniciar o carrossel
// depoimentos
const carousel = document.querySelector(".testimonial-carousel");
const testimonials = document.querySelectorAll(".testimonial");
let scrollAmount = 0;
let interval;

// Função para iniciar o carrossel
function startCarousel() {
  interval = setInterval(() => {
    scrollAmount += 1; // Move 1px por intervalo
    if (scrollAmount >= carousel.scrollWidth - carousel.clientWidth) {
      scrollAmount = 0; // Reinicia o carrossel ao final
    }
    carousel.scrollLeft = scrollAmount;
  }, 20); // Ajuste do intervalo para velocidade
}

// Pausa o carrossel ao passar o mouse em um depoimento
testimonials.forEach((testimonial) => {
  testimonial.addEventListener("mouseenter", () => {
    clearInterval(interval); // Pausa o carrossel
    testimonial.style.opacity = "1"; // Destaca o depoimento
    testimonial.style.transform = "scale(1.05)"; // Ampliação
  });

  testimonial.addEventListener("mouseleave", () => {
    testimonial.style.opacity = "0.6"; // Retorna à opacidade original
    testimonial.style.transform = "scale(1)"; // Retorna ao tamanho original
    startCarousel(); // Retoma o carrossel
  });
});

// Inicializa o carrossel ao carregar a página
startCarousel();

// Função para esconder seções subsequentes (se houver) após a última seção
const lastSection = document.querySelector("section:last-of-type");

if (lastSection) {
  let nextElement = lastSection.nextElementSibling;

  while (nextElement) {
    nextElement.style.display = "none"; // Oculta o próximo elemento
    nextElement = nextElement.nextElementSibling; // Avança para o próximo
  }
}
