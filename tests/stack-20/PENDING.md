# Stack 20 — Testes técnicos pendentes

## Teste 01 — 404 centralizado
**O que valida:** rota inexistente usa o ErrorHandler.
**Como executar:** acessar uma URL não cadastrada com `Accept: text/html` e depois `Accept: application/json`.
**Resultado esperado:** HTTP 404; HTML ou JSON padronizado com `request_id`.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — 405 e Allow
**O que valida:** método incorreto informa métodos permitidos.
**Como executar:** chamar rota conhecida com método incompatível.
**Resultado esperado:** HTTP 405 e cabeçalho `Allow` coerente.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Validação 422
**O que valida:** `ValidationException` não vira 500.
**Como executar:** disparar Validator com campo obrigatório vazio.
**Resultado esperado:** HTTP 422 e, em JSON, erros por campo.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 04 — Erro interno sem vazamento
**O que valida:** produção não exibe stack trace/SQL.
**Como executar:** provocar exceção inesperada com `APP_DEBUG=false`.
**Resultado esperado:** HTTP 500 com mensagem genérica e `request_id`; detalhe somente no log.
**Resultado obtido:** não executado.
**Status:** PENDENTE
