# Stack 24 — Testes técnicos pendentes

## Teste 01 — Build da imagem
**O que valida:** Dockerfile constrói a imagem PHP 8.2 com dependências e usuário não-root.
**Como executar:** `docker build -t gym-genesis:test .`.
**Resultado esperado:** build finaliza sem erro.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Health check e persistência
**O que valida:** app sobe, `/health` responde e banco/uploads persistem após restart.
**Como executar:** `docker compose up --build -d`, aguardar healthy, reiniciar os serviços e validar dados.
**Resultado esperado:** health 200 e dados preservados.
**Resultado obtido:** não executado.
**Status:** PENDENTE
