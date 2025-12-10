<?php
/**
 * Versão que tenta múltiplas portas e métodos SMTP
 */

set_time_limit(30);
ini_set('max_execution_time', 30);

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

function saveToFile($name, $email, $phone, $company, $message, $logInfo = '') {
    $logFile = __DIR__ . '/contatos.txt';
    $logEntry = date('Y-m-d H:i:s') . " | Nome: $name | Email: $email | Telefone: $phone | Empresa: $company | Mensagem: $message\n";
    if ($logInfo) {
        $logEntry .= "Log: $logInfo\n";
    }
    $logEntry .= str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

saveToFile($name, $email, $phone, $company, $message, 'Iniciando tentativas de envio');

// Tentar mail() primeiro
ini_set('sendmail_from', 'contato@jrtechnologysolutions.com.br');
$headers = "From: Formulário Site <contato@jrtechnologysolutions.com.br>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

error_clear_last();
$mailResult = @mail($to, $subject, $emailBody, $headers);

if ($mailResult) {
    saveToFile($name, $email, $phone, $company, $message, 'mail() retornou TRUE - email enviado com sucesso');
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
    exit;
}

$smtpHost = 'smtp.appuni.com.br';
$smtpUsername = 'contato@jrtechnologysolutions.com.br';
$smtpPassword = '';

$passwordFile = __DIR__ . '/.smtp_password';
if (file_exists($passwordFile)) {
    $smtpPassword = trim(file_get_contents($passwordFile));
}

if (empty($smtpPassword)) {
    saveToFile($name, $email, $phone, $company, $message, 'Senha SMTP não configurada');
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// Função para tentar enviar via SMTP em uma porta específica
function trySMTP($host, $port, $username, $password, $to, $subject, $body, $fromEmail, $fromName, $replyTo, $useSSL = false) {
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    
    // Tentar conectar
    $socket = @stream_socket_client(
        ($useSSL ? "ssl://" : "tcp://") . "$host:$port",
        $errno,
        $errstr,
        5,
        STREAM_CLIENT_CONNECT,
        $context
    );
    
    if (!$socket) {
        return ['success' => false, 'error' => "Não conectou: $errstr ($errno)"];
    }
    
    stream_set_timeout($socket, 3);
    
    // Ler resposta inicial
    $response = @fgets($socket, 515);
    if (!$response) {
        @fclose($socket);
        return ['success' => false, 'error' => 'Sem resposta do servidor'];
    }
    
    // Verificar código de resposta (pode ser 220 ou outros)
    $code = substr($response, 0, 3);
    if ($code != '220' && $code != '250') {
        @fclose($socket);
        return ['success' => false, 'error' => "Resposta inicial: $response"];
    }
    
    // Se não for SSL direto, fazer EHLO
    if (!$useSSL) {
        @fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $response = '';
        $timeout = time() + 3;
        while (time() < $timeout && ($line = @fgets($socket, 515))) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
    } else {
        // Se for SSL, fazer EHLO direto
        @fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $response = '';
        $timeout = time() + 3;
        while (time() < $timeout && ($line = @fgets($socket, 515))) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
    }
    
    // AUTH LOGIN
    @fputs($socket, "AUTH LOGIN\r\n");
    $response = @fgets($socket, 515);
    if (!$response || substr($response, 0, 3) != '334') {
        @fclose($socket);
        return ['success' => false, 'error' => "AUTH LOGIN: $response"];
    }
    
    // Usuário
    @fputs($socket, base64_encode($username) . "\r\n");
    $response = @fgets($socket, 515);
    if (!$response || substr($response, 0, 3) != '334') {
        @fclose($socket);
        return ['success' => false, 'error' => "Usuário: $response"];
    }
    
    // Senha
    @fputs($socket, base64_encode($password) . "\r\n");
    $response = @fgets($socket, 515);
    if (!$response || substr($response, 0, 3) != '235') {
        @fclose($socket);
        return ['success' => false, 'error' => "Autenticação: $response"];
    }
    
    // MAIL FROM
    @fputs($socket, "MAIL FROM: <$fromEmail>\r\n");
    $response = @fgets($socket, 515);
    if (!$response || substr($response, 0, 3) != '250') {
        @fclose($socket);
        return ['success' => false, 'error' => "MAIL FROM: $response"];
    }
    
    // RCPT TO
    @fputs($socket, "RCPT TO: <$to>\r\n");
    $response = @fgets($socket, 515);
    if (!$response || substr($response, 0, 3) != '250') {
        @fclose($socket);
        return ['success' => false, 'error' => "RCPT TO: $response"];
    }
    
    // DATA
    @fputs($socket, "DATA\r\n");
    $response = @fgets($socket, 515);
    if (!$response || substr($response, 0, 3) != '354') {
        @fclose($socket);
        return ['success' => false, 'error' => "DATA: $response"];
    }
    
    // Headers e corpo
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "To: <$to>\r\n";
    $headers .= "Reply-To: $replyTo\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Date: " . date('r') . "\r\n";
    $headers .= "\r\n";
    
    @fputs($socket, $headers . $body . "\r\n.\r\n");
    $response = @fgets($socket, 515);
    
    @fputs($socket, "QUIT\r\n");
    @fclose($socket);
    
    if ($response && substr($response, 0, 3) == '250') {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => "Envio: $response"];
    }
}

// Tentar múltiplas portas e métodos
$attempts = [
    ['port' => 465, 'ssl' => true, 'name' => 'Porta 465 com SSL'],
    ['port' => 587, 'ssl' => false, 'name' => 'Porta 587 sem SSL'],
    ['port' => 25, 'ssl' => false, 'name' => 'Porta 25 sem SSL'],
];

$lastError = '';

foreach ($attempts as $attempt) {
    saveToFile($name, $email, $phone, $company, $message, "Tentando: {$attempt['name']}");
    
    $result = trySMTP(
        $smtpHost,
        $attempt['port'],
        $smtpUsername,
        $smtpPassword,
        $to,
        $subject,
        $emailBody,
        'contato@jrtechnologysolutions.com.br',
        'Formulário Site',
        $email,
        $attempt['ssl']
    );
    
    if ($result['success']) {
        saveToFile($name, $email, $phone, $company, $message, "SUCESSO via {$attempt['name']}!");
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
        exit;
    } else {
        $lastError = $result['error'];
        saveToFile($name, $email, $phone, $company, $message, "{$attempt['name']} falhou: {$result['error']}");
    }
}

// Se todas as tentativas falharam
saveToFile($name, $email, $phone, $company, $message, "Todas tentativas falharam. Último erro: $lastError");
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Mensagem recebida! Entraremos em contato em breve.'
]);
?>
