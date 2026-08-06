# Stack 09 — Testes técnicos pendentes

## Teste 01 — Papéis diferentes por academia

**O que valida:** confirma que o mesmo usuário pode possuir papéis diferentes em duas academias e que a sessão recebe o papel da academia ativa.

**Como executar:**
1. Aplicar migrations até `0004_user_roles_status` em banco descartável.
2. Executar `composer test:user-roles`.

**Resultado esperado:** saída `OK: papéis por academia validados.`; Academia A aplica papel 1 e Academia B papel 3; dados temporários são revertidos.

**Resultado obtido:** ainda não executado.

**Status:** PENDENTE

## Teste 02 — Migração de papéis legados

**O que valida:** confirma que `academia_usuario.papel` recebe inicialmente o valor histórico de `usuario.tipo_usuario`.

**Como executar:**
1. Restaurar banco descartável com a Stack 08 aplicada.
2. Registrar uma amostra dos perfis legados.
3. Executar `composer migrate`.
4. Comparar `academia_usuario.papel` com os valores anteriores.

**Resultado esperado:** todos os vínculos existentes recebem papel equivalente ao perfil legado.

**Resultado obtido:** ainda não executado.

**Status:** PENDENTE

## Teste 03 — Conta global inativa não autentica

**O que valida:** impede login de usuário com `usuario.status = inativo`.

**Como executar:**
1. Criar ou alterar uma conta de teste para `status = inativo`.
2. Tentar login com a senha correta.

**Resultado esperado:** autenticação falha e nenhuma sessão autenticada é criada.

**Resultado obtido:** ainda não executado.

**Status:** PENDENTE

## Teste 04 — Rehash e último login

**O que valida:** confirma atualização de hash antigo quando necessária e gravação de `last_login_at`.

**Como executar:**
1. Criar usuário com hash compatível porém que exija rehash.
2. Realizar login válido.
3. Comparar o hash e `last_login_at` antes/depois.

**Resultado esperado:** hash é atualizado quando `password_needs_rehash` indicar necessidade e `last_login_at` recebe data/hora do login.

**Resultado obtido:** ainda não executado.

**Status:** PENDENTE
