# Stack 08 — Testes manuais

## Teste 01 — Migração de dados legados para academia padrão

**O que valida:** confirma que registros existentes recebem `academia_id` sem perda de dados.

**Como executar:**
1. Restaurar uma cópia descartável do banco legado.
2. Executar `composer migrate`.
3. Conferir `academias`, `unidades`, `academia_usuario` e registros operacionais.

**Resultado esperado:** uma academia/unidade padrão é criada, usuários e registros antigos ficam vinculados e nenhuma informação existente é perdida.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE

## Teste 02 — Isolamento entre duas academias

**O que valida:** impede que uma academia visualize registros de outra.

**Como executar:**
1. Criar duas academias e usuários/vínculos de teste.
2. Criar registros equivalentes em ambas.
3. Acessar listagens e URLs de recursos alternando o contexto.

**Resultado esperado:** cada contexto retorna somente registros da academia ativa; IDs cruzados não são acessíveis.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE

## Teste 03 — Seleção de academia sem vínculo

**O que valida:** bloqueia troca de contexto para academia não autorizada.

**Como executar:** enviar `POST /academy/select` com CSRF válido e ID de academia sem vínculo com o usuário.

**Resultado esperado:** requisição rejeitada; sessão permanece na academia anterior.

**Resultado obtido:** a preencher pelo responsável.

**Status:** PENDENTE