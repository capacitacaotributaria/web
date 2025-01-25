<?php
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$classificacao = $data['classificacao'];
$tipo = $data['tipo'];
$valor = $data['valor'];

$sql = "INSERT INTO previsao_orcamentaria (classificacao, tipo, valor) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssd', $classificacao, $tipo, $valor);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
