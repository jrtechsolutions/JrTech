<?php
/**
 * Versão com logs detalhados e tentativas mais robustas
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

// Função para salvar em arquivo com log de tentativas
function saveToFile($name, $email, $phone, $company, $message, $logInfo = '') {
    $logFile = __DIR__ . '/contatos.txt';
    $logEntry = date('Y-m-d H:i:s') . " | Nome: $name | Email: $email | Telefone: $phone | Empresa: $company | Mensagem: $message\n";
    if ($logInfo) {
        $logEntry .= "Log: $logInfo\n";
    }
    $logEntry .= str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Sempre salvar primeiro
saveToFile($name, $email, $phone, $company, $message, 'Iniciando tentativas de envio');

// Tentar mail() primeiro
ini_set('sendmail_from', 'contato@jrtechnologysolutions.com.br');
$headers = "From: Formulário Site <contato@jrtechnologysolutions.com.br>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

error_clear_last();
$mailResult = @mail($to, $subject, $emailBody, $headers);
$mailError = error_get_last();

if ($mailResult) {
    saveToFile($name, $email, $phone, $company, $message, 'mail() retornou TRUE - email enviado com sucesso');
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
    exit;
} else {
    $mailErrorMsg = $mailError ? $mailError['message'] : 'mail() retornou FALSE sem erro específico';
    saveToFile($name, $email, $phone, $company, $message, "mail() falhou: $mailErrorMsg");
}

// Se mail() falhou, tentar SMTP
$smtpHost = 'smtp.appuni.com.br';
$smtpPort = 587;
$smtpUsername = 'contato@jrtechnologysolutions.com.br';
$smtpPassword = '';

$passwordFile = __DIR__ . '/.smtp_password';
if (file_exists($passwordFile)) {
    $smtpPassword = trim(file_get_contents($passwordFile));
    if (!empty($smtpPassword)) {
        saveToFile($name, $email, $phone, $company, $message, 'Senha SMTP encontrada no arquivo');
    } else {
        saveToFile($name, $email, $phone, $company, $message, 'Arquivo .smtp_password existe mas está vazio');
    }
} else {
    saveToFile($name, $email, $phone, $company, $message, 'Arquivo .smtp_password não encontrado - email não será enviado via SMTP');
}

if (empty($smtpPassword)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve. (Email não configurado)'
    ]);
    exit;
}

// Tentar SMTP com timeout curto
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

$socket = @stream_socket_client(
    "tcp://$smtpHost:$smtpPort",
    $errno,
    $errstr,
    5,
    STREAM_CLIENT_CONNECT,
    $context
);

if (!$socket) {
    saveToFile($name, $email, $phone, $company, $message, "SMTP: Não conseguiu conectar em 5s - $errstr ($errno)");
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
    exit;
}

stream_set_timeout($socket, 3);

// Ler resposta inicial
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '220') {
    @fclose($socket);
    saveToFile($name, $email, $phone, $company, $message, "SMTP: Resposta inicial inválida - $response");
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

// AUTH LOGIN
@fputs($socket, "AUTH LOGIN\r\n");
$response = @fgets($socket, 515);
if (!$response || substr($response, 0, 3) != '334') {
    @fclose($socket);
    saveToFile($name, $email, $phone, $company, $message, "SMTP: AUTH LOGIN falhou - $response");
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
    saveToFile($name, $email, $phone, $company, $message, "SMTP: Usuário rejeitado - $response");
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
    saveToFile($name, $email, $phone, $company, $message, "SMTP: Autenticação falhou - $response");
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
    saveToFile($name, $email, $phone, $company, $message, "SMTP: MAIL FROM falhou - $response");
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
    saveToFile($name, $email, $phone, $company, $message, "SMTP: RCPT TO falhou - $response");
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
    saveToFile($name, $email, $phone, $company, $message, "SMTP: DATA falhou - $response");
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

// Verificar resultado
if ($response && substr($response, 0, 3) == '250') {
    saveToFile($name, $email, $phone, $company, $message, "SMTP: Email enviado com sucesso - $response");
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
} else {
    saveToFile($name, $email, $phone, $company, $message, "SMTP: Envio falhou no final - $response");
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
}
?>
