# Stack 21 — Testes técnicos pendentes

## Teste 01 — Suíte PHPUnit completa
**O que valida:** todos os testes unitários e de integração configurados.
**Como executar:** `composer install && composer test`.
**Resultado esperado:** processo termina com código 0 e nenhuma falha.
**Resultado obtido:** não executado neste ambiente.
**Status:** PENDENTE

## Teste 02 — Banco de teste isolado
**O que valida:** testes de integração usam exclusivamente `gym_genesis_test`.
**Como executar:** configurar `.env.testing`, executar migrations e `composer test:integration`.
**Resultado esperado:** nenhum dado de desenvolvimento ou produção é alterado.
**Resultado obtido:** não executado neste ambiente.
**Status:** PENDENTE
