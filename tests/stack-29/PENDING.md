# Stack 29 — Testes técnicos pendentes

## Teste 01 — Token e isolamento
**O que valida:** token da Academia A não retorna alunos da Academia B.
**Como executar:** emitir tokens para duas academias e chamar `/api/v1/students`.
**Resultado esperado:** cada token recebe somente dados de seu tenant.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Escopo e rate limit
**O que valida:** token sem `students:read` recebe 403 e excesso recebe 429.
**Como executar:** chamar endpoint com token sem escopo e depois exceder 120 chamadas/min em teste.
**Resultado esperado:** 403 e 429 respectivamente.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Assinatura de webhook
**O que valida:** HMAC válido é aceito e payload alterado é rejeitado.
**Como executar:** testar `WebhookVerifier` com assinatura conhecida e payload modificado.
**Resultado esperado:** true para íntegro, false para alterado.
**Resultado obtido:** não executado.
**Status:** PENDENTE
