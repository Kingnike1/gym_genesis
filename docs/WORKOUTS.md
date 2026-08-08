# Stack 12 — Treinos

A Stack 12 substitui o contrato inconsistente da tabela legada `treino` por uma estrutura normalizada usada pelo `TreinoRepository`.

## Estrutura

- `ficha_treino`: ficha atribuída a aluno e professor;
- `ficha_treino_exercicio`: exercícios, ordem, séries, repetições, carga, intervalo e observações;
- `execucao_treino`: histórico de início/conclusão pelo aluno.

A tabela legada `treino` permanece temporariamente apenas para compatibilidade histórica; o código orientado a objetos novo usa `ficha_treino`.

## Regras

- ficha pertence obrigatoriamente à academia atual;
- professor autenticado só cria ficha para aluno vinculado a ele;
- `professor_id` não é confiado ao formulário: vem do perfil do usuário autenticado;
- aluno é resolvido por `aluno.usuario_id`, evitando confundir `usuario_id` com `idaluno`;
- edição incrementa `versao`;
- status permitido: `rascunho`, `ativo`, `encerrado`;
- somente treino ativo pode iniciar execução;
- aluno só inicia/conclui execução da própria ficha;
- criação/edição da ficha e itens usa transação.

## Exercícios

O catálogo `exercicio` permanece global nesta fase. A ficha referencia esse catálogo e guarda apenas a prescrição específica: séries, repetições, carga, intervalo, ordem e observações.

## Histórico

`execucao_treino` não altera a ficha original. Isso permite acompanhar uso real sem sobrescrever a prescrição profissional.