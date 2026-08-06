# Gym Genesis — Estratégia de Registro de Testes

A partir da Stack 09, toda stack deve manter três arquivos separados:

- `tests/stack-XX/EXECUTED.md`: somente testes realmente executados.
- `tests/stack-XX/PENDING.md`: testes técnicos/automatizados criados ou planejados, mas ainda não executados.
- `tests/stack-XX/MANUAL.md`: testes que devem ser executados manualmente pelo responsável pelo projeto.

Cada teste deve informar obrigatoriamente:

1. **Nome**
2. **O que valida**
3. **Como executar**
4. **Resultado esperado**
5. **Resultado obtido** (quando executado)
6. **Status**: `PENDENTE`, `APROVADO`, `FALHOU` ou `BLOQUEADO`

Formato recomendado:

```markdown
## Teste 01 — Nome do teste

**O que valida:** descrição objetiva.

**Como executar:**
1. Passo 1.
2. Passo 2.

**Resultado esperado:** comportamento esperado.

**Resultado obtido:** preencher após a execução.

**Status:** PENDENTE
```

Um teste não executado nunca deve aparecer como aprovado em `EXECUTED.md`. Quando um teste pendente for executado, ele deve ser movido para `EXECUTED.md` com o resultado registrado. Testes manuais permanecem em `MANUAL.md` e devem receber o resultado informado pelo responsável.

## Resumo obrigatório por stack

```text
Stack 09: testes executados
1. Nome do teste — status

Testes técnicos pendentes
1. Nome do teste — status

Testes manuais para o responsável
1. Nome do teste — status
```

Nenhum teste deve ser considerado aprovado sem resultado registrado.