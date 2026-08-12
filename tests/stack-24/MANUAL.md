# Stack 24 — Testes manuais

## Teste 01 — Subida completa do ambiente
**O que valida:** aplicação, MariaDB, migrations, health check e volumes funcionam juntos.
**Como executar:** configure `.env`, rode `docker compose up --build -d`, aplique migrations, abra `/health`, faça login e reinicie os containers.
**Resultado esperado:** health responde `ok`, login funciona e dados permanecem após reinício.
**Resultado obtido:** preencher após execução.
**Status:** PENDENTE
