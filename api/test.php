<?php
/**
 * Arquivo de teste para diagnosticar problemas no servidor PHP
 * Acesse: https://jrtechnologysolutions.com.br/api/test.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste PHP - JR Technology Solutions</title>
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
    <h1>🔍 Teste de Configuração PHP</h1>
    
    <h2>Informações do PHP</h2>
    <pre>
Versão PHP: <span class="info"><?php echo phpversion(); ?></span>
Sistema Operacional: <?php echo PHP_OS; ?>
Servidor: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido'; ?>
    </pre>
    
    <h2>Funções Importantes</h2>
    <pre>
mail() disponível: <?php echo function_exists('mail') ? '<span class="success">✓ SIM</span>' : '<span class="error">✗ NÃO</span>'; ?>
json_decode() disponível: <?php echo function_exists('json_decode') ? '<span class="success">✓ SIM</span>' : '<span class="error">✗ NÃO</span>'; ?>
file_get_contents() disponível: <?php echo function_exists('file_get_contents') ? '<span class="success">✓ SIM</span>' : '<span class="error">✗ NÃO</span>'; ?>
    </pre>
    
    <h2>Teste de JSON</h2>
    <pre>
<?php
$testData = ['name' => 'Teste', 'email' => 'teste@teste.com', 'message' => 'Mensagem de teste'];
$json = json_encode($testData);
echo "JSON encode: " . ($json ? '<span class="success">✓ Funcionando</span>' : '<span class="error">✗ Erro</span>') . "\n";
echo "Resultado: " . htmlspecialchars($json) . "\n\n";

$decoded = json_decode($json, true);
echo "JSON decode: " . ($decoded ? '<span class="success">✓ Funcionando</span>' : '<span class="error">✗ Erro</span>') . "\n";
?>
    </pre>
    
    <h2>Teste de Sanitização</h2>
    <pre>
<?php
$testString = '<script>alert("test")</script>';
echo "Original: " . htmlspecialchars($testString) . "\n";
echo "Sanitizado: " . htmlspecialchars($testString, ENT_QUOTES, 'UTF-8') . "\n";
echo "Status: <span class='success'>✓ Funcionando</span>\n";
?>
    </pre>
    
    <h2>Permissões de Arquivo</h2>
    <pre>
Arquivo atual: <?php echo __FILE__; ?>
Permissões: <?php echo substr(sprintf('%o', fileperms(__FILE__)), -4); ?>
Legível: <?php echo is_readable(__FILE__) ? '<span class="success">✓ SIM</span>' : '<span class="error">✗ NÃO</span>'; ?>
Executável: <?php echo is_executable(__FILE__) ? '<span class="success">✓ SIM</span>' : '<span class="error">✗ NÃO</span>'; ?>
    </pre>
    
    <h2>Teste de Email (Simulação)</h2>
    <pre>
<?php
if (function_exists('mail')) {
    echo "<span class='info'>Função mail() está disponível</span>\n";
    echo "Para testar o envio real, use o formulário de contato.\n";
} else {
    echo "<span class='error'>Função mail() NÃO está disponível!</span>\n";
    echo "Você precisará usar SMTP ou configurar o servidor.\n";
}
?>
    </pre>
    
    <h2>Headers HTTP</h2>
    <pre>
<?php
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}
?>
    </pre>
    
    <hr>
    <p><strong>Próximos passos:</strong></p>
    <ul>
        <li>Se todas as funções estão disponíveis, o problema pode estar na configuração do email</li>
        <li>Verifique os logs de erro do PHP no Plesk</li>
        <li>Teste o arquivo contact.php diretamente com uma requisição POST</li>
    </ul>
</body>
</html>

