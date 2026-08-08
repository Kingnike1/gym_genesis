# Stack 12 — Testes técnicos pendentes

## Teste 01 — Criação atômica da ficha
**O que valida:** ficha e exercícios são gravados juntos.
**Como executar:** criar ficha com três exercícios e forçar falha no segundo/terceiro item.
**Resultado esperado:** em caso de erro, nem ficha nem itens ficam parcialmente persistidos.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 02 — Professor só usa alunos vinculados
**O que valida:** professor não cria treino para aluno sem vínculo ativo.
**Como executar:** tentar criar ficha para aluno da mesma academia, mas não vinculado ao professor.
**Resultado esperado:** controller responde 403 e nenhuma ficha é criada.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 03 — Isolamento de ficha por academia
**O que valida:** ID de ficha da Academia A não é encontrado na Academia B.
**Como executar:** criar fichas em duas academias e alternar `AcademyContext`.
**Resultado esperado:** `find`, listagens e histórico retornam somente dados do tenant ativo.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 04 — Execução somente do próprio aluno
**O que valida:** outro aluno não consegue iniciar nem concluir execução de ficha alheia.
**Como executar:** usar IDs de dois alunos diferentes na mesma academia.
**Resultado esperado:** `startExecution` retorna 0 e `finishExecution` não altera registro de outro aluno.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 05 — Versionamento da ficha
**O que valida:** edição incrementa `versao` e substitui itens sem duplicar ordem.
**Como executar:** criar ficha, editar duas vezes e consultar `ficha_treino`/`ficha_treino_exercicio`.
**Resultado esperado:** versão aumenta e existe uma única lista ordenada de itens atuais.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO
