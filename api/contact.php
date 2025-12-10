<?php
// Habilitar exibição de erros para debug (remover em produção)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder a requisições OPTIONS (preflight)
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

try {
    // Receber dados JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Verificar se o JSON foi decodificado corretamente
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Erro ao processar dados JSON: ' . json_last_error_msg());
    }
    
    // Validar dados obrigatórios
    if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Campos obrigatórios não preenchidos (nome, email, mensagem)'
        ]);
        exit;
    }
    
    // Sanitizar dados (usando htmlspecialchars para compatibilidade com PHP 8+)
    $name = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $phone = isset($data['phone']) ? htmlspecialchars(trim($data['phone']), ENT_QUOTES, 'UTF-8') : '';
    $company = isset($data['company']) ? htmlspecialchars(trim($data['company']), ENT_QUOTES, 'UTF-8') : '';
    $message = htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8');
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit;
    }
    
    // Configurações do email
    $to = 'contato@jrtechnologysolutions.com.br';
    $subject = 'Novo contato do site - ' . $name;
    
    // Montar corpo do email
    $emailBody = "Você recebeu uma nova mensagem do formulário de contato do site.\n\n";
    $emailBody .= "Nome: " . $name . "\n";
    $emailBody .= "Email: " . $email . "\n";
    $emailBody .= "Telefone: " . ($phone ? $phone : 'Não informado') . "\n";
    $emailBody .= "Empresa: " . ($company ? $company : 'Não informado') . "\n\n";
    $emailBody .= "Mensagem:\n" . $message . "\n";
    
    // Headers do email
    $headers = "From: Formulário Site <contato@jrtechnologysolutions.com.br>\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Tentar enviar o email
    $mailSent = @mail($to, $subject, $emailBody, $headers);
    
    if ($mailSent) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso!'
        ]);
    } else {
        // Verificar se a função mail() está disponível
        if (!function_exists('mail')) {
            throw new Exception('Função mail() não está disponível no servidor');
        }
        
        // Obter último erro do PHP
        $error = error_get_last();
        $errorMsg = $error ? $error['message'] : 'Erro desconhecido ao enviar email';
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.',
            'debug' => $errorMsg // Remover em produção
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar solicitação: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro fatal: ' . $e->getMessage()
    ]);
}
?>
