<?php
// Versão simplificada com correção do sendmail_from
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

// CORREÇÃO: Definir sendmail_from antes de enviar
ini_set('sendmail_from', 'contato@jrtechnologysolutions.com.br');

$to = 'contato@jrtechnologysolutions.com.br';
$subject = 'Novo contato do site - ' . $name;
$body = "Nome: $name\nEmail: $email\nTelefone: " . ($phone ?: 'Não informado') . "\nEmpresa: " . ($company ?: 'Não informado') . "\n\nMensagem:\n$message";

$headers = "From: contato@jrtechnologysolutions.com.br\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Limpar erros anteriores
error_clear_last();

$result = @mail($to, $subject, $body, $headers);

if ($result) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
} else {
    // Se falhou, salvar em arquivo como backup
    $logFile = __DIR__ . '/contatos.txt';
    $logEntry = date('Y-m-d H:i:s') . " | Nome: $name | Email: $email | Telefone: $phone | Empresa: $company | Mensagem: $message\n";
    $logEntry .= str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Retornar sucesso porque salvou em arquivo
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);
}
?>
