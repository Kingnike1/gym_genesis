# Stack 11 — Testes manuais

## Teste 01 — Cadastro profissional
**O que valida:** criação do perfil com nome, CREF, telefone e bio.
**Como executar:** criar usuário com papel Professor, cadastrar perfil profissional e reabrir os dados.
**Resultado esperado:** dados persistidos na academia correta e CREF normalizado.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Especialidades e unidades
**O que valida:** professor pode atuar em várias unidades da própria academia e possuir várias especialidades.
**Como executar:** associar duas unidades e duas especialidades, salvar e consultar o banco/tela correspondente.
**Resultado esperado:** vínculos corretos, sem duplicação.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 03 — Vincular e desvincular aluno
**O que valida:** ciclo de vínculo operacional professor/aluno.
**Como executar:** vincular um aluno, consultar `students()`, desvincular e consultar novamente.
**Resultado esperado:** aluno aparece enquanto vínculo está ativo e desaparece após desativação.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 04 — Isolamento entre academias
**O que valida:** professor não visualiza alunos de outra academia.
**Como executar:** criar dados em duas academias e alternar contexto.
**Resultado esperado:** nenhuma listagem cruza tenants.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE
