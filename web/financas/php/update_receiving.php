<?php
include 'conexao.php';

// Obtém os dados enviados pelo JavaScript
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$data_recebimento = $data['data_recebimento'] ?? null;
$descricao = $data['descricao'] ?? null;
$classificacao = $data['classificacao'] ?? null;
$valor = $data['valor'] ?? null;
$forma_recebimento = $data['forma_recebimento'] ?? null;

if ($id && $data_recebimento && $descricao && $classificacao && $valor && $forma_recebimento) {
    // Consulta SQL para atualizar o registro
    $sql = "UPDATE recebimentos SET data_recebimento = ?, descricao = ?, classificacao = ?, valor = ?, forma_recebimento = ? WHERE id = ?";
    
    // Prepara a consulta
    $stmt = $conn->prepare($sql);
    
    // Bind dos parâmetros
    $stmt->bind_param('ssssdi', $data_recebimento, $descricao, $classificacao, $valor, $forma_recebimento, $id);
    
    // Executa a consulta
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    
    // Fecha a declaração
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
}

// Fecha a conexão
$conn->close();
?>
