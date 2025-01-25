<?php
include 'conexao.php';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados enviados pelo formulário
    $classificacao = $_POST['classificacao'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    $valor = $_POST['valor'] ?? null;

    // Valida os campos obrigatórios
    if (empty($classificacao) || empty($tipo) || empty($valor)) {
        die("Todos os campos são obrigatórios!");
    }

    // Prepara a consulta para inserir os dados
    $sql = "INSERT INTO previsao_orcamentaria (classificacao, tipo, valor) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssd', $classificacao, $tipo, $valor);

    // Executa a consulta
    if ($stmt->execute()) {
        echo "<p>Classificação adicionada com sucesso!</p>";
        // Redireciona de volta para a página de previsão orçamentária
        echo "<meta http-equiv='refresh' content='2;url=/financas/previsao_orcamentaria.html'>";
    } else {
        echo "Erro ao inserir classificação: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método de requisição inválido!";
}
?>
