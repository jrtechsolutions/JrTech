# 🔧 Diagnóstico de Erro 500 - Formulário de Contato

## ⚠️ Problema Identificado

Você está recebendo um erro **500 (Internal Server Error)** ao tentar enviar o formulário. Isso indica um problema no servidor PHP.

---

## 🚀 Solução Rápida - Passo a Passo

### **PASSO 1: Fazer Upload do Arquivo Corrigido**

1. O arquivo `api/contact.php` foi corrigido e agora é compatível com PHP 8+
2. Faça upload do novo arquivo `contact.php` para a pasta `api` no Plesk
3. **Substitua** o arquivo antigo pelo novo

---

### **PASSO 2: Testar o PHP**

1. Faça upload do arquivo `api/test.php` para a pasta `api` no servidor
2. Acesse no navegador: `https://jrtechnologysolutions.com.br/api/test.php`
3. Verifique se todas as funções estão disponíveis (devem aparecer com ✓)

**O que verificar:**
- ✓ Versão do PHP (deve ser 7.4 ou superior)
- ✓ Função `mail()` disponível
- ✓ Função `json_decode()` disponível
- ✓ Permissões do arquivo (644 ou 755)

---

### **PASSO 3: Verificar Logs de Erro no Plesk**

1. No Plesk, vá em **"Logs"** ou **"Error Log"**
2. Procure por erros recentes relacionados ao `contact.php`
3. Os erros vão mostrar exatamente qual é o problema

**Como acessar os logs:**
- Plesk → Domínio → **"Logs"** → **"Error Log"**
- Ou: Plesk → **"Logs"** → **"Error Log"**

---

### **PASSO 4: Verificar Configuração PHP**

1. No Plesk, vá em **"PHP Settings"** ou **"Configurações PHP"**
2. Verifique:
   - Versão do PHP (recomendado: 7.4, 8.0, 8.1 ou superior)
   - Função `mail()` habilitada
   - `display_errors` desabilitado em produção
   - `log_errors` habilitado

---

### **PASSO 5: Verificar Permissões**

1. No File Manager do Plesk, encontre o arquivo `api/contact.php`
2. Clique com botão direito → **"Change Permissions"** (Alterar Permissões)
3. Configure como: **644** ou **755**
4. Salve

---

## 🔍 Problemas Comuns e Soluções

### **Problema 1: FILTER_SANITIZE_STRING não existe**

**Causa:** PHP 8.1+ removeu essa função

**Solução:** ✅ Já corrigido no novo `contact.php` (usa `htmlspecialchars`)

---

### **Problema 2: Função mail() não funciona**

**Sintomas:**
- Erro 500 ao enviar
- Logs mostram erro relacionado ao `mail()`

**Soluções:**

**Opção A - Verificar configuração de email no Plesk:**
1. Plesk → **"Mail"** → Verifique se `contato@jrtechnologysolutions.com.br` existe
2. Teste enviando um email manualmente

**Opção B - Usar SMTP:**
1. Use o arquivo `contact-smtp.php` (renomeie para `contact.php`)
2. Configure as credenciais SMTP dentro do arquivo
3. No Plesk: **"Mail"** → **"contato@..."** → **"Configurações"** → Pegue as credenciais SMTP

---

### **Problema 3: Erro de sintaxe PHP**

**Sintomas:**
- Erro 500 imediato
- Logs mostram "Parse error" ou "Syntax error"

**Solução:**
1. Verifique se o arquivo foi copiado completamente
2. Verifique se não há caracteres especiais no arquivo
3. Teste o arquivo `test.php` primeiro

---

### **Problema 4: CORS ou Headers**

**Sintomas:**
- Erro no console do navegador sobre CORS
- Requisição não chega ao servidor

**Solução:** ✅ Já corrigido no novo `contact.php` (inclui headers CORS e suporte a OPTIONS)

---

## 📋 Checklist de Diagnóstico

Execute estes passos na ordem:

- [ ] Arquivo `contact.php` atualizado no servidor
- [ ] Arquivo `test.php` acessado e todas funções OK
- [ ] Logs de erro verificados no Plesk
- [ ] Versão PHP verificada (7.4+)
- [ ] Função `mail()` disponível
- [ ] Permissões do arquivo corretas (644 ou 755)
- [ ] Email `contato@jrtechnologysolutions.com.br` existe e está ativo
- [ ] Formulário testado novamente

---

## 🆘 Se Nada Funcionar

### **Alternativa 1: Usar SMTP**

1. Use o arquivo `contact-smtp.php`
2. Configure as credenciais SMTP do seu email no Plesk
3. Renomeie para `contact.php`

### **Alternativa 2: Verificar Logs Detalhados**

1. No arquivo `contact.php`, descomente temporariamente:
   ```php
   ini_set('display_errors', 1);
   ```
2. Teste o formulário
3. Veja o erro exato no navegador
4. **IMPORTANTE:** Desative novamente após o diagnóstico!

### **Alternativa 3: Contatar Suporte**

Se nenhuma solução funcionar:
1. Cole aqui os erros dos logs do Plesk
2. Envie uma captura de tela do `test.php`
3. Informe a versão do PHP do servidor

---

## 📝 Informações Úteis para Debug

Quando pedir ajuda, forneça:

1. **Versão do PHP:** (veja em `test.php`)
2. **Erro dos logs:** (copie do Error Log do Plesk)
3. **Resultado do test.php:** (todas as funções disponíveis?)
4. **Mensagem de erro exata:** (do console do navegador)

---

## ✅ Após Corrigir

1. Teste o formulário novamente
2. Verifique se o email chega em `contato@jrtechnologysolutions.com.br`
3. Se funcionar, desative `display_errors` no `contact.php` (já está desativado por padrão)

