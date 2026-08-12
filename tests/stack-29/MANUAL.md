# Stack 29 — Testes manuais

## Teste 01 — Consumo externo da API
**O que valida:** um cliente externo consegue autenticar e paginar sem sessão web manual.
**Como executar:** em homologação, emita um token com `students:read`; use curl/Postman em `/api/v1/me` e `/api/v1/students?page=1&per_page=10` com header Bearer.
**Resultado esperado:** JSON consistente, apenas dados da academia do token e nenhuma informação de senha/hash.
**Resultado obtido:** preencher após execução.
**Status:** PENDENTE
