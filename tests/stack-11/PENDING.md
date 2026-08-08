# Stack 11 — Testes técnicos pendentes

## Teste 01 — CREF único por academia
**O que valida:** duplicidade de CREF é bloqueada somente dentro da mesma academia.
**Como executar:** criar dois professores com mesmo CREF na Academia A e um terceiro com o mesmo CREF na Academia B.
**Resultado esperado:** segunda criação em A falha; criação em B funciona.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 02 — Vínculo professor/aluno entre academias
**O que valida:** não é possível vincular professor da Academia A a aluno da Academia B.
**Como executar:** criar os perfis em academias distintas e chamar `linkStudent()`.
**Resultado esperado:** nenhum vínculo válido é criado.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 03 — Unidade de outra academia
**O que valida:** `replaceUnits()` não vincula unidade fora do tenant atual.
**Como executar:** fornecer ID de unidade pertencente a outra academia.
**Resultado esperado:** unidade não é inserida em `professor_unidade`.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO

## Teste 04 — Especialidades sem duplicação
**O que valida:** lista de especialidades é substituída e duplicatas são eliminadas.
**Como executar:** salvar `['Musculação','Musculação','Funcional']`.
**Resultado esperado:** apenas duas especialidades ficam persistidas.
**Resultado obtido:** não executado.
**Status:** BLOQUEADO
