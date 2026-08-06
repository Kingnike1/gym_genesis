# Stack 09 — Testes técnicos

## Teste 01 — Papéis diferentes por academia

**O que valida:** confirma que o mesmo usuário pode possuir papéis diferentes em duas academias e que a sessão recebe o papel da academia ativa.

**Como executar:**
1. Aplicar migrations até `0004_user_roles_status` em banco descartável.
2. Executar `composer test:user-roles`.

**Resultado esperado:** saída `OK: papéis por academia validados.`; ao selecionar Academia A a sessão recebe papel 1 e ao selecionar Academia B recebe papel 3; todos os dados temporários são revertidos.

**Resultado obtido:** teste automatizado criado, porém não executado neste ambiente porque não há instância MySQL/MariaDB conectada ao repositório.

**Status:** BLOQUEADO

## Teste 02 — Migração de papéis legados

**O que valida:** confirma que `academia_usuario.papel` recebe inicialmente o valor histórico de `usuario.tipo_usuario`.

**Como executar:**
1. Restaurar banco descartável com a migration multiacademia já aplicada.
2. Registrar amostra de `usuario.tipo_usuario` e seus vínculos.
3. Executar `composer migrate`.
4. Comparar `academia_usuario.papel` com o valor anterior.

**Resultado esperado:** todos os vínculos existentes recebem papel equivalente ao perfil legado.

**Resultado obtido:** não executado por ausência de banco executável.

**Status:** BLOQUEADO

## Teste 03 — Conta global inativa não autentica

**O que valida:** impede login de usuário com `usuario.status = inativo`.

**Como executar:** criar usuário de teste inativo e tentar autenticar com senha correta.

**Resultado esperado:** autenticação retorna falha e nenhuma sessão autenticada é criada.

**Resultado obtido:** lógica implementada em `UserService::authenticateUser`, sem execução integrada neste ambiente.

**Status:** BLOQUEADO
