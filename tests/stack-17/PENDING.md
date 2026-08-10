# Stack 17 — Testes técnicos pendentes

## Teste 01 — Total calculado no backend
**O que valida:** valores enviados pelo cliente não controlam o total.
**Como executar:** criar pedido com produto conhecido e tentar informar total divergente no cliente.
**Resultado esperado:** total persistido deriva exclusivamente dos preços no banco.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Idempotência do pedido/pagamento
**O que valida:** repetição da mesma requisição não duplica registros.
**Como executar:** chamar criação duas vezes com a mesma chave.
**Resultado esperado:** mesmo pedido/pagamento retornado.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Concorrência de estoque
**O que valida:** dois pedidos simultâneos não vendem além do saldo.
**Como executar:** disparar pedidos concorrentes para o último item.
**Resultado esperado:** apenas uma transação confirma a reserva.
**Resultado obtido:** não executado.
**Status:** PENDENTE
