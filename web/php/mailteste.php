<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$pdo = new PDO('mysql:host=leadsdb.mysql.uhserver.com;dbname=leadsdb', 'superadmin1', 'Sistemas01*');

// Buscar leads do banco de dados
$queryLeads = $pdo->query('SELECT nome, email, status_conversao FROM leads');
$leads = $queryLeads->fetchAll(PDO::FETCH_ASSOC);

// Buscar e-mails enviados na última hora
$queryLimite = $pdo->query("SELECT COUNT(*) AS total FROM emails_enviados WHERE data_envio >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$totalEnviadosUltimaHora = $queryLimite->fetch(PDO::FETCH_ASSOC)['total'];

// Buscar leads que nunca receberam e-mails
$queryLeadsSemEmail = $pdo->query("
    SELECT l.nome, l.email
    FROM leads l
    LEFT JOIN emails_enviados e ON l.email = e.email
    WHERE e.email IS NULL
");
$leadsSemEmail = $queryLeadsSemEmail->fetchAll(PDO::FETCH_ASSOC);

// Buscar e-mails rejeitados
$queryRejeitados = $pdo->query("SELECT email, motivo_rejeicao, data_rejeicao FROM emails_rejeitados ORDER BY data_rejeicao DESC");
$emailsRejeitados = $queryRejeitados->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {
    $subject = $_POST['subject'];
    $message = $_POST['email_body'];
    $selectedEmails = isset($_POST['emails']) ? $_POST['emails'] : [];

    $transport = Transport::fromDsn('smtp://contato@capacitacaotributaria.com.br:Sistemas01@smtp.capacitacaotributaria.com.br:587');
    $mailer = new Mailer($transport);

    try {
        foreach ($selectedEmails as $email) {
            $emailMessage = (new Email())
                ->from(new Symfony\Component\Mime\Address('contato@capacitacaotributaria.com.br', 'Prof. José Ariel'))
                ->to($email)
                ->subject($subject)
                ->html($message)
                ->text(strip_tags($message));

            try {
                $mailer->send($emailMessage);

                // Registro de envio bem-sucedido
                $stmt = $pdo->prepare('INSERT INTO emails_enviados (email, data_envio, status_envio) VALUES (:email, NOW(), "sucesso")');
                $stmt->execute([':email' => $email]);
            } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
                // Registro de rejeição
                $stmt = $pdo->prepare('INSERT INTO emails_enviados (email, data_envio, status_envio) VALUES (:email, NOW(), "rejeitado")');
                $stmt->execute([':email' => $email]);

                // Detalhamento do motivo da rejeição
                $stmtRejeitado = $pdo->prepare('INSERT INTO emails_rejeitados (email, motivo_rejeicao, data_rejeicao) VALUES (:email, :motivo, NOW())');
                $stmtRejeitado->execute([
                    ':email' => $email,
                    ':motivo' => $e->getMessage()
                ]);

                echo '<p style="color:red;">Erro ao enviar para ' . htmlspecialchars($email) . ': ' . $e->getMessage() . '</p>';
            }
        }
        echo '<p style="color:green;">E-mails enviados com sucesso!</p>';
    } catch (Exception $e) {
        echo '<p style="color:red;">Erro ao processar o envio: ' . $e->getMessage() . '</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio de E-mails</title>
    <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        form {
            display: flex;
            width: 90%;
            max-width: 1200px;
            height: 90%;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .notifications {
            width: 90%;
            max-width: 1200px;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        .notifications p {
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .notifications p.warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        .notifications p.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .email-list-container {
            flex: 1;
            overflow-y: auto;
            border-right: 1px solid #ddd;
            padding: 10px;
            background: #f9f9f9;
        }
        .email-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .email-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="notifications">
        <?php
        if ($totalEnviadosUltimaHora >= 140) {
            echo '<p class="warning">⚠️ Você está próximo do limite de 150 e-mails/hora. Enviados: ' . $totalEnviadosUltimaHora . '</p>';
        }
        if (count($leadsSemEmail) > 0) {
            echo '<p class="warning">⚠️ Existem ' . count($leadsSemEmail) . ' leads que nunca receberam e-mails:</p>';
            echo '<ul>';
            foreach ($leadsSemEmail as $lead) {
                echo '<li>' . htmlspecialchars($lead['nome']) . ' (' . htmlspecialchars($lead['email']) . ')</li>';
            }
            echo '</ul>';
        }
        if (count($emailsRejeitados) > 0) {
            echo '<p class="error">⚠️ Existem ' . count($emailsRejeitados) . ' e-mails rejeitados:</p>';
            echo '<ul>';
            foreach ($emailsRejeitados as $rejeitado) {
                echo '<li>' . htmlspecialchars($rejeitado['email']) . ': ' . htmlspecialchars($rejeitado['motivo_rejeicao']) . '</li>';
            }
            echo '</ul>';
        }
        ?>
    </div>

    <form method="POST">
        <div class="email-list-container">
            <ul class="email-list">
                <?php foreach ($leads as $lead): ?>
                    <li class="email-item">
                        <input type="checkbox" name="emails[]" value="<?php echo htmlspecialchars($lead['email']); ?>">
                        <label><?php echo htmlspecialchars($lead['nome']); ?> (<?php echo htmlspecialchars($lead['email']); ?>)</label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <label for="subject">Assunto:</label>
            <input type="text" name="subject" required>
            <label for="email_body">Mensagem:</label>
            <textarea name="email_body" id="email_body"></textarea>
            <script>
                CKEDITOR.replace('email_body');
            </script>
            <button type="submit" name="send_email">Enviar E-mails</button>
        </div>
    </form>
</body>
</html>
