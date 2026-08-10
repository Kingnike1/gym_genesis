# Stack 17 — Testes manuais

## Teste 01 — Pedido com estoque
**O que valida:** criação e baixa de estoque.
**Como executar:** criar produto com saldo 5 e fazer pedido de 2 unidades.
**Resultado esperado:** pedido com preço do banco, item snapshot e saldo 3.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Repetir requisição
**O que valida:** idempotência.
**Como executar:** repetir exatamente a mesma criação com a mesma chave.
**Resultado esperado:** nenhum pedido/pagamento duplicado.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE
