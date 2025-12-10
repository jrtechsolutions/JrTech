# 🧪 Teste Rápido - Diagnóstico do Erro 500

## ⚡ Teste Imediato

Como o `test.php` funcionou mas o `contact.php` está dando erro 500, vamos testar passo a passo:

---

## 📝 PASSO 1: Testar Versão Simplificada

1. **Faça upload do arquivo `contact-simple.php`** para a pasta `api` no servidor
2. **Temporariamente**, altere o código do formulário para usar este arquivo:
   - No arquivo `ContactSection.tsx`, linha 57, altere:
   ```typescript
   const response = await fetch('/api/contact-simple.php', {
   ```
3. **Faça um novo build** (`npm run build`)
4. **Faça upload dos arquivos novos** para o servidor
5. **Teste o formulário**

**Se funcionar:** O problema está na versão completa. Use a versão simplificada ou vamos corrigir a completa.

**Se não funcionar:** O problema pode ser com a função `mail()` ou configuração do servidor.

---

## 📝 PASSO 2: Verificar Logs de Erro

1. No Plesk, vá em **"Logs"** → **"Error Log"**
2. Procure por erros recentes relacionados ao `contact.php`
3. **Copie o erro completo** e me envie

Os erros vão mostrar exatamente qual linha está causando o problema.

---

## 📝 PASSO 3: Testar Versão de Debug

1. **Faça upload do arquivo `contact-debug.php`** para a pasta `api`
2. **Temporariamente**, altere o código do formulário:
   ```typescript
   const response = await fetch('/api/contact-debug.php', {
   ```
3. **Faça build e teste**
4. **Veja a resposta no console do navegador** (F12 → Console)
5. A resposta vai mostrar informações de debug sobre o erro

---

## 📝 PASSO 4: Verificar se há BOM ou Espaços

Às vezes arquivos PHP podem ter BOM (Byte Order Mark) ou espaços antes do `<?php` que causam erro 500.

**Solução:**
1. Abra o arquivo `contact.php` em um editor de texto
2. Certifique-se de que a primeira linha é exatamente `<?php` (sem espaços antes)
3. Salve o arquivo como UTF-8 sem BOM
4. Faça upload novamente

---

## 🔍 Possíveis Causas do Erro 500

### 1. **Erro de Sintaxe PHP**
- Verifique se todas as aspas estão fechadas
- Verifique se todos os parênteses estão fechados
- Verifique se não há vírgulas ou pontos e vírgulas faltando

### 2. **Problema com Headers**
- Headers já foram enviados antes
- Output antes dos headers

### 3. **Problema com Encoding**
- Arquivo não está em UTF-8
- BOM no início do arquivo

### 4. **Problema com Função mail()**
- Função existe mas não está configurada corretamente
- Servidor não permite envio de email

---

## ✅ Solução Rápida Alternativa

Se nada funcionar, podemos usar uma solução com SMTP que é mais confiável:

1. Use o arquivo `contact-smtp.php`
2. Configure as credenciais SMTP do seu email no Plesk
3. Renomeie para `contact.php`

---

## 📞 Próximos Passos

1. Teste a versão simplificada primeiro
2. Verifique os logs de erro no Plesk
3. Me envie o erro completo dos logs
4. Teste a versão de debug e me envie a resposta

Com essas informações, consigo identificar exatamente qual é o problema!

