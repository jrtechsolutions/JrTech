<?php
// Configuração de erros
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers - devem ser enviados antes de qualquer output
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder a requisições OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Permitir apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Função para enviar resposta de erro
function sendError($message, $code = 500, $debug = null) {
    http_response_code($code);
    $response = ['success' => false, 'message' => $message];
    if ($debug !== null) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    exit;
}

// Função para enviar resposta de sucesso
function sendSuccess($message) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => $message]);
    exit;
}

try {
    // Receber dados JSON
    $input = @file_get_contents('php://input');
    
    if ($input === false || empty($input)) {
        sendError('Nenhum dado recebido', 400);
    }
    
    $data = @json_decode($input, true);
    
    // Verificar se o JSON foi decodificado corretamente
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError('Erro ao processar JSON: ' . json_last_error_msg(), 400);
    }
    
    // Validar dados obrigatórios
    if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
        sendError('Campos obrigatórios não preenchidos (nome, email, mensagem)', 400);
    }
    
    // Sanitizar e validar dados
    $name = trim($data['name']);
    $email = trim($data['email']);
    $phone = isset($data['phone']) ? trim($data['phone']) : '';
    $company = isset($data['company']) ? trim($data['company']) : '';
    $message = trim($data['message']);
    
    // Validar tamanhos
    if (strlen($name) > 200 || strlen($email) > 200 || strlen($message) > 5000) {
        sendError('Dados muito longos', 400);
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Email inválido', 400);
    }
    
    // Sanitizar para HTML (prevenir XSS)
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $company = htmlspecialchars($company, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Configurações do email
    $to = 'contato@jrtechnologysolutions.com.br';
    $subject = 'Novo contato do site - ' . $name;
    
    // Montar corpo do email (texto simples, sem HTML)
    $emailBody = "Você recebeu uma nova mensagem do formulário de contato do site.\n\n";
    $emailBody .= "Nome: " . $name . "\n";
    $emailBody .= "Email: " . $email . "\n";
    $emailBody .= "Telefone: " . ($phone ? $phone : 'Não informado') . "\n";
    $emailBody .= "Empresa: " . ($company ? $company : 'Não informado') . "\n\n";
    $emailBody .= "Mensagem:\n" . $message . "\n";
    
    // Headers do email
    $headers = array();
    $headers[] = "From: Formulário Site <contato@jrtechnologysolutions.com.br>";
    $headers[] = "Reply-To: " . $email;
    $headers[] = "Content-Type: text/plain; charset=UTF-8";
    $headers[] = "X-Mailer: PHP/" . phpversion();
    $headersString = implode("\r\n", $headers);
    
    // Verificar se a função mail() está disponível
    if (!function_exists('mail')) {
        sendError('Função mail() não está disponível no servidor', 500);
    }
    
    // Tentar enviar o email
    $mailSent = @mail($to, $subject, $emailBody, $headersString);
    
    if ($mailSent) {
        sendSuccess('Mensagem enviada com sucesso!');
    } else {
        // Obter último erro do PHP
        $error = error_get_last();
        $errorMsg = 'Erro desconhecido ao enviar email';
        
        if ($error && isset($error['message'])) {
            $errorMsg = $error['message'];
        }
        
        // Log do erro (não expor detalhes ao usuário em produção)
        error_log("Erro ao enviar email: " . $errorMsg);
        
        sendError('Erro ao enviar mensagem. Tente novamente mais tarde.', 500);
    }
    
} catch (Throwable $e) {
    // Captura tanto Exception quanto Error (PHP 7+)
    error_log("Erro no contact.php: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
    sendError('Erro ao processar solicitação', 500);
}
?>
