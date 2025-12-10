<?php
/**
 * Versão alternativa usando SMTP (mais confiável)
 * Use este arquivo se a função mail() não funcionar
 * 
 * INSTRUÇÕES:
 * 1. Renomeie este arquivo para contact.php (substituindo o anterior)
 * 2. Configure as credenciais SMTP abaixo com os dados do seu email no Plesk
 * 3. Descomente a linha que instala o PHPMailer (se necessário)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Permitir apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validar dados obrigatórios
if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
    exit;
}

// Sanitizar dados
$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$phone = isset($data['phone']) ? filter_var($data['phone'], FILTER_SANITIZE_STRING) : '';
$company = isset($data['company']) ? filter_var($data['company'], FILTER_SANITIZE_STRING) : '';
$message = filter_var($data['message'], FILTER_SANITIZE_STRING);

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

// ============================================
// CONFIGURAÇÕES SMTP - PREENCHA COM SEUS DADOS
// ============================================
// No Plesk, você encontra essas informações em:
// Email > contato@jrtechnologysolutions.com.br > Configurações

$smtp_host = 'mail.jrtechnologysolutions.com.br'; // ou o servidor SMTP do seu provedor
$smtp_port = 587; // 587 para TLS, 465 para SSL
$smtp_username = 'contato@jrtechnologysolutions.com.br';
$smtp_password = 'SUA_SENHA_AQUI'; // Senha do email
$smtp_secure = 'tls'; // 'tls' ou 'ssl'

// Email de destino
$to_email = 'contato@jrtechnologysolutions.com.br';
$to_name = 'JR Technology Solutions';

// ============================================
// CÓDIGO COM PHPMailer (Recomendado)
// ============================================
// Se o Plesk não tiver PHPMailer instalado, você pode usar a função mail() abaixo
// ou instalar via Composer no Plesk

// Tentar usar PHPMailer se disponível
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require_once 'PHPMailer/src/Exception.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;
        $mail->CharSet = 'UTF-8';
        
        // Remetente e destinatário
        $mail->setFrom($smtp_username, 'Formulário Site');
        $mail->addAddress($to_email, $to_name);
        $mail->addReplyTo($email, $name);
        
        // Conteúdo do email
        $mail->isHTML(false);
        $mail->Subject = 'Novo contato do site - ' . $name;
        $mail->Body = "Você recebeu uma nova mensagem do formulário de contato do site.\n\n";
        $mail->Body .= "Nome: " . $name . "\n";
        $mail->Body .= "Email: " . $email . "\n";
        $mail->Body .= "Telefone: " . ($phone ? $phone : 'Não informado') . "\n";
        $mail->Body .= "Empresa: " . ($company ? $company : 'Não informado') . "\n\n";
        $mail->Body .= "Mensagem:\n" . $message . "\n";
        
        $mail->send();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso!'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao enviar mensagem: ' . $mail->ErrorInfo
        ]);
    }
} else {
    // Fallback para função mail() se PHPMailer não estiver disponível
    $to = $to_email;
    $subject = 'Novo contato do site - ' . $name;
    $emailBody = "Você recebeu uma nova mensagem do formulário de contato do site.\n\n";
    $emailBody .= "Nome: " . $name . "\n";
    $emailBody .= "Email: " . $email . "\n";
    $emailBody .= "Telefone: " . ($phone ? $phone : 'Não informado') . "\n";
    $emailBody .= "Empresa: " . ($company ? $company : 'Não informado') . "\n\n";
    $emailBody .= "Mensagem:\n" . $message . "\n";
    
    $headers = "From: Formulário Site <" . $smtp_username . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    $mailSent = mail($to, $subject, $emailBody, $headers);
    
    if ($mailSent) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso!'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.'
        ]);
    }
}
?>

