# Stack 30 — Testes técnicos pendentes

## Teste 01 — Liveness/readiness
**O que valida:** `/health` independe do banco e `/ready` reflete disponibilidade do banco.
**Como executar:** subir homologação, chamar os dois endpoints, interromper temporariamente o banco e repetir.
**Resultado esperado:** `/health` continua 200; `/ready` muda para 503 durante indisponibilidade.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Backup e restore
**O que valida:** backup produzido é realmente restaurável.
**Como executar:** criar backup de banco de teste, restaurar em banco descartável e executar smoke tests.
**Resultado esperado:** dados e esquema restaurados corretamente.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Rollback de aplicação
**O que valida:** imagem/tag anterior pode ser reimplantada sem perda inesperada de compatibilidade.
**Como executar:** publicar duas versões em staging e retornar para a anterior seguindo o runbook.
**Resultado esperado:** serviço volta à versão anterior e permanece healthy/ready.
**Resultado obtido:** não executado.
**Status:** PENDENTE
