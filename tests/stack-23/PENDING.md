# Stack 23 — Testes técnicos pendentes

## Teste 01 — Correlação por request_id
**O que valida:** um erro gera log e resposta com o mesmo `request_id`.
**Como executar:** provoque uma rota que lance exceção em ambiente de teste e compare resposta com stderr.
**Resultado esperado:** o mesmo identificador aparece nos dois locais.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Ausência de segredos nos logs
**O que valida:** senha, token e cookie não são registrados.
**Como executar:** realizar login/reset inválidos e inspecionar stderr.
**Resultado esperado:** nenhum segredo aparece em texto claro.
**Resultado obtido:** não executado.
**Status:** PENDENTE
