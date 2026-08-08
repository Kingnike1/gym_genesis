# Stack 10 — Testes manuais

## Teste 01 — Cadastro de aluno
**O que valida:** criação de perfil de aluno com matrícula e dados básicos.
**Como executar:** aplicar migrations, criar usuário com papel Aluno na academia ativa e cadastrar perfil com matrícula única.
**Resultado esperado:** registro criado com `academia_id`, `unidade_id` e `usuario_id` corretos.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Suspensão sem exclusão
**O que valida:** aluno pode ser suspenso preservando histórico.
**Como executar:** alterar status de um aluno ativo para `suspenso` e consultar novamente.
**Resultado esperado:** registro continua existindo, mas status passa a `suspenso`.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 03 — Isolamento visual entre academias
**O que valida:** listagens administrativas não misturam alunos.
**Como executar:** cadastrar alunos distintos em duas academias, alternar academia ativa e consultar a lista.
**Resultado esperado:** cada academia mostra somente seus próprios alunos.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 04 — Contato de emergência e observações
**O que valida:** dados complementares são persistidos e recuperados.
**Como executar:** preencher telefone/contato de emergência, objetivo e observações, salvar e reabrir o perfil.
**Resultado esperado:** os valores persistem corretamente.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE
