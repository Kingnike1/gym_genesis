# Stack 19 — Testes técnicos pendentes

## Teste 01 — Token expira e é uso único
**O que valida:** token usado/expirado não redefine senha.
**Como executar:** usar token válido, repetir o uso e testar um token com `expires_at` passado.
**Resultado esperado:** apenas a primeira utilização válida é aceita.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Enumeração de conta
**O que valida:** e-mail existente e inexistente recebem a mesma resposta pública.
**Como executar:** solicitar recuperação para ambos.
**Resultado esperado:** mensagem pública idêntica.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Invalidação de sessões
**O que valida:** troca de senha encerra sessões antigas.
**Como executar:** manter sessão autenticada, redefinir senha em outro navegador e reutilizar a sessão antiga.
**Resultado esperado:** `session_version` divergente invalida a sessão.
**Resultado obtido:** não executado.
**Status:** PENDENTE
