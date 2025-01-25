<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $data = $_POST['receiving-date'];
    $descricao = $_POST['receiving-descricao'];
    $classificacao = $_POST['classification-receiving'];  // Deixe como string
    $valor = $_POST['amount-receiving'];
    $forma = $_POST['receiving-method'];

    // Verifica se todos os campos estão preenchidos
    if (empty($data) || empty($descricao) || empty($classificacao) || empty($valor) || empty($forma)) {
        die("Todos os campos são obrigatórios!");
    }

    // Corrige a forma de bind_param para string
    $sql = "INSERT INTO recebimentos (data_recebimento, descricao, classificacao, valor, forma_recebimento)
            VALUES (?, ?, ?, ?, ?)";

    // Prepara a declaração SQL
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }

    // Faz o bind dos parâmetros de forma correta
    $stmt->bind_param("sssss", $data, $descricao, $classificacao, $valor, $forma);

    // Executa a consulta
    if ($stmt->execute()) {
        echo "<p>Recebimento cadastrado com sucesso!</p>";
        
        // Defina o baseUrl antes de usá-lo
        $baseUrl = '/financas'; // Substitua conforme necessário
        echo "<meta http-equiv='refresh' content='2;url={$baseUrl}/recebimentos.html'>";
    } else {
        die('Erro ao executar a consulta: ' . $stmt->error);
    }

    // Fecha a declaração e a conexão
    $stmt->close();
    $conn->close();
}
?>
