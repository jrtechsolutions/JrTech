# 📊 Diferença Entre Access Log e Error Log

## ⚠️ O Que Você Viu

Você está vendo o **Access Log** (Log de Acesso), que mostra:
- ✅ Quem acessou o site
- ✅ Quais arquivos foram solicitados
- ✅ Códigos de status HTTP (200, 404, 500, etc.)

**MAS NÃO mostra:**
- ❌ O erro PHP detalhado
- ❌ Mensagens de erro do servidor
- ❌ O que causou o erro 500

---

## 🎯 O Que Precisamos

Precisamos do **Error Log** (Log de Erros), que mostra:
- ✅ Erros PHP detalhados
- ✅ Mensagens de erro completas
- ✅ Linha exata do erro
- ✅ Stack trace completo

---

## 📍 Como Encontrar o Error Log

### **No Plesk:**

1. **Você está em "Logs" → "Access Log"** (é isso que você viu)
2. **Procure por "Error Log"** na mesma seção de Logs
3. **Clique em "Error Log"** (não "Access Log")

### **Ou:**

1. **No menu lateral, procure por:**
   - "Error Log"
   - "PHP Error Log"
   - "Error Logs"
   - "Logs de Erro"

---

## 🔍 O Que Procurar no Error Log

Procure por linhas que contenham:
- `contact.php`
- `contact-simple.php`
- `mail()`
- `Fatal error`
- `Warning`
- `Parse error`

**Exemplo do que você deve encontrar:**

```
[10-Dec-2025 12:38:58 UTC] PHP Warning: mail(): Failed to connect to mailserver at "localhost" port 25, verify your "SMTP" and "smtp_port" setting in php.ini or use ini_set() in /var/www/vhosts/jrtechnologysolutions.com.br/httpdocs/api/contact.php on line 54
```

---

## 🚀 Solução Alternativa - Ver Erro Direto

Se não conseguir encontrar o Error Log, podemos criar um arquivo que mostra o erro diretamente:

1. **Crie um arquivo `api/error-test.php`** com este conteúdo:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Simular o que o contact.php faz
$to = 'contato@jrtechnologysolutions.com.br';
$subject = 'Teste';
$body = 'Teste de email';
$headers = "From: contato@jrtechnologysolutions.com.br\r\n";

echo "Testando função mail()...<br>";
echo "Função existe: " . (function_exists('mail') ? 'SIM' : 'NÃO') . "<br>";

$result = @mail($to, $subject, $body, $headers);
echo "Resultado: " . ($result ? 'SUCESSO' : 'FALHOU') . "<br>";

$error = error_get_last();
if ($error) {
    echo "Último erro: " . $error['message'] . "<br>";
    echo "Arquivo: " . $error['file'] . "<br>";
    echo "Linha: " . $error['line'] . "<br>";
}
?>
```

2. **Acesse:** `https://www.jrtechnologysolutions.com.br/api/error-test.php`
3. **Veja o erro diretamente na tela**

---

## 📋 Checklist

- [ ] Acessou "Error Log" (não "Access Log")
- [ ] Procurou por erros com data/hora recente (12:38, 12:39, 12:46, 12:47, 12:51, 12:53, 12:54)
- [ ] Procurou por `contact.php` ou `mail()`
- [ ] Copiou o erro completo

---

## 🆘 Se Ainda Não Encontrar

Use a solução alternativa acima (`error-test.php`) que vai mostrar o erro diretamente na tela do navegador!

