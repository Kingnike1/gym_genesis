# Stack 20 — Testes manuais

## Teste 01 — Página inexistente
**O que valida:** 404 amigável.
**Como executar:** abrir uma URL inexistente no navegador.
**Resultado esperado:** HTTP 404, mensagem simples e código de referência.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Método incorreto
**O que valida:** 405.
**Como executar:** enviar GET para uma rota que aceita apenas POST.
**Resultado esperado:** HTTP 405 e cabeçalho `Allow`.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 03 — Erro de produção
**O que valida:** nenhum detalhe interno vaza.
**Como executar:** com `APP_DEBUG=false`, provocar um erro controlado em ambiente de teste.
**Resultado esperado:** mensagem genérica com `request_id`, sem stack trace.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE
