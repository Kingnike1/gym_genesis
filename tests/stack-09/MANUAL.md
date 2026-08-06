# Stack 09 — Testes manuais

## Teste 01 — Usuário com papéis diferentes em duas academias

**O que valida:** garante que a mesma conta possa ser Administrador em uma academia e Aluno em outra.

**Como executar:**
1. Criar duas academias.
2. Vincular o mesmo usuário às duas.
3. Definir papel Administrador na Academia A e Aluno na Academia B.
4. Entrar na Academia A e acessar `/admin/dashboard`.
5. Trocar para a Academia B e tentar acessar `/admin/dashboard` e `/student/dashboard`.

**Resultado esperado:** na Academia A o painel administrativo é permitido; na Academia B o painel administrativo retorna 403 e o painel do aluno é permitido.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE

## Teste 02 — Desativação somente do vínculo da academia

**O que valida:** confirma que desativar um usuário em uma academia não inutiliza a mesma conta em outra academia ativa.

**Como executar:**
1. Usar uma conta vinculada a duas academias.
2. Na Academia A, editar o usuário e definir o vínculo como inativo.
3. Trocar para Academia B.
4. Autenticar e navegar normalmente.

**Resultado esperado:** acesso à Academia A é bloqueado; Academia B continua acessível.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE

## Teste 03 — Último login

**O que valida:** confirma o preenchimento de `usuario.last_login_at` após autenticação válida.

**Como executar:**
1. Consultar `last_login_at` de um usuário de teste.
2. Realizar login válido.
3. Consultar o campo novamente.

**Resultado esperado:** `last_login_at` é atualizado para o momento do login.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE

## Teste 04 — Hash de senha não aparece nas listagens

**O que valida:** garante que telas/listagens administrativas não recebam o hash da senha.

**Como executar:**
1. Acessar a lista de usuários como administrador.
2. Inspecionar os dados entregues à view ou resposta gerada.
3. Editar um usuário e repetir a inspeção.

**Resultado esperado:** nenhum campo `senha` ou hash de senha é entregue às views de listagem/edição.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE

## Teste 05 — Migração e rollback da Stack 09

**O que valida:** confirma que a migration `0004_user_roles_status` pode ser aplicada e revertida em banco descartável.

**Como executar:**
1. Executar `composer migrate`.
2. Conferir colunas novas em `usuario` e `academia_usuario`.
3. Executar `composer migrate:rollback`.
4. Conferir remoção das colunas.
5. Executar `composer migrate` novamente.

**Resultado esperado:** migrate, rollback e migrate finalizam sem erro e preservam o estado esperado em cada etapa.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE
