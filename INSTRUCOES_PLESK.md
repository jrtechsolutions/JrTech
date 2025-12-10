# 📧 Instruções Completas - Configurar Formulário de Contato no Plesk

Este guia vai te ajudar a configurar o formulário de contato para enviar emails através do servidor Plesk.

---

## 📋 Pré-requisitos

- Acesso ao painel Plesk
- Email `contato@jrtechnologysolutions.com.br` já configurado no Plesk
- Acesso via FTP ou File Manager do Plesk

---

## 🚀 Passo a Passo Completo

### **PASSO 1: Fazer Build do Projeto React**

1. Abra o terminal na pasta do projeto (`C:\JrTech`)
2. Execute o comando para fazer o build:
   ```bash
   npm run build
   ```
3. Isso vai criar uma pasta `dist` com os arquivos prontos para produção

---

### **PASSO 2: Acessar o File Manager no Plesk**

1. Faça login no painel Plesk
2. Selecione o domínio `jrtechnologysolutions.com.br`
3. Clique em **"File Manager"** (Gerenciador de Arquivos) no menu lateral
4. Navegue até a pasta `httpdocs` (esta é a pasta raiz do seu site)

---

### **PASSO 3: Fazer Upload dos Arquivos do Build**

1. **Opção A - Via File Manager:**
   - No File Manager do Plesk, clique em **"Upload"**
   - Selecione todos os arquivos da pasta `dist` do seu projeto
   - Faça upload de todos os arquivos

2. **Opção B - Via FTP:**
   - Use um cliente FTP (FileZilla, WinSCP, etc.)
   - Conecte-se ao servidor usando as credenciais do Plesk
   - Navegue até a pasta `httpdocs`
   - Faça upload de todos os arquivos da pasta `dist`

**⚠️ IMPORTANTE:** Se já existirem arquivos antigos, você pode substituí-los ou fazer backup antes.

---

### **PASSO 4: Criar a Pasta API e Fazer Upload do Arquivo PHP**

1. No File Manager do Plesk, dentro da pasta `httpdocs`:
   - Clique em **"New Folder"** (Nova Pasta)
   - Nomeie a pasta como: `api`
   - Entre na pasta `api`

2. Dentro da pasta `api`:
   - Clique em **"Upload"**
   - Faça upload do arquivo `contact.php` que está na pasta `api` do seu projeto local
   - Ou crie um novo arquivo chamado `contact.php` e cole o conteúdo do arquivo

---

### **PASSO 5: Verificar Permissões do Arquivo PHP**

1. No File Manager, encontre o arquivo `contact.php` dentro da pasta `api`
2. Clique com o botão direito no arquivo e selecione **"Change Permissions"** (Alterar Permissões)
3. Configure as permissões como: `644` ou `755`
4. Salve as alterações

---

### **PASSO 6: Configurar o Vite para Produção**

Para que o formulário funcione corretamente, precisamos garantir que o caminho da API esteja correto. O arquivo `contact.php` já está configurado para aceitar requisições do mesmo domínio.

**Se o formulário não funcionar**, você pode precisar ajustar o caminho no arquivo `ContactSection.tsx`:

- Se o site estiver na raiz: `/api/contact.php` (já está assim)
- Se o site estiver em uma subpasta: `/subpasta/api/contact.php`

---

### **PASSO 7: Testar o Formulário**

1. Acesse seu site: `https://jrtechnologysolutions.com.br`
2. Vá até a seção de contato
3. Preencha o formulário com dados de teste
4. Clique em "Enviar Mensagem"
5. Verifique se aparece a mensagem de sucesso
6. Verifique a caixa de entrada do email `contato@jrtechnologysolutions.com.br`

---

## 🔧 Solução de Problemas

### **Problema: Email não está sendo enviado**

**Solução 1 - Verificar configuração PHP:**
1. No Plesk, vá em **"PHP Settings"**
2. Verifique se a função `mail()` está habilitada
3. Se não estiver, habilite-a

**Solução 2 - Verificar logs:**
1. No Plesk, vá em **"Logs"**
2. Verifique os logs de erro do PHP
3. Procure por erros relacionados ao `mail()`

**Solução 3 - Usar SMTP (Alternativa mais confiável):**
Se a função `mail()` não funcionar, podemos configurar SMTP. Entre em contato para configurarmos isso.

---

### **Problema: Erro 404 ao enviar formulário**

**Solução:**
1. Verifique se a pasta `api` existe dentro de `httpdocs`
2. Verifique se o arquivo `contact.php` está dentro da pasta `api`
3. Verifique se o caminho no código está correto: `/api/contact.php`

---

### **Problema: Erro de CORS**

**Solução:**
O arquivo PHP já está configurado com headers CORS. Se ainda houver problemas:
1. Verifique se está acessando o site pelo domínio correto
2. Verifique se não há redirecionamentos configurados que possam interferir

---

## 📝 Estrutura Final de Arquivos no Servidor

```
httpdocs/
├── index.html
├── assets/
│   ├── (arquivos JS e CSS do build)
├── api/
│   └── contact.php
└── (outros arquivos do build)
```

---

## ✅ Checklist Final

- [ ] Build do projeto React feito (`npm run build`)
- [ ] Arquivos da pasta `dist` enviados para `httpdocs`
- [ ] Pasta `api` criada dentro de `httpdocs`
- [ ] Arquivo `contact.php` enviado para `httpdocs/api/`
- [ ] Permissões do arquivo PHP configuradas (644 ou 755)
- [ ] Formulário testado no site
- [ ] Email recebido em `contato@jrtechnologysolutions.com.br`

---

## 🆘 Precisa de Ajuda?

Se encontrar algum problema durante a configuração:
1. Verifique os logs de erro no Plesk
2. Teste o arquivo PHP diretamente acessando: `https://jrtechnologysolutions.com.br/api/contact.php`
3. Verifique se o email está configurado corretamente no Plesk

---

## 📧 Configuração Alternativa com SMTP (Opcional)

Se a função `mail()` do PHP não funcionar bem, podemos configurar SMTP. Isso requer:
1. Credenciais SMTP do seu email no Plesk
2. Modificar o arquivo `contact.php` para usar PHPMailer ou similar

Se precisar dessa alternativa, me avise que eu preparo os arquivos!

