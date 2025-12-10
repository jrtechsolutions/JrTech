# 📍 Caminho Exato para Ver Logs no Plesk

## 🎯 Caminho Completo Passo a Passo

### **MÉTODO 1: Via Interface Web do Plesk (Mais Fácil)**

1. **Faça login no Plesk**
   - Acesse: `https://seu-servidor-plesk.com:8443` (ou o endereço do seu Plesk)

2. **Selecione o Domínio**
   - Na lista de domínios, clique em **`jrtechnologysolutions.com.br`**

3. **Acesse a Seção de Logs**
   - No menu lateral esquerdo, procure por **"Logs"** ou **"Registros"**
   - Clique em **"Logs"**

4. **Abra o Error Log**
   - Dentro de "Logs", você verá várias opções:
     - **"Error Log"** ← **ESTE É O QUE VOCÊ PRECISA**
     - "Access Log"
     - "Mail Log"
     - etc.
   - Clique em **"Error Log"**

5. **Visualize os Erros**
   - Os erros mais recentes aparecem no topo
   - Procure por erros relacionados a `contact.php` ou `mail()`

---

### **MÉTODO 2: Via File Manager (Acesso Direto ao Arquivo)**

1. **No Plesk, vá em "File Manager"**
2. **Navegue até a pasta de logs:**
   ```
   /var/log/plesk-php82-fpm/
   ```
   ou
   ```
   /var/log/plesk-php83-fpm/
   ```
   (dependendo da versão do PHP)

3. **Procure pelo arquivo:**
   - `error_log`
   - Ou `php-fpm-error.log`

**OU**

1. **No File Manager, vá para:**
   ```
   /var/www/vhosts/jrtechnologysolutions.com.br/logs/
   ```

2. **Procure pelo arquivo:**
   - `error_log`
   - `php_errors.log`

---

### **MÉTODO 3: Via SSH (Se Tiver Acesso)**

Se você tem acesso SSH ao servidor:

1. **Conecte-se via SSH**
2. **Execute:**
   ```bash
   tail -f /var/log/plesk-php83-fpm/error_log
   ```
   (ajuste a versão do PHP conforme necessário)

3. **Ou para ver os últimos 100 erros:**
   ```bash
   tail -n 100 /var/log/plesk-php83-fpm/error_log | grep contact
   ```

---

## 🔍 Onde Procurar Especificamente

### **No Error Log, procure por:**

- `contact.php`
- `contact-simple.php`
- `mail()`
- `sendmail`
- `SMTP`
- `500`
- `Fatal error`
- `Warning`

### **Exemplo de como o erro pode aparecer:**

```
[10-Dec-2025 12:37:45 UTC] PHP Warning: mail(): Failed to connect to mailserver at "localhost" port 25, verify your "SMTP" and "smtp_port" setting in php.ini or use ini_set() in /var/www/vhosts/jrtechnologysolutions.com.br/httpdocs/api/contact.php on line 54
```

---

## 📋 Caminhos Comuns de Logs no Plesk

Dependendo da configuração do seu servidor, os logs podem estar em:

### **Linux (mais comum):**
```
/var/log/plesk-php82-fpm/error_log
/var/log/plesk-php83-fpm/error_log
/var/www/vhosts/jrtechnologysolutions.com.br/logs/error_log
/var/www/vhosts/jrtechnologysolutions.com.br/logs/php_errors.log
/var/log/apache2/error_log
/var/log/httpd/error_log
```

### **Windows:**
```
C:\inetpub\vhosts\jrtechnologysolutions.com.br\logs\error_log
C:\Program Files (x86)\Parallels\Plesk\Logs\
```

---

## 🎯 Passo a Passo Visual (Interface Web)

1. **Plesk Dashboard** → Clique em **"Domínios"**
2. **Lista de Domínios** → Clique em **"jrtechnologysolutions.com.br"**
3. **Menu Lateral** → Clique em **"Logs"** (ícone de arquivo de texto)
4. **Submenu** → Clique em **"Error Log"**
5. **Visualizar Logs** → Os erros aparecem em ordem cronológica (mais recentes primeiro)

---

## 💡 Dica Importante

- Os logs são atualizados em **tempo real**
- Após testar o formulário, **atualize a página** dos logs para ver o erro mais recente
- Os erros aparecem com **data e hora**, então você pode identificar facilmente qual é o mais recente

---

## 🆘 Se Não Encontrar os Logs

1. **Verifique se os logs estão habilitados:**
   - No Plesk: **"PHP Settings"** → Verifique se `log_errors` está como `On`

2. **Procure em outras localizações:**
   - **"Logs"** → **"Access Log"** (às vezes erros aparecem aqui também)
   - **"Logs"** → **"Mail Log"** (se o erro for relacionado a email)

3. **Contate o Suporte do Plesk:**
   - Eles podem te mostrar exatamente onde estão os logs no seu servidor específico

---

## ✅ Checklist Rápido

- [ ] Login no Plesk feito
- [ ] Domínio `jrtechnologysolutions.com.br` selecionado
- [ ] Menu "Logs" acessado
- [ ] "Error Log" aberto
- [ ] Erros recentes visualizados
- [ ] Erro relacionado a `contact.php` encontrado
- [ ] Erro completo copiado

---

## 📞 Próximo Passo

Depois de encontrar o erro nos logs:
1. **Copie o erro completo** (incluindo data/hora e caminho do arquivo)
2. **Me envie aqui** para eu ajudar a resolver
3. Ou **siga as soluções** baseadas no tipo de erro encontrado

