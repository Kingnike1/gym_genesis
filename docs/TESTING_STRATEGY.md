# Gym Genesis — Estratégia de Registro de Testes

A partir da Stack 09, toda stack deve manter dois arquivos separados de testes:

- `tests/stack-XX/EXECUTED.md`: testes técnicos/automatizados já executados pelo desenvolvimento.
- `tests/stack-XX/MANUAL.md`: testes manuais que devem ser executados pelo responsável pelo projeto.

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

Os testes não executados nunca devem ser omitidos. Eles permanecem registrados em `MANUAL.md` ou em `EXECUTED.md` com status `BLOQUEADO/PENDENTE`, para serem executados posteriormente.

Quando um teste manual for realizado pelo responsável do projeto, registrar o resultado no mesmo arquivo, preservando o histórico.