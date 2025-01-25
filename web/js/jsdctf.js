// preloader
window.addEventListener("load", () => {
  document.body.classList.add("loaded"); // Remove o preloader
  document.getElementById("preloader").style.display = "none"; // Oculta o preloader
});

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

// Defina a data e hora de término da oferta
const offerEndTime = new Date("2025-01-31T00:00:00").getTime(); // Substitua pela sua data de término

function updateSpecialOfferCountdown() {
  const now = new Date().getTime(); // Hora atual
  const timeLeft = offerEndTime - now; // Tempo restante em milissegundos

  if (timeLeft <= 0) {
    // Se a oferta terminou, exiba a mensagem "Oferta Encerrada"
    document.getElementById("countdown-special-offer").innerHTML =
      "<p>Oferta Encerrada!</p>";
    return;
  }

  // Calcula os dias, horas, minutos e segundos restantes
  const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
  const hours = Math.floor(
    (timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );
  const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

  // Atualiza os valores no HTML
  document.getElementById("special-days").textContent = String(days).padStart(
    2,
    "0"
  );
  document.getElementById("special-hours").textContent = String(hours).padStart(
    2,
    "0"
  );
  document.getElementById("special-minutes").textContent = String(
    minutes
  ).padStart(2, "0");
  document.getElementById("special-seconds").textContent = String(
    seconds
  ).padStart(2, "0");
}

// Atualiza o contador a cada segundo
setInterval(updateSpecialOfferCountdown, 1000);

// Inicia a contagem imediatamente ao carregar a página
updateSpecialOfferCountdown();

// Dados simulados de compras
const compras = [
  {
    nome: "Raquel Silveira",
    tempo: "há 12 segundos",
    pagamento: "cartão de crédito",
  },
  { nome: "Ana da Silva.", tempo: "há 3 minutos", pagamento: "boleto" },
  { nome: "Francisco Tadeu.", tempo: "há 10 minutos", pagamento: "Pix" },
  {
    nome: "Paulo Renato",
    tempo: "há 5 minutos",
    pagamento: "cartão de crédito",
  },
  { nome: "Julia Santos.", tempo: "há 1 minuto", pagamento: "boleto" },
  { nome: "Carlos Magno.", tempo: "há 4 minutos", pagamento: "Pix" },
  {
    nome: "Mariana F.",
    tempo: "há 2 minutos",
    pagamento: "cartão de crédito",
  },
  { nome: "Thiago Pereira.", tempo: "há 7 minutos", pagamento: "Pix" },
  {
    nome: "Beatriz Lima.",
    tempo: "há 15 segundos",
    pagamento: "cartão de crédito",
  },
  {
    nome: "Luciana Santana.",
    tempo: "há 9 minutos",
    pagamento: "boleto",
  },
];

// Índice para controlar as compras
let indiceCompra = 0;

// Função para simular uma compra e mostrar a notificação
function exibirNotificacao(compra) {
  const toast = document.getElementById("toast-compras");
  const nomeElement = toast.querySelector(".nome");
  const tempoElement = toast.querySelector(".tempo");
  const pagamentoElement = toast.querySelector(".pagamento");

  // Atualizando os dados da notificação
  nomeElement.textContent = compra.nome;
  tempoElement.textContent = compra.tempo;
  pagamentoElement.textContent = `comprou no ${compra.pagamento}`;

  // Exibindo a notificação com a classe 'show'
  toast.classList.add("show");

  // Remover a notificação após 4 segundos
  setTimeout(() => {
    toast.classList.remove("show");
  }, 4000);
}

// Função para exibir as notificações em loop
function loopNotificacoes() {
  exibirNotificacao(compras[indiceCompra]);

  // Avançar para o próximo item da lista
  indiceCompra++;

  // Se chegar ao final, reinicia o loop
  if (indiceCompra >= compras.length) {
    indiceCompra = 0; // Reinicia o loop
  }
}

// Chama a função de notificação a cada 7 segundos
setInterval(loopNotificacoes, 7000);

// Scroll suave
document
  .querySelector(".header-action a")
  .addEventListener("click", function (e) {
    e.preventDefault(); // Evita o comportamento padrão do link
    document.querySelector("#comprar-agora").scrollIntoView({
      behavior: "smooth", // Scroll suave
      block: "center", // Centraliza o botão na tela
    });
  });
