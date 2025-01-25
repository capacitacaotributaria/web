<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php'; // Ajuste o caminho para o autoloader do Composer.

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

if (isset($_POST['enviarFormulario'])) {
    // Captura e validação dos dados do formulário
    $nome = isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $telefone = isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : '';
    $aceite = isset($_POST['aceite']) ? true : false;

    // Valida se os campos obrigatórios foram preenchidos
    if (empty($nome) || empty($email) || empty($telefone) || !$aceite) {
        echo "<p style='color:red;'>Por favor, preencha todos os campos obrigatórios corretamente.</p>";
        exit();
    }

    // Valida o e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>E-mail inválido!</p>";
        exit();
    }

    // Configurações do servidor de e-mail
    $enviaFormularioParaNome = 'Equipe Capacitação Tributária';
    $enviaFormularioParaEmail = 'contato@capacitacaotributaria.com.br';

    $caixaPostalServidorEmail = 'contato@capacitacaotributaria.com.br';
    $caixaPostalServidorSenha = 'Sistemas01';

    // Mensagem para o administrador
    $assuntoAdmin = "Nova inscrição para a aula gratuita!";
    $mensagemAdmin = "Detalhes da inscrição:\n\n";
    $mensagemAdmin .= "Nome: $nome\n";
    $mensagemAdmin .= "E-mail: $email\n";
    $mensagemAdmin .= "Telefone: $telefone\n";

    // Mensagem para o usuário
    $assuntoUsuario = "Inscrição confirmada!";
    $mensagemUsuario = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #333333;
                line-height: 1.6;
            }
            h2 {
                font-size: 22px;
            }
            p {
                font-size: 16px;
                margin-bottom: 10px;
            }
            .icon {
                font-size: 30px;
                vertical-align: middle;
                margin-right: 10px;
            }
            .highlight {
                color: #0073e6;
                font-weight: bold;
            }
            .button {
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
            }
            .button:hover {
                background-color: #45a049;
            }
            .section {
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <h2>Olá $nome,</h2>
        <p>Primeiramente, queremos agradecer pela sua inscrição na nossa <strong>Aula ao Vivo e Gratuita</strong> sobre a Nova DCTF Web!</p>
        
        <p>🎯 Estamos muito felizes com o seu interesse e comprometimento em se atualizar sobre as mudanças que impactarão diretamente a sua atuação no mercado tributário em 2025.</p>
        
        <div class='section'>
            <p><span class='icon'>🔔</span><strong>Lembre-se:</strong> A aula será no <span class='highlight'>dia 21/01/2025, às 20h (horário de Brasília)</span>. Prepare-se para aprender estratégias valiosas que vão te colocar à frente das exigências fiscais.</p>
        </div>

        <div class='section'>
            <p><span class='icon'>📱</span><strong>Grupo Exclusivo no WhatsApp</strong></p>
            <p>Para tornar a experiência ainda mais interativa e produtiva, criamos um grupo exclusivo no WhatsApp para os participantes da aula. No grupo, você terá acesso a:</p>
            <ul>
                <li><strong>Conteúdo adicional</strong> e atualizações sobre a aula;</li>
                <li><strong>Dúvidas esclarecidas</strong> diretamente com o <strong>Prof. José Ariel</strong>;</li>
                <li><strong>Networking</strong> com outros profissionais da área tributária;</li>
                <li>E muito mais!</li>
            </ul>

        </div>

        <div class='section'>
            <p>Estamos ansiosos para te ver na aula e compartilhar todo o conhecimento que preparamos para você!</p>
            <p>Se tiver qualquer dúvida ou precisar de mais informações, não hesite em nos contatar.</p>
            <p>Nos vemos em breve!</p>
        </div>

        <p>Atenciosamente,<br>Equipe Capacitação Tributária</p>
    </body>
    </html>";

    try {
        // Configuração do transporte SMTP
        $transport = Transport::fromDsn(
            "smtp://$caixaPostalServidorEmail:$caixaPostalServidorSenha@smtp.".substr(strstr($caixaPostalServidorEmail, '@'), 1).":587"
        );
        $mailer = new Mailer($transport);

        // Envia o e-mail para o administrador
        $emailParaAdmin = (new Email())
            ->from($caixaPostalServidorEmail)
            ->to($enviaFormularioParaEmail)
            ->subject($assuntoAdmin)
            ->text($mensagemAdmin); // A mensagem do admin permanece em texto simples

        // Envia o e-mail de agradecimento ao usuário
        $emailParaUsuario = (new Email())
            ->from($caixaPostalServidorEmail)
            ->to($email)
            ->subject($assuntoUsuario)
            ->html($mensagemUsuario);  // Enviando a mensagem como HTML

        // Adiciona o cabeçalho List-Unsubscribe no e-mail para o usuário
        $emailParaUsuario->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:unsubscribe@capacitacaotributaria.com.br>');

        // Envia os e-mails
        $mailer->send($emailParaAdmin);
        $mailer->send($emailParaUsuario);

        // Redireciona para a página de agradecimento
        header("Location: https://capacitacaotributaria.com.br/agradecimento.html");
        exit();
    } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
        echo "<p style='color:red;'>Erro ao enviar o email: " . $e->getMessage() . "</p>";
        exit();
    } catch (Exception $e) {
        echo "<p style='color:red;'>Erro geral: " . $e->getMessage() . "</p>";
        exit();
    }
} else {
    echo "<p style='color:red;'>Acesso inválido. Por favor, envie o formulário corretamente.</p>";
    exit();
}
?>
