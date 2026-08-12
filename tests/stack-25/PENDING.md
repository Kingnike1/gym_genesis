# Stack 25 — Testes técnicos pendentes

## Teste 01 — Pipeline completo
**O que valida:** migrations, qualidade, testes, audit e Docker build funcionam no GitHub Actions.
**Como executar:** atualize `composer.lock`, envie a branch e observe o workflow `CI`.
**Resultado esperado:** jobs `quality` e `docker` ficam verdes.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Lockfile consistente
**O que valida:** dependências novas estão versionadas de forma reproduzível.
**Como executar:** rode `composer update`, revise e versione `composer.lock`, depois `composer validate --strict`.
**Resultado esperado:** validação não acusa lock desatualizado.
**Resultado obtido:** não executado.
**Status:** PENDENTE
