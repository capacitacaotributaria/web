<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$pdo = new PDO('mysql:host=leadsdb.mysql.uhserver.com;dbname=leadsdb', 'superadmin1', 'Sistemas01*');
$query = $pdo->query('SELECT nome, email, status_conversao FROM leads');
$leads = $query->fetchAll(PDO::FETCH_ASSOC);

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

            $mailer->send($emailMessage);
        }
        echo '<p style="color:green;">E-mails enviados com sucesso!</p>';
    } catch (Exception $e) {
        echo '<p style="color:red;">Erro ao enviar e-mails: ' . $e->getMessage() . '</p>';
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
        .email-item label {
            flex: 1;
            cursor: pointer;
        }
        .editor-container {
            flex: 2;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        textarea {
            width: 100%;
            height: 300px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <form method="POST">
        <div class="email-list-container">
            <select id="status_filter" onchange="filterEmails()">
                <option value="">Todos os Status</option>
                <option value="Venda Efetuada">Venda Efetuada</option>
                <option value="Pendente">Pendente</option>
                <option value="Perdido">Perdido</option>
                <option value="Em negociação">Em negociação</option>
                <option value="Aguardando Aprovação">Aguardando Aprovação</option>
            </select>
            <div>
                <button type="button" onclick="toggleCheckboxes(true)">Marcar Todos</button>
                <button type="button" onclick="toggleCheckboxes(false)">Desmarcar Todos</button>
            </div>
            <ul class="email-list" id="email_list">
                <?php foreach ($leads as $lead): ?>
                    <li class="email-item" data-status="<?php echo $lead['status_conversao']; ?>">
                        <input type="checkbox" name="emails[]" value="<?php echo $lead['email']; ?>">
                        <label><?php echo $lead['nome']; ?> (<?php echo $lead['email']; ?>)</label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="editor-container">
            <label for="subject">Assunto do E-mail:</label>
            <input type="text" name="subject" required>

            <label for="email_body">Mensagem do E-mail:</label>
            <textarea name="email_body" id="email_body"></textarea>

            <script>
                CKEDITOR.replace('email_body');
            </script>

            <button type="submit" name="send_email">Enviar E-mails</button>
        </div>
    </form>

    <script>
        function filterEmails() {
            const filter = document.getElementById('status_filter').value;
            const emailItems = document.querySelectorAll('.email-item');

            emailItems.forEach(item => {
                if (filter === '' || item.getAttribute('data-status') === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        function toggleCheckboxes(checked) {
            const checkboxes = document.querySelectorAll('input[name="emails[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = checked);
        }
    </script>
</body>
</html>
