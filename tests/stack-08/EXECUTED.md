# Stack 08 — Testes técnicos

## Teste 01 — Smoke test automatizado de isolamento

**O que valida:** cria duas academias, um usuário vinculado às duas e um plano por academia; alterna o contexto e verifica que cada academia enxerga somente o próprio plano.

**Como executar:**
1. Usar um banco descartável com migrations aplicadas.
2. Executar `composer test:tenancy`.

**Resultado esperado:** saída `OK: isolamento multiacademia validado.` e rollback dos dados temporários.

**Resultado obtido:** teste criado, mas não executado neste ambiente por ausência de uma instância MySQL/MariaDB ligada ao repositório.

**Status:** BLOQUEADO