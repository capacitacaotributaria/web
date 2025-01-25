// Captura o evento de envio do formulário
document
  .getElementById("payment-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault(); // Impede o comportamento padrão do formulário

    // Coleta os dados do formulário
    const formData = {
      date: document.getElementById("payment-date").value,
      description: document.getElementById("payment-descricao").value,
      dueDate: document.getElementById("due-date").value,
      classification: document.getElementById("classification").value,
      amount: document.getElementById("amount").value,
      paymentMethod: document.getElementById("payment-method").value,
    };

    // URL do script Apps Script
    const scriptURL =
      "https://script.google.com/macros/s/AKfycbzHz30xu0JFlvHumqrHzBSEh02KWLv4ATPx-RTog5OElBKlmbydKaAi1VHOiJTs1oKvEQ/exec"; // Substitua pela URL gerada no Apps Script

    try {
      const response = await fetch(scriptURL, {
        method: "POST",
        body: JSON.stringify(formData),
        headers: { "Content-Type": "application/json" },
      });

      // Mostra uma mensagem de sucesso ou erro
      const result = await response.text();
      alert(result); // Exibe o retorno do Apps Script
    } catch (error) {
      console.error("Erro ao enviar os dados:", error);
      alert("Ocorreu um erro ao enviar os dados. Tente novamente.");
    }
  });
