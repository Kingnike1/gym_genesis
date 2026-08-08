# Stack 10 — Testes técnicos pendentes

## Teste 01 — Isolamento de aluno por academia
**O que valida:** um aluno da Academia A não pode ser consultado quando o contexto ativo é a Academia B.
**Como executar:** criar duas academias, um aluno em cada uma, alternar `AcademyContext` e chamar `AlunoRepository::find()` e `search()`.
**Resultado esperado:** cada contexto retorna apenas os próprios alunos.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 02 — Matrícula única por academia
**O que valida:** duas academias podem usar a mesma matrícula, mas uma academia não pode duplicá-la.
**Como executar:** cadastrar matrícula `A001` duas vezes na mesma academia e uma vez em outra academia.
**Resultado esperado:** a segunda gravação na mesma academia falha; na outra academia é aceita.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 03 — Busca e paginação
**O que valida:** busca por nome, matrícula e CPF respeita filtro de status, limite e offset.
**Como executar:** popular dados fictícios e chamar `AlunoService::search()` com páginas e filtros diferentes.
**Resultado esperado:** resultados corretos, no máximo 100 registros por chamada e sem vazamento entre academias.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 04 — Status inválido
**O que valida:** o service recusa status fora de `ativo`, `inativo`, `suspenso`.
**Como executar:** chamar `changeStatus()` com `excluido`.
**Resultado esperado:** `InvalidArgumentException` e nenhuma alteração no banco.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO
