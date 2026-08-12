# Stack 28 — Testes técnicos pendentes

## Teste 01 — Isolamento de solicitações do titular
**O que valida:** uma academia não consulta solicitações de outra.
**Como executar:** criar usuários/solicitações em duas academias e consultar por contexto alternado.
**Resultado esperado:** cada contexto retorna apenas os próprios registros.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Consentimento e revogação
**O que valida:** consentimento registra versão/finalidade e revogação não apaga histórico.
**Como executar:** registrar consentimento, revogar e consultar banco.
**Resultado esperado:** `revogado_em` preenchido e registro preservado.
**Resultado obtido:** não executado.
**Status:** PENDENTE
