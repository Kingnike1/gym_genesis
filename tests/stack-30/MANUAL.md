# Stack 30 — Testes manuais

## Teste 01 — Simulação operacional em staging
**O que valida:** deploy, health, logs, backup, restore e rollback são executáveis pelo responsável.
**Como executar:** publicar a release candidata em staging; validar `/health` e `/ready`; gerar backup; restaurar em banco descartável; provocar erro controlado e localizar request_id; executar rollback para imagem anterior.
**Resultado esperado:** todos os procedimentos do runbook podem ser concluídos e documentados sem acessar produção para ensaio.
**Resultado obtido:** preencher após execução.
**Status:** PENDENTE
