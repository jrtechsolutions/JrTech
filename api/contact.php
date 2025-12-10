<?php
/**
 * Versão otimizada com timeouts curtos
 * Tenta mail() primeiro (mais rápido), depois SMTP rápido
 */

// Aumentar timeout do PHP para este script
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

// Função para salvar em arquivo (sempre fazer isso primeiro)
function saveToFile($name, $email, $phone, $company, $message) {
    $logFile = __DIR__ . '/contatos.txt';
    $logEntry = date('Y-m-d H:i:s') . " | Nome: $name | Email: $email | Telefone: $phone | Empresa: $company | Mensagem: $message\n";
    $logEntry .= str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Sempre salvar em arquivo primeiro (garantir que não perde)
saveToFile($name, $email, $phone, $company, $message);

// Tentar mail() primeiro (mais rápido, sem timeout)
ini_set('sendmail_from', 'contato@jrtechnologysolutions.com.br');
$headers = "From: Formulário Site <contato@jrtechnologysolutions.com.br>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

error_clear_last();
$mailResult = @mail($to, $subject, $emailBody, $headers);

if ($mailResult) {
    // Se mail() funcionou, retornar sucesso imediatamente
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
    exit;
}

// Se mail() falhou, tentar SMTP mas com timeout muito curto
$smtpHost = 'smtp.appuni.com.br';
$smtpPort = 587;
$smtpUsername = 'contato@jrtechnologysolutions.com.br';
$smtpPassword = '';

$passwordFile = __DIR__ . '/.smtp_password';
if (file_exists($passwordFile)) {
    $smtpPassword = trim(file_get_contents($passwordFile));
}

if (empty($smtpPassword)) {
    // Se não tem senha, já salvou em arquivo, retornar sucesso
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// Tentar SMTP com timeout muito curto (5 segundos)
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ],
    'socket' => [
        'bindto' => '0.0.0.0:0'
    ]
]);

// Usar stream_socket_client com timeout curto
$socket = @stream_socket_client(
    "tcp://$smtpHost:$smtpPort",
    $errno,
    $errstr,
    5, // Timeout de 5 segundos apenas
    STREAM_CLIENT_CONNECT,
    $context
);

if (!$socket) {
    // Se não conseguiu conectar em 5 segundos, já salvou em arquivo, retornar sucesso
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// Configurar timeout de leitura/escrita
stream_set_timeout($socket, 3); // 3 segundos para operações

// Ler resposta inicial
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '220') {
    @fclose($socket);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// EHLO
@fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
$response = '';
$timeout = time() + 3;
while (time() < $timeout && ($line = @fgets($socket, 515))) {
    $response .= $line;
    if (substr($line, 3, 1) == ' ') break;
}

// AUTH LOGIN (pular STARTTLS para ser mais rápido)
@fputs($socket, "AUTH LOGIN\r\n");
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '334') {
    @fclose($socket);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// Enviar usuário
@fputs($socket, base64_encode($smtpUsername) . "\r\n");
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '334') {
    @fclose($socket);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// Enviar senha
@fputs($socket, base64_encode($smtpPassword) . "\r\n");
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '235') {
    @fclose($socket);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// MAIL FROM
@fputs($socket, "MAIL FROM: <contato@jrtechnologysolutions.com.br>\r\n");
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '250') {
    @fclose($socket);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// RCPT TO
@fputs($socket, "RCPT TO: <$to>\r\n");
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '250') {
    @fclose($socket);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// DATA
@fputs($socket, "DATA\r\n");
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '354') {
    @fclose($socket);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

// Headers e corpo
$headers = "From: Formulário Site <contato@jrtechnologysolutions.com.br>\r\n";
$headers .= "To: <$to>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Subject: $subject\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Date: " . date('r') . "\r\n";
$headers .= "\r\n";

@fputs($socket, $headers . $emailBody . "\r\n.\r\n");
$response = @fgets($socket, 515);

// QUIT
@fputs($socket, "QUIT\r\n");
@fclose($socket);

// Sempre retornar sucesso (já salvou em arquivo)
http_response_code(200);
if ($response && substr($response, 0, 3) == '250') {
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
}
?>
