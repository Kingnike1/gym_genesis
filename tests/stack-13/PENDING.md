# Stack 13 — Testes técnicos pendentes

## Teste 01 — Isolamento por academia
**O que valida:** plano de uma academia não pode ser lido por outra.
**Como executar:** criar duas academias, um plano em cada uma e consultar alternando `AcademyContext`.
**Resultado esperado:** cada contexto retorna somente seu plano.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 02 — Criação atômica do plano
**O que valida:** falha em item/refeição reverte plano completo.
**Como executar:** forçar erro durante inserção de item dentro de `createPlan`.
**Resultado esperado:** nenhuma linha parcial permanece.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 03 — Responsável não pode ser falsificado
**O que valida:** `responsavel_usuario_id` vem da sessão, não do formulário.
**Como executar:** enviar ID de terceiro no POST e criar plano autenticado como outro usuário.
**Resultado esperado:** plano registra somente usuário autenticado.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 04 — Aluno de outra academia
**O que valida:** plano não pode ser criado para aluno de outro tenant.
**Como executar:** usar `aluno_id` pertencente a academia diferente.
**Resultado esperado:** `InvalidArgumentException` e nenhuma gravação.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 05 — Versionamento e histórico
**O que valida:** edição incrementa versão e grava snapshot.
**Como executar:** criar plano, editar duas vezes e consultar `plano_alimentar_historico`.
**Resultado esperado:** versões 1, 2 e 3 rastreáveis.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO
