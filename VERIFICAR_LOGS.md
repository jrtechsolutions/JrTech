# 🔍 Como Verificar os Logs de Erro no Plesk

## ⚠️ IMPORTANTE

O erro 500 está acontecendo mesmo com código simplificado, o que indica problema na **configuração do servidor**, não no código PHP.

Os logs vão mostrar **exatamente** qual é o problema!

---

## 📋 Passo a Passo para Verificar Logs

### **PASSO 1: Acessar os Logs**

1. Faça login no **Plesk**
2. Selecione o domínio **`jrtechnologysolutions.com.br`**
3. No menu lateral, clique em **"Logs"** ou **"Registros"**
4. Clique em **"Error Log"** ou **"Log de Erros"**

---

### **PASSO 2: Procurar Erros Recentes**

1. Os logs são ordenados por data (mais recentes primeiro)
2. Procure por erros que aconteceram **agora** ou **há poucos minutos**
3. Procure por linhas que contenham:
   - `contact.php`
   - `contact-simple.php`
   - `mail()`
   - `sendmail`
   - `SMTP`
   - `500`

---

### **PASSO 3: Copiar o Erro Completo**

Quando encontrar um erro relacionado, copie **TUDO**:

**Exemplo de como o erro pode aparecer:**
```
[10-Dec-2025 12:37:45 UTC] PHP Warning: mail(): Failed to connect to mailserver at "localhost" port 25, verify your "SMTP" and "smtp_port" setting in php.ini or use ini_set() in /var/www/vhosts/jrtechnologysolutions.com.br/httpdocs/api/contact.php on line 54
```

**Ou:**
```
[10-Dec-2025 12:37:45 UTC] PHP Fatal error: Call to undefined function mail() in /var/www/vhosts/jrtechnologysolutions.com.br/httpdocs/api/contact.php on line 54
```

---

## 🔧 Erros Comuns e Soluções

### **Erro 1: "Failed to connect to mailserver"**

**Significa:** O servidor não consegue conectar ao servidor de email

**Solução:**
1. No Plesk, vá em **"PHP Settings"**
2. Procure por **"sendmail_path"**
3. Configure como: `/usr/sbin/sendmail -t -i`
4. Ou configure **"SMTP"** e **"smtp_port"**

---

### **Erro 2: "Call to undefined function mail()"**

**Significa:** A função mail() não está disponível

**Solução:**
1. No Plesk, vá em **"PHP Settings"**
2. Procure por extensões PHP desabilitadas
3. Habilite a extensão de email (se houver)

---

### **Erro 3: "sendmail_path" não configurado**

**Significa:** O caminho do sendmail não está definido

**Solução:**
1. No Plesk, vá em **"PHP Settings"**
2. Adicione ou edite: `sendmail_path = /usr/sbin/sendmail -t -i`
3. Salve e teste novamente

---

### **Erro 4: "550 Relaying denied"**

**Significa:** O servidor SMTP não permite relaying

**Solução:**
1. Use autenticação SMTP
2. Ou configure o servidor para permitir relaying local

---

## 📞 O Que Fazer Depois

1. **Copie o erro completo** dos logs
2. **Me envie aqui** para eu ajudar a resolver
3. **Ou siga as soluções** acima baseado no erro encontrado

---

## 🚀 Solução Temporária

Enquanto isso, você pode usar o arquivo **`contact-backup.php`** que:
- ✅ Salva os contatos em um arquivo `contatos.txt`
- ✅ Tenta enviar email também
- ✅ Não dá erro 500 mesmo se o email falhar

**Para usar:**
1. Faça upload do `contact-backup.php`
2. Renomeie para `contact.php`
3. Os contatos serão salvos em `api/contatos.txt`
4. Você pode verificar os contatos acessando esse arquivo (ou pelo File Manager)

---

## ✅ Próximos Passos

1. **Verifique os logs** seguindo os passos acima
2. **Copie o erro** e me envie
3. **Ou use o contact-backup.php** como solução temporária

Com essas informações, consigo te ajudar a resolver definitivamente!

