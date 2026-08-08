# Stack 12 — Testes manuais

## Teste 01 — Criar ficha completa
**O que valida:** professor cria ficha para aluno vinculado com exercícios ordenados.
**Como executar:** entrar como professor ativo, escolher aluno vinculado, adicionar nome, descrição, vigência e ao menos dois exercícios com séries/repetições/carga/intervalo.
**Resultado esperado:** ficha é criada, aparece na lista do professor e na área do aluno correto.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Bloquear aluno não vinculado
**O que valida:** formulário/manipulação de request não permite atribuir treino a aluno fora da responsabilidade do professor.
**Como executar:** alterar manualmente `aluno_id` do request para outro aluno não vinculado.
**Resultado esperado:** HTTP 403 e nenhuma ficha criada.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 03 — Visualização do aluno
**O que valida:** aluno vê apenas suas próprias fichas.
**Como executar:** criar fichas para dois alunos, entrar como um deles e acessar listagem/detalhe; tentar ID da ficha do outro aluno.
**Resultado esperado:** próprias fichas aparecem; ficha de outro aluno retorna 404/nega acesso.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 04 — Iniciar e concluir treino
**O que valida:** histórico real de execução.
**Como executar:** entrar como aluno com ficha ativa, iniciar execução pela rota/tela, depois concluir e consultar banco/histórico.
**Resultado esperado:** `execucao_treino` registra início e conclusão com academia, ficha e aluno corretos.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 05 — Editar ficha
**O que valida:** atualização de prescrição e versionamento.
**Como executar:** editar nome, exercícios e carga de uma ficha existente.
**Resultado esperado:** dados atuais são substituídos, ordem permanece consistente e `versao` aumenta em 1.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 06 — Encerrar ficha
**O que valida:** ficha encerrada continua no histórico, mas não aceita nova execução.
**Como executar:** alterar status para `encerrado` e tentar iniciar execução como aluno.
**Resultado esperado:** ficha permanece consultável no histórico e nova execução é rejeitada.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE
