# Stack 18 — Testes técnicos pendentes

## Teste 01 — MIME real
**O que valida:** extensão falsa não burla a lista permitida.
**Como executar:** renomear um PHP para `.jpg` e enviar.
**Resultado esperado:** upload rejeitado pelo `finfo`.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Limite de tamanho
**O que valida:** arquivos maiores que 5 MB são rejeitados.
**Como executar:** enviar arquivo acima do limite.
**Resultado esperado:** nenhuma gravação física ou metadado persistido.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 03 — Autorização privada
**O que valida:** usuário não acessa arquivo privado de outro.
**Como executar:** criar arquivo para usuário A e resolver caminho como usuário B.
**Resultado esperado:** acesso negado.
**Resultado obtido:** não executado.
**Status:** PENDENTE
