# Stack 14 — Testes manuais

## Teste 01 — Criar avaliação
**O que valida:** cadastro e cálculo de IMC.
**Como executar:** entrar como aluno, cadastrar peso, altura e gordura corporal e abrir o detalhe.
**Resultado esperado:** avaliação criada, IMC calculado pelo backend e registro visível apenas ao próprio aluno.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Tentativa de editar histórico
**O que valida:** imutabilidade.
**Como executar:** chamar uma rota de edição/alteração de avaliação antiga.
**Resultado esperado:** HTTP 405 e registro original preservado.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 03 — Evolução
**O que valida:** comparação entre avaliações.
**Como executar:** criar pelo menos duas avaliações em datas distintas e abrir progresso.
**Resultado esperado:** peso inicial, atual e variação coerentes.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE
