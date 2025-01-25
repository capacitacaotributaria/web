<?php
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$data_pagamento = $data['data_pagamento'] ?? null;
$descricao = $data['descricao'] ?? null;
$data_vencimento = $data['data_vencimento'] ?? null;
$classificacao = $data['classificacao'] ?? null;
$valor = $data['valor'] ?? null;
$forma_pagamento = $data['forma_pagamento'] ?? null;

if ($id && $data_pagamento && $descricao && $data_vencimento && $classificacao && $valor && $forma_pagamento) {
    $sql = "UPDATE pagamentos SET data_pagamento = ?, descricao = ?, data_vencimento = ?, classificacao = ?, valor = ?, forma_pagamento = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssdsi', $data_pagamento, $descricao, $data_vencimento, $classificacao, $valor, $forma_pagamento, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
}

$conn->close();
?>
