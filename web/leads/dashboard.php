<?php
session_start();
require_once('db.php');

// Exibe todos os erros para facilitar o diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
$usuarioLogado = $_SESSION['usuario'];

// Consultas para obter as quantidades de leads
$totalLeads = $conn->query("SELECT COUNT(*) as total FROM leads")->fetch_assoc()['total'] ?? 0;
$leadsConvertidos = $conn->query("SELECT COUNT(*) as total FROM leads WHERE status_conversao = 'Venda Efetuada'")->fetch_assoc()['total'] ?? 0;
$leadsPendentes = $conn->query("SELECT COUNT(*) as total FROM leads WHERE status_conversao = 'Pendente'")->fetch_assoc()['total'] ?? 0;

// Se um ID de lead foi passado, carrega os dados para edição
$lead = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Verifica se o ID existe no banco de dados antes de carregar os dados
    $leadResult = $conn->query("SELECT * FROM leads WHERE id = $id");
    if ($leadResult->num_rows > 0) {
        $lead = $leadResult->fetch_assoc();
    } else {
        die("Lead não encontrado.");
    }
}

// Atualização ou inserção de lead
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $status = $_POST['status'];
    $email_enviado = $_POST['email_enviado'];
    $whatsapp = $_POST['whatsapp'];
    $fonte = $_POST['fonte'];
    $responsavel = $_POST['responsavel'];
    $data_ultimo_contato = $_POST['data_ultimo_contato'];
    $status_conversao = $_POST['status_conversao'];
    $observacoes = $_POST['observacoes'];
    $id = $_POST['id'];

    // Exibe os dados recebidos para verificação
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Verifica se é uma atualização ou inserção
    if (!empty($id)) {
        // Atualiza o lead existente
        $stmt = $conn->prepare("UPDATE leads SET nome=?, email=?, telefone=?, status=?, email_enviado=?, whatsapp=?, fonte=?, responsavel=?, data_ultimo_contato=?, status_conversao=?, observacoes=? WHERE id=?");

        if (!$stmt) {
            die('Erro ao preparar a consulta: ' . $conn->error);
        }

        $stmt->bind_param('sssssssssssi', $nome, $email, $telefone, $status, $email_enviado, $whatsapp, $fonte, $responsavel, $data_ultimo_contato, $status_conversao, $observacoes, $id);
        
        if (!$stmt->execute()) {
            die('Erro ao executar a consulta de atualização: ' . $stmt->error);
        } else {
            // Atualização bem-sucedida, redireciona para o dashboard
            header("Location: dashboard.php");
            exit;
        }
    } else {
        // Insere um novo lead
        $stmt = $conn->prepare("INSERT INTO leads (nome, email, telefone, status, email_enviado, whatsapp, fonte, responsavel, data_ultimo_contato, status_conversao, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die('Erro ao preparar a consulta: ' . $conn->error);
        }

        $stmt->bind_param('sssssssssss', $nome, $email, $telefone, $status, $email_enviado, $whatsapp, $fonte, $responsavel, $data_ultimo_contato, $status_conversao, $observacoes);

        if (!$stmt->execute()) {
            die('Erro ao executar a consulta de inserção: ' . $stmt->error);
        } else {
            // Inserção bem-sucedida, redireciona para o dashboard
            header("Location: dashboard.php");
            exit;
        }
    }
}

// Exclusão de Lead
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Verifica se o ID do lead a ser excluído existe no banco
    $result = $conn->query("SELECT id FROM leads WHERE id = $delete_id");
    if ($result->num_rows == 0) {
        die("Lead não encontrado para exclusão.");
    }

    $stmt = $conn->prepare("DELETE FROM leads WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if (!$stmt->execute()) {
        die('Erro ao excluir o lead: ' . $stmt->error);
    }
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Leads</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: white; color: black; font-family: 'Arial', sans-serif; }
        .navbar { background-color: #1f1f1f; }
        .table-hover tbody tr:hover { background-color: #f1f1f1; transition: background-color 0.3s; }
        .card { border-radius: 15px; }
        .card-body { text-align: center; }
        .table th, .table td { text-align: center; }
        .btn { border-radius: 8px; }
        .table-responsive { border-radius: 10px; overflow: hidden; }
        .table thead { background-color: #17a2b8; color: white; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Gestão de Leads</a>
    <span class="navbar-text">Usuário: <?php echo htmlspecialchars($usuarioLogado); ?></span>
    <a class="btn btn-danger ml-auto" href="logout.php">Sair</a>
</nav>

<!-- Dashboard -->
<div class="container mt-4">
    <h2>Dashboard de Leads</h2>
    <div class="row">
        <!-- Card de Total de Leads -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total de Leads</h5>
                    <p class="card-text"><?php echo $totalLeads; ?></p>
                </div>
            </div>
        </div>

        <!-- Card de Leads Convertidos -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Leads Convertidos</h5>
                    <p class="card-text"><?php echo $leadsConvertidos; ?></p>
                </div>
            </div>
        </div>

        <!-- Card de Leads Pendentes -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Leads Pendentes</h5>
                    <p class="card-text"><?php echo $leadsPendentes; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário para Adicionar ou Editar Lead -->
    <h3 class="mt-5">Adicionar ou Editar Lead</h3>
    <form action="dashboard.php" method="POST">
        <input type="hidden" name="id" value="<?php echo isset($lead) ? $lead['id'] : ''; ?>">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Nome</label>
                <input type="text" class="form-control" name="nome" value="<?php echo isset($lead) ? $lead['nome'] : ''; ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label>E-mail</label>
                <input type="email" class="form-control" name="email" value="<?php echo isset($lead) ? $lead['email'] : ''; ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label>Telefone</label>
                <input type="text" class="form-control" name="telefone" value="<?php echo isset($lead) ? $lead['telefone'] : ''; ?>">
            </div>
            <div class="form-group col-md-6">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="Em Progresso" <?php echo (isset($lead) && $lead['status'] == 'Em Progresso') ? 'selected' : ''; ?>>Em Progresso</option>
                    <option value="Qualificado" <?php echo (isset($lead) && $lead['status'] == 'Qualificado') ? 'selected' : ''; ?>>Qualificado</option>
                    <option value="Não Qualificado" <?php echo (isset($lead) && $lead['status'] == 'Não Qualificado') ? 'selected' : ''; ?>>Não Qualificado</option>
                    <option value="Inativo" <?php echo (isset($lead) && $lead['status'] == 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
                    <option value="Fechado (Não Vendido)" <?php echo (isset($lead) && $lead['status'] == 'Fechado (Não Vendido)') ? 'selected' : ''; ?>>Fechado (Não Vendido)</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>E-mail Enviado</label>
                <select class="form-control" name="email_enviado">
                    <option value="sim" <?php echo (isset($lead) && $lead['email_enviado'] == 'sim') ? 'selected' : ''; ?>>Sim</option>
                    <option value="não" <?php echo (isset($lead) && $lead['email_enviado'] == 'não') ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>WhatsApp</label>
                <select class="form-control" name="whatsapp">
                    <option value="sim" <?php echo (isset($lead) && $lead['whatsapp'] == 'sim') ? 'selected' : ''; ?>>Sim</option>
                    <option value="não" <?php echo (isset($lead) && $lead['whatsapp'] == 'não') ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
             <div class="form-group col-md-6">
                <label>Fonte</label>
                <select class="form-control" name="fonte">
                    <option value="Formulário do site" <?php echo (isset($lead) && $lead['fonte'] == 'Formulário do site') ? 'selected' : ''; ?>>Formulário do site</option>
                    <option value="E-mail marketing" <?php echo (isset($lead) && $lead['fonte'] == 'E-mail marketing') ? 'selected' : ''; ?>>E-mail marketing</option>
                    <option value="Anúncios pagos" <?php echo (isset($lead) && $lead['fonte'] == 'Anúncios pagos') ? 'selected' : ''; ?>>Anúncios pagos (Google Ads, Facebook Ads)</option>
                    <option value="SEO" <?php echo (isset($lead) && $lead['fonte'] == 'SEO') ? 'selected' : ''; ?>>SEO (Otimização para motores de busca)</option>
                    <option value="Indicação" <?php echo (isset($lead) && $lead['fonte'] == 'Indicação') ? 'selected' : ''; ?>>Indicação</option>
                    <option value="Redes sociais" <?php echo (isset($lead) && $lead['fonte'] == 'Redes sociais') ? 'selected' : ''; ?>>Redes sociais</option>
                    <option value="Feiras e eventos" <?php echo (isset($lead) && $lead['fonte'] == 'Feiras e eventos') ? 'selected' : ''; ?>>Feiras e eventos</option>
                    <option value="Conteúdo orgânico" <?php echo (isset($lead) && $lead['fonte'] == 'Conteúdo orgânico') ? 'selected' : ''; ?>>Conteúdo orgânico (Blog, site)</option>
                    <option value="Outros" <?php echo (isset($lead) && $lead['fonte'] == 'Outros') ? 'selected' : ''; ?>>Outros</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Responsável</label>
                <select class="form-control" name="responsavel">
                    <option value="Ariel" <?php echo (isset($lead) && $lead['responsavel'] == 'Ariel') ? 'selected' : ''; ?>>Ariel</option>
                    <option value="Marcelo" <?php echo (isset($lead) && $lead['responsavel'] == 'Marcelo') ? 'selected' : ''; ?>>Marcelo</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Data Último Contato</label>
                <input type="datetime-local" class="form-control" name="data_ultimo_contato" value="<?php echo isset($lead) ? $lead['data_ultimo_contato'] : ''; ?>">
            </div>
            <div class="form-group col-md-6">
                <label>Status de Conversão</label>
                <select class="form-control" name="status_conversao">
                    <option value="Venda Efetuada" <?php echo (isset($lead) && $lead['status_conversao'] == 'Venda Efetuada') ? 'selected' : ''; ?>>Venda Efetuada</option>
                    <option value="Pendente" <?php echo (isset($lead) && $lead['status_conversao'] == 'Pendente') ? 'selected' : ''; ?>>Pendente</option>
                    <option value="Perdido" <?php echo (isset($lead) && $lead['status_conversao'] == 'Perdido') ? 'selected' : ''; ?>>Perdido</option>
                    <option value="Em negociação" <?php echo (isset($lead) && $lead['status_conversao'] == 'Em negociação') ? 'selected' : ''; ?>>Em negociação</option>
                    <option value="Aguardando Aprovação" <?php echo (isset($lead) && $lead['status_conversao'] == 'Aguardando Aprovação') ? 'selected' : ''; ?>>Aguardando Aprovação</option>
                </select>
            </div>

            <div class="form-group col-md-12">
                <label>Observações</label>
                <textarea class="form-control" name="observacoes"><?php echo isset($lead) ? $lead['observacoes'] : ''; ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
    <!-- Lista de Leads -->
    <h3 class="mt-5">Lista de Leads</h3>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $leadsResult = $conn->query("SELECT * FROM leads");
            while ($row = $leadsResult->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['telefone']; ?></td>
                <td><?php echo $row['status_conversao']; ?></td>
                <td>
                    <a href="dashboard.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="dashboard.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
