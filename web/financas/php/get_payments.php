<?php
include 'conexao.php';

// Consulta os pagamentos
$sql = "SELECT id, data_pagamento, descricao, data_vencimento, classificacao, valor, forma_pagamento FROM pagamentos";
$result = $conn->query($sql);

if ($result === false) {
    die("Erro na consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode([]);
}

// Fecha a conexão com o banco
$conn->close();
?>
