async function loadReceivings() {
  try {
    const response = await fetch(
      "https://capacitacaotributaria.com.br/financas/php/get_receivings.php"
    );
    if (!response.ok) {
      throw new Error("Erro na solicitação: " + response.status);
    }

    const data = await response.json(); // Converte a resposta para JSON

    const tbody = document.querySelector("#receivings-table tbody");
    tbody.innerHTML = ""; // Limpa o conteúdo atual

    data.forEach((item) => {
      const row = document.createElement("tr");
      const valorFormatado = formatCurrency(parseFloat(item.valor) || 0);
      const dataFormatada = formatDate(item.data_recebimento);

      row.innerHTML = `
                <td>${item.id}</td>
                <td contenteditable="true" data-field="data_recebimento">${dataFormatada}</td>
                <td contenteditable="true" data-field="descricao">${item.descricao}</td>
                <td contenteditable="true" data-field="classificacao">${item.classificacao}</td>
                <td contenteditable="true" data-field="valor">${valorFormatado}</td>
                <td contenteditable="true" data-field="forma_recebimento">${item.forma_recebimento}</td>
                <td>
                    <button onclick="deleteReceiving(${item.id})">Excluir</button>
                    <button onclick="updateReceiving(${item.id}, this)">Salvar</button>
                </td>
            `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Erro ao carregar os dados:", error);
  }
}
