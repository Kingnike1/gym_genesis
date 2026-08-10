# Stack 16 — Testes técnicos pendentes

## Teste 01 — Bloqueio de estoque negativo
**O que valida:** saída maior que o saldo é rejeitada.
**Como executar:** criar produto com saldo 2 e tentar saída de 3.
**Resultado esperado:** exceção de estoque insuficiente e saldo preservado.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Concorrência
**O que valida:** duas saídas simultâneas não vendem o mesmo estoque.
**Como executar:** executar duas transações concorrentes sobre o mesmo produto.
**Resultado esperado:** bloqueio `FOR UPDATE` serializa as alterações e impede saldo negativo.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Isolamento por academia
**O que valida:** estoque de um tenant não é alterado por outro.
**Como executar:** repetir o mesmo ID lógico em academias distintas e movimentar apenas uma.
**Resultado esperado:** somente o contexto atual é afetado.
**Resultado obtido:** não executado.
**Status:** PENDENTE
