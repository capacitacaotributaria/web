<?php
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$classificacao = $data['classificacao'];
$tipo = $data['tipo'];
$valor = $data['valor'];

$sql = "UPDATE previsao_orcamentaria SET classificacao = ?, tipo = ?, valor = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssdi', $classificacao, $tipo, $valor, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
