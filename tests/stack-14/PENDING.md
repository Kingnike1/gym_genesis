# Stack 14 — Testes técnicos pendentes

## Teste 01 — Isolamento de avaliações
**O que valida:** uma academia não acessa avaliações de outra.
**Como executar:** criar duas academias, um aluno em cada e consultar pelo repository em ambos os contextos.
**Resultado esperado:** cada contexto retorna somente seus próprios registros.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Histórico imutável
**O que valida:** avaliações antigas não são sobrescritas.
**Como executar:** criar duas avaliações sucessivas e conferir os dois registros.
**Resultado esperado:** ambos permanecem armazenados e o progresso usa a primeira e a última.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Medidas em transação
**O que valida:** avaliação e medidas são persistidas atomicamente.
**Como executar:** provocar erro em uma medida durante a criação.
**Resultado esperado:** nenhuma avaliação parcial permanece.
**Resultado obtido:** não executado.
**Status:** PENDENTE
