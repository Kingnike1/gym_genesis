# Stack 15 — Testes técnicos pendentes

## Teste 01 — Valor contratado preservado
**O que valida:** alteração no preço do plano não muda matrícula existente.
**Como executar:** criar plano e matrícula, alterar o plano e consultar a matrícula.
**Resultado esperado:** `valor_contratado` permanece igual ao valor original.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Histórico de status
**O que valida:** suspensão/cancelamento gera histórico.
**Como executar:** alterar uma matrícula ativa para suspensa.
**Resultado esperado:** matrícula atualizada e linha correspondente em `matricula_historico`.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Isolamento por academia
**O que valida:** aluno e plano de academias diferentes não podem formar matrícula.
**Como executar:** tentar criar vínculo cruzado.
**Resultado esperado:** operação rejeitada.
**Resultado obtido:** não executado.
**Status:** PENDENTE
