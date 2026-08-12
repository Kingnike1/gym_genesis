# Stack 26 — Testes técnicos pendentes

## Teste 01 — Índices e EXPLAIN
**O que valida:** consultas multiacademia usam os novos índices.
**Como executar:** aplique a migration e rode `EXPLAIN` nas consultas de aluno, treino, matrícula, produto e pedido.
**Resultado esperado:** planos de execução utilizam índices adequados e evitam full scan desnecessário.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Paginação limitada
**O que valida:** `paginate()` respeita página e limite máximo de 100.
**Como executar:** criar massa de dados de teste e solicitar páginas/limites diferentes.
**Resultado esperado:** metadados corretos e nunca mais de 100 itens.
**Resultado obtido:** não executado.
**Status:** PENDENTE
