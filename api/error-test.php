<?php
/**
 * Arquivo para testar e mostrar erros diretamente
 * Acesse: https://www.jrtechnologysolutions.com.br/api/error-test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Erro - Email</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1e1e1e; color: #fff; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .info { color: #60a5fa; }
        pre { background: #2d2d2d; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h2 { border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🔍 Teste de Erro - Função mail()</h1>
    
    <h2>Informações</h2>
    <pre>
PHP Version: <?php echo phpversion(); ?>
Função mail() existe: <?php echo function_exists('mail') ? '<span class="success">✓ SIM</span>' : '<span class="error">✗ NÃO</span>'; ?>
    </pre>
    
    <h2>Teste de Envio de Email</h2>
    <pre>
<?php
$to = 'contato@jrtechnologysolutions.com.br';
$subject = 'Teste de Email';
$body = 'Este é um teste de envio de email';
$headers = "From: contato@jrtechnologysolutions.com.br\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

echo "Tentando enviar email...\n";
echo "Para: $to\n";
echo "Assunto: $subject\n\n";

// Limpar erros anteriores
error_clear_last();

// Tentar enviar
$result = @mail($to, $subject, $body, $headers);

echo "Resultado do mail(): " . ($result ? '<span class="success">TRUE (sucesso)</span>' : '<span class="error">FALSE (falhou)</span>') . "\n\n";

// Verificar erros
$error = error_get_last();
if ($error) {
    echo "<span class='error'>ERRO ENCONTRADO:</span>\n";
    echo "Tipo: " . $error['type'] . "\n";
    echo "Mensagem: " . $error['message'] . "\n";
    echo "Arquivo: " . $error['file'] . "\n";
    echo "Linha: " . $error['line'] . "\n";
} else {
    echo "<span class='success'>Nenhum erro PHP detectado</span>\n";
    if (!$result) {
        echo "<span class='error'>Mas mail() retornou FALSE - pode ser problema de configuração do servidor</span>\n";
    }
}
?>
    </pre>
    
    <h2>Configurações PHP Relacionadas</h2>
    <pre>
<?php
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";
echo "sendmail_from: " . ini_get('sendmail_from') . "\n";
?>
    </pre>
    
    <h2>Próximos Passos</h2>
    <p>Se aparecer um erro acima, <strong>copie tudo</strong> e me envie para eu ajudar a resolver!</p>
</body>
</html>

