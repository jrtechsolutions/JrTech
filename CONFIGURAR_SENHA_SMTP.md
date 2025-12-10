# 🔐 Como Configurar a Senha SMTP

## ⚠️ IMPORTANTE

O código precisa da **senha do email** `contato@jrtechnologysolutions.com.br` para enviar emails via SMTP autenticado.

---

## 🎯 Opções para Configurar a Senha

### **OPÇÃO 1: Arquivo .smtp_password (RECOMENDADO - Mais Seguro)**

1. **No Plesk File Manager, vá em `httpdocs/api/`**
2. **Crie um novo arquivo** chamado `.smtp_password` (com o ponto no início)
3. **Cole apenas a senha do email** (sem espaços, sem quebras de linha)
4. **Salve o arquivo**
5. **Configure as permissões** como `600` (apenas o dono pode ler/escrever)

**Exemplo do conteúdo do arquivo:**
```
minhasenha123
```
(apenas a senha, nada mais)

---

### **OPÇÃO 2: Editar contact.php Diretamente (Menos Seguro)**

1. **Abra o arquivo `contact.php`** no File Manager
2. **Encontre a linha:**
   ```php
   $smtpPassword = ''; // PREENCHA COM A SENHA DO EMAIL
   ```
3. **Substitua por:**
   ```php
   $smtpPassword = 'SUA_SENHA_AQUI';
   ```
4. **Salve o arquivo**

⚠️ **ATENÇÃO:** Esta opção é menos segura porque a senha fica visível no código!

---

### **OPÇÃO 3: Variável de Ambiente (Avançado)**

Se você tem acesso SSH ou pode configurar variáveis de ambiente no Plesk:

1. Configure a variável `SMTP_PASSWORD` com a senha
2. O código vai ler automaticamente

---

## 📋 Passo a Passo Recomendado (Opção 1)

### **1. Criar o Arquivo .smtp_password**

1. No Plesk File Manager: `httpdocs/api/`
2. Clique em **"New File"** (Novo Arquivo)
3. Nome: `.smtp_password` (com o ponto no início!)
4. Conteúdo: apenas a senha do email
5. Salve

### **2. Configurar Permissões**

1. Clique com botão direito no arquivo `.smtp_password`
2. **"Change Permissions"** (Alterar Permissões)
3. Configure como: `600` (rw-------)
4. Salve

### **3. Testar**

1. Acesse o site
2. Preencha o formulário
3. Envie
4. Verifique se recebeu o email!

---

## 🔒 Segurança

- ✅ **Opção 1 (arquivo .smtp_password):** Mais seguro, senha não fica no código
- ⚠️ **Opção 2 (no código):** Menos seguro, mas funciona
- ✅ **Opção 3 (variável de ambiente):** Mais seguro, mas requer acesso avançado

---

## 🎯 Qual Opção Usar?

**Recomendo a Opção 1** (arquivo `.smtp_password`):
- Mais seguro
- Fácil de configurar
- Senha não fica exposta no código

---

## ✅ Após Configurar

1. Faça upload do novo `contact.php` (se ainda não fez)
2. Crie o arquivo `.smtp_password` com a senha
3. Configure as permissões (600)
4. Teste o formulário
5. Verifique se recebeu o email!

---

## 🆘 Se Não Funcionar

Se mesmo com a senha configurada não funcionar:

1. Verifique se a senha está correta
2. Verifique se o arquivo `.smtp_password` está na pasta `api/`
3. Verifique as permissões do arquivo (600)
4. Veja o arquivo `contatos.txt` para ver o erro específico

Me envie o erro que aparece em `contatos.txt` que eu ajudo a resolver!

