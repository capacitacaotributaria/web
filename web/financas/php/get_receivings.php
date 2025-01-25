<?php
include 'conexao.php';

// Consulta os recebimentos
$sql = "SELECT id, data_recebimento, descricao, classificacao, valor, forma_recebimento FROM recebimentos";
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

$conn->close();
?>
