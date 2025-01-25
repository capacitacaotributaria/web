<?php
include 'conexao.php';

header('Content-Type: application/json');

// Consulta os dados da tabela
$sql = "SELECT * FROM previsao_orcamentaria";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Erro na consulta: ' . $conn->error]);
    exit;
}

$classificacoes = [];
while ($row = $result->fetch_assoc()) {
    $classificacoes[] = $row;
}

// Retorna os dados como JSON
echo json_encode($classificacoes);

$conn->close();
?>
