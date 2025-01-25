<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $data_pagamento = $_POST['payment-date'];
    $descricao = $_POST['payment-descricao'];
    $data_vencimento = $_POST['due-date'];
    $classificacao = $_POST['classification'];
    $valor = $_POST['amount-payment'];
    $forma_pagamento = $_POST['payment-method'];

    // Verifica se todos os campos estão preenchidos
    if (empty($data_pagamento) || empty($descricao) || empty($data_vencimento) || empty($classificacao) || empty($valor) || empty($forma_pagamento)) {
        die("Todos os campos são obrigatórios!");
    }

    // SQL para inserir os dados na tabela (sem o campo 'id', pois é autoincrementado)
    $sql = "INSERT INTO pagamentos (data_pagamento, descricao, data_vencimento, classificacao, valor, forma_pagamento)
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepara a consulta
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }

    // Faz o bind dos parâmetros
    $stmt->bind_param("ssssds", $data_pagamento, $descricao, $data_vencimento, $classificacao, $valor, $forma_pagamento);

    // Executa a consulta
    if ($stmt->execute()) {
        echo "<p>Pagamento cadastrado com sucesso!</p>";
        echo "<meta http-equiv='refresh' content='2;url=/financas/pagamentos.html'>";
    } else {
        die('Erro ao executar a consulta: ' . $stmt->error);
    }

    // Fecha a declaração e a conexão
    $stmt->close();
    $conn->close();
}
?>
