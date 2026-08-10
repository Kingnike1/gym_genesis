# Stack 18 — Testes manuais

## Teste 01 — Upload permitido
**O que valida:** JPEG/PNG/WebP/PDF válidos são armazenados fora de `public/`.
**Como executar:** enviar um arquivo permitido e conferir metadados e caminho físico.
**Resultado esperado:** nome aleatório, MIME correto e arquivo em `storage/uploads`.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Upload malicioso
**O que valida:** bloqueio de executáveis.
**Como executar:** tentar enviar PHP renomeado para imagem.
**Resultado esperado:** upload rejeitado.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE
