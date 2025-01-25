<?php
include 'conexao.php';

header('Content-Type: application/json');

try {
    // Definir as categorias específicas
    $categorias = [
        'bancos' => [
            'formas' => [
                'Banco do Brasil - Greicy',
                'Banco Inter - Ariel',
                'Nubank - Ariel',
                'Nubank - Greicy',
            ],
        ],
        'vale_alimentacao' => [
            'formas' => ['Alelo'],
        ],
        'fgts' => [
            'formas' => ['Caixa Econômica Federal - Ariel'],
        ],
    ];

    $saldos = [];

    foreach ($categorias as $categoria => $detalhes) {
        $formas = $detalhes['formas'];

        // Consultar recebimentos
        $sqlRecebimentos = "
            SELECT SUM(valor) AS total
            FROM recebimentos
            WHERE forma_recebimento IN ('" . implode("','", $formas) . "')";
        $resultRecebimentos = $conn->query($sqlRecebimentos);
        $recebimentosTotal = $resultRecebimentos->fetch_assoc()['total'] ?? 0;

        // Consultar pagamentos
        $sqlPagamentos = "
            SELECT SUM(valor) AS total
            FROM pagamentos
            WHERE forma_pagamento IN ('" . implode("','", $formas) . "')";
        $resultPagamentos = $conn->query($sqlPagamentos);
        $pagamentosTotal = $resultPagamentos->fetch_assoc()['total'] ?? 0;

        // Calcular o saldo
        $saldos[$categoria] = $recebimentosTotal - $pagamentosTotal;
    }

    // Retornar os saldos em JSON
    echo json_encode(['success' => true, 'saldos' => $saldos]);
} catch (Exception $e) {
    // Retornar mensagem de erro em caso de falha
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
