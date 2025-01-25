// Função para rolar até a seção do formulário
function scrollToForm() {
  document
    .getElementById("form-section")
    .scrollIntoView({ behavior: "smooth" });
}

// Efeito de animação para os boxes de aprendizado
function hoverEffect(element) {
  element.style.transform = "scale(1.05)";
  element.style.boxShadow = "0px 8px 20px rgba(0, 0, 0, 0.1)";
}

function removeEffect(element) {
  element.style.transform = "scale(1)";
  element.style.boxShadow = "none";
}

// Definindo a data do evento (21 de janeiro de 2025, 20:00)
const eventDate = new Date("2025-01-21T20:00:00").getTime();

// Atualiza a contagem regressiva a cada 1 segundo
const countdownInterval = setInterval(function () {
  const now = new Date().getTime();
  const distance = eventDate - now;

  // Calcula os dias, horas, minutos e segundos
  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
  const hours = Math.floor(
    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Exibindo no formato desejado
  document.getElementById(
    "countdown"
  ).innerHTML = `Faltam <span class="time-unit">${padNumber(
    days
  )}</span> <span class="unit-label">Dias</span> : 
       <span class="time-unit">${padNumber(
         hours
       )}</span> <span class="unit-label">Horas</span> : 
       <span class="time-unit">${padNumber(
         minutes
       )}</span> <span class="unit-label">Minutos</span> : 
       <span class="time-unit">${padNumber(
         seconds
       )}</span> <span class="unit-label">Segundos</span>`;

  if (distance < 0) {
    clearInterval(countdownInterval);
    document.getElementById("countdown").innerHTML = "A aula gratuita começou!";
    document.getElementById("urgent-message").style.display = "none";
  }
}, 1000);

function padNumber(number) {
  return number < 10 ? "0" + number : number;
}
