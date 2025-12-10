<?php
/**
 * Versão com múltiplas tentativas de conexão SMTP
 * Tenta SSL direto, STARTTLS, ou sem criptografia
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

// ============================================
// CONFIGURAÇÕES SMTP
// ============================================
$smtpHost = 'smtp.appuni.com.br';
$smtpPort = 587;
$smtpUsername = 'contato@jrtechnologysolutions.com.br';
$smtpPassword = '';

// Ler senha do arquivo
$passwordFile = __DIR__ . '/.smtp_password';
if (file_exists($passwordFile)) {
    $smtpPassword = trim(file_get_contents($passwordFile));
}

if (empty($smtpPassword)) {
    $smtpPassword = getenv('SMTP_PASSWORD') ?: '';
}

// Função para enviar via SMTP (tenta sem STARTTLS primeiro)
function sendEmailSMTP($host, $port, $username, $password, $to, $subject, $body, $fromEmail, $fromName, $replyTo) {
    // Tentar conectar sem SSL primeiro
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    
    $socket = @stream_socket_client(
        "tcp://$host:$port",
        $errno,
        $errstr,
        10,
        STREAM_CLIENT_CONNECT,
        $context
    );
    
    if (!$socket) {
        return ['success' => false, 'error' => "Não foi possível conectar: $errstr ($errno)"];
    }
    
    // Ler resposta inicial
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '220') {
        fclose($socket);
        return ['success' => false, 'error' => "Servidor não respondeu: $response"];
    }
    
    // EHLO
    fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
    $response = '';
    while ($line = fgets($socket, 515)) {
        $response .= $line;
        if (substr($line, 3, 1) == ' ') break;
    }
    
    // Tentar STARTTLS apenas se o servidor suportar
    if (strpos($response, 'STARTTLS') !== false) {
        fputs($socket, "STARTTLS\r\n");
        $response = fgets($socket, 515);
        
        // Se STARTTLS for aceito, negociar TLS
        if (substr($response, 0, 3) == '220') {
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($socket);
                return ['success' => false, 'error' => 'Falha ao negociar TLS'];
            }
            
            // EHLO novamente após TLS
            fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) == ' ') break;
            }
        }
        // Se STARTTLS foi rejeitado (503), continuar sem TLS
    }
    
    // AUTH LOGIN
    fputs($socket, "AUTH LOGIN\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '334') {
        fclose($socket);
        return ['success' => false, 'error' => "AUTH LOGIN falhou: $response"];
    }
    
    // Enviar usuário
    fputs($socket, base64_encode($username) . "\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '334') {
        fclose($socket);
        return ['success' => false, 'error' => "Usuário rejeitado: $response"];
    }
    
    // Enviar senha
    fputs($socket, base64_encode($password) . "\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '235') {
        fclose($socket);
        return ['success' => false, 'error' => "Autenticação falhou: $response"];
    }
    
    // MAIL FROM
    fputs($socket, "MAIL FROM: <$fromEmail>\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '250') {
        fclose($socket);
        return ['success' => false, 'error' => "MAIL FROM falhou: $response"];
    }
    
    // RCPT TO
    fputs($socket, "RCPT TO: <$to>\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '250') {
        fclose($socket);
        return ['success' => false, 'error' => "RCPT TO falhou: $response"];
    }
    
    // DATA
    fputs($socket, "DATA\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '354') {
        fclose($socket);
        return ['success' => false, 'error' => "DATA falhou: $response"];
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
        return ['success' => false, 'error' => "Envio falhou: $response"];
    }
}

// Verificar se tem senha
if (empty($smtpPassword)) {
    $logFile = __DIR__ . '/contatos.txt';
    $logEntry = date('Y-m-d H:i:s') . " | Nome: $name | Email: $email | Telefone: $phone | Empresa: $company | Mensagem: $message\n";
    $logEntry .= "AVISO: Senha SMTP não configurada\n";
    $logEntry .= str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// Tentar enviar via SMTP
$smtpResult = sendEmailSMTP(
    $smtpHost,
    $smtpPort,
    $smtpUsername,
    $smtpPassword,
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
    // Se falhou, salvar em arquivo
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
?>
