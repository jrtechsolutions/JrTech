<?php
/**
 * Versão que usa SMTP do Plesk diretamente
 * Funciona mesmo quando mail() falha
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Erro ao processar dados']);
    exit;
}

if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
    exit;
}

$name = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$phone = isset($data['phone']) ? htmlspecialchars(trim($data['phone']), ENT_QUOTES, 'UTF-8') : '';
$company = isset($data['company']) ? htmlspecialchars(trim($data['company']), ENT_QUOTES, 'UTF-8') : '';
$message = htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

$to = 'contato@jrtechnologysolutions.com.br';
$subject = 'Novo contato do site - ' . $name;

$emailBody = "Você recebeu uma nova mensagem do formulário de contato do site.\n\n";
$emailBody .= "Nome: $name\n";
$emailBody .= "Email: $email\n";
$emailBody .= "Telefone: " . ($phone ?: 'Não informado') . "\n";
$emailBody .= "Empresa: " . ($company ?: 'Não informado') . "\n\n";
$emailBody .= "Mensagem:\n$message\n";

// Função para enviar via SMTP direto (sem bibliotecas)
function sendEmailSMTP($to, $subject, $body, $fromEmail, $fromName, $replyTo) {
    // No Plesk, geralmente o servidor SMTP local é 'localhost' na porta 25
    // Ou pode ser o servidor de email do domínio
    $smtpHost = 'localhost'; // Tenta localhost primeiro (servidor local do Plesk)
    $smtpPort = 25;
    
    // Tentar conectar ao servidor SMTP
    $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
    
    if (!$socket) {
        // Se localhost falhar, tenta o servidor do domínio
        $smtpHost = 'mail.jrtechnologysolutions.com.br';
        $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
        
        if (!$socket) {
            return ['success' => false, 'error' => "Não foi possível conectar ao servidor SMTP: $errstr ($errno)"];
        }
    }
    
    // Ler resposta inicial
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '220') {
        fclose($socket);
        return ['success' => false, 'error' => "Servidor SMTP não respondeu: $response"];
    }
    
    // EHLO
    fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
    $response = fgets($socket, 515);
    
    // MAIL FROM
    fputs($socket, "MAIL FROM: <$fromEmail>\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '250') {
        fclose($socket);
        return ['success' => false, 'error' => "Erro no MAIL FROM: $response"];
    }
    
    // RCPT TO
    fputs($socket, "RCPT TO: <$to>\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '250') {
        fclose($socket);
        return ['success' => false, 'error' => "Erro no RCPT TO: $response"];
    }
    
    // DATA
    fputs($socket, "DATA\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '354') {
        fclose($socket);
        return ['success' => false, 'error' => "Erro no DATA: $response"];
    }
    
    // Headers e corpo
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "To: <$to>\r\n";
    $headers .= "Reply-To: $replyTo\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Date: " . date('r') . "\r\n";
    $headers .= "\r\n";
    
    fputs($socket, $headers . $body . "\r\n.\r\n");
    $response = fgets($socket, 515);
    
    // QUIT
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
    if (substr($response, 0, 3) == '250') {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => "Erro ao enviar: $response"];
    }
}

// Tentar primeiro com mail() (mais rápido se funcionar)
ini_set('sendmail_from', 'contato@jrtechnologysolutions.com.br');

$headers = "From: Formulário Site <contato@jrtechnologysolutions.com.br>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

error_clear_last();
$mailResult = @mail($to, $subject, $emailBody, $headers);

if ($mailResult) {
    // Se mail() funcionou, ótimo!
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
} else {
    // Se mail() falhou, tentar SMTP direto
    $smtpResult = sendEmailSMTP(
        $to,
        $subject,
        $emailBody,
        'contato@jrtechnologysolutions.com.br',
        'Formulário Site',
        $email
    );
    
    if ($smtpResult['success']) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
    } else {
        // Se ambos falharam, salvar em arquivo como backup
        $logFile = __DIR__ . '/contatos.txt';
        $logEntry = date('Y-m-d H:i:s') . " | Nome: $name | Email: $email | Telefone: $phone | Empresa: $company | Mensagem: $message\n";
        $logEntry .= "Erro SMTP: " . ($smtpResult['error'] ?? 'Desconhecido') . "\n";
        $logEntry .= str_repeat('-', 80) . "\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem recebida! Entraremos em contato em breve.'
        ]);
    }
}
?>
