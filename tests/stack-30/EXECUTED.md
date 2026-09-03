# Stack 30 — Testes executados

## Teste 01 — Validação do Composer na CI

**O que valida:** `composer.json` e `composer.lock` estão consistentes antes de instalar dependências.

**Como executar:** workflow GitHub Actions `CI`, job `quality`, etapa `Validate Composer`.

**Resultado esperado:** `composer validate --strict` conclui com sucesso e libera as etapas seguintes.

**Resultado obtido:** falhou. O `composer.lock` está desatualizado em relação ao `composer.json`; as etapas de instalação, migrations, quality gate e auditoria foram ignoradas após a falha.

**Status:** FALHOU

## Teste 02 — Build Docker na CI

**O que valida:** a imagem de produção consegue instalar dependências bloqueadas pelo lockfile e concluir o build.

**Como executar:** workflow GitHub Actions `CI`, job `docker`, `docker/build-push-action`.

**Resultado esperado:** imagem Docker construída com sucesso.

**Resultado obtido:** falhou no estágio Composer. O pacote obrigatório `monolog/monolog` não está presente no `composer.lock`; `composer install --no-dev` encerrou com código 4.

**Status:** FALHOU

## Testes operacionais ainda não executados

Backup, restore, readiness com indisponibilidade de banco e rollback em staging continuam documentados em `PENDING.md` e `MANUAL.md`.
