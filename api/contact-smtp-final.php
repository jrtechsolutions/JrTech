<?php
/**
 * Versão usando SMTP do próprio servidor Plesk
 * Mais confiável que a função mail()
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

// Receber dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Erro ao decodificar JSON']);
    exit;
}

if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
    exit;
}

$name = trim($data['name']);
$email = trim($data['email']);
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$company = isset($data['company']) ? trim($data['company']) : '';
$message = trim($data['message']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

// ============================================
// CONFIGURAÇÕES SMTP - PREENCHA COM SEUS DADOS
// ============================================
// No Plesk: Email > contato@jrtechnologysolutions.com.br > Configurações
// Ou use o servidor SMTP padrão do Plesk

$smtp_host = 'localhost'; // Ou 'mail.jrtechnologysolutions.com.br' ou '127.0.0.1'
$smtp_port = 25; // Porta padrão do servidor local (ou 587 para TLS, 465 para SSL)
$smtp_username = 'contato@jrtechnologysolutions.com.br';
$smtp_password = ''; // Deixe vazio se usar servidor local sem autenticação
$smtp_from = 'contato@jrtechnologysolutions.com.br';
$smtp_from_name = 'Formulário Site';

$to_email = 'contato@jrtechnologysolutions.com.br';
$to_name = 'JR Technology Solutions';

$subject = 'Novo contato do site - ' . $name;
$body = "Você recebeu uma nova mensagem do formulário de contato do site.\n\n";
$body .= "Nome: $name\n";
$body .= "Email: $email\n";
$body .= "Telefone: " . ($phone ?: 'Não informado') . "\n";
$body .= "Empresa: " . ($company ?: 'Não informado') . "\n\n";
$body .= "Mensagem:\n$message\n";

// Tentar usar socket SMTP direto (sem bibliotecas externas)
function sendSMTP($host, $port, $username, $password, $from, $fromName, $to, $toName, $subject, $body) {
    $socket = @fsockopen($host, $port, $errno, $errstr, 10);
    
    if (!$socket) {
        return ['success' => false, 'error' => "Não foi possível conectar ao servidor SMTP: $errstr ($errno)"];
    }
    
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '220') {
        fclose($socket);
        return ['success' => false, 'error' => "Servidor SMTP não respondeu corretamente: $response"];
    }
    
    // EHLO
    fputs($socket, "EHLO $host\r\n");
    $response = fgets($socket, 515);
    
    // Se precisar de autenticação
    if (!empty($username) && !empty($password)) {
        fputs($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 515);
        
        fputs($socket, base64_encode($username) . "\r\n");
        $response = fgets($socket, 515);
        
        fputs($socket, base64_encode($password) . "\r\n");
        $response = fgets($socket, 515);
        
        if (substr($response, 0, 3) != '235') {
            fclose($socket);
            return ['success' => false, 'error' => "Falha na autenticação SMTP: $response"];
        }
    }
    
    // MAIL FROM
    fputs($socket, "MAIL FROM: <$from>\r\n");
    $response = fgets($socket, 515);
    
    // RCPT TO
    fputs($socket, "RCPT TO: <$to>\r\n");
    $response = fgets($socket, 515);
    
    // DATA
    fputs($socket, "DATA\r\n");
    $response = fgets($socket, 515);
    
    // Headers e corpo
    $headers = "From: $fromName <$from>\r\n";
    $headers .= "To: $toName <$to>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "\r\n";
    
    fputs($socket, $headers . $body . "\r\n.\r\n");
    $response = fgets($socket, 515);
    
    // QUIT
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
    if (substr($response, 0, 3) == '250') {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => "Erro ao enviar email: $response"];
    }
}

// Tentar primeiro com função mail() (mais simples)
$mailSent = false;
$errorMsg = '';

if (function_exists('mail')) {
    $headers = "From: $smtp_from_name <$smtp_from>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $mailSent = @mail($to_email, $subject, $body, $headers);
    
    if (!$mailSent) {
        $error = error_get_last();
        $errorMsg = $error ? $error['message'] : 'Erro desconhecido';
    }
}

// Se mail() falhou, tentar SMTP direto
if (!$mailSent) {
    $smtpResult = sendSMTP(
        $smtp_host,
        $smtp_port,
        $smtp_username,
        $smtp_password,
        $smtp_from,
        $smtp_from_name,
        $to_email,
        $to_name,
        $subject,
        $body
    );
    
    if ($smtpResult['success']) {
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
        exit;
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao enviar mensagem',
            'debug' => [
                'mail_error' => $errorMsg,
                'smtp_error' => $smtpResult['error'] ?? 'Desconhecido'
            ]
        ]);
        exit;
    }
}

if ($mailSent) {
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar mensagem',
        'debug' => $errorMsg
    ]);
}
?>

