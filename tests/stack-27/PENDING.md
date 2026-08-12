# Stack 27 — Testes técnicos pendentes

## Teste 01 — Logout seguro no layout
**O que valida:** os três perfis enviam logout via POST com CSRF.
**Como executar:** abrir dashboards de admin/professor/aluno e inspecionar/submeter o formulário de logout.
**Resultado esperado:** logout válido redireciona; requisição sem token retorna 403.
**Resultado obtido:** não executado.
**Status:** PENDENTE

## Teste 02 — Responsividade e acessibilidade básica
**O que valida:** layout funciona em desktop/mobile e foco de teclado é visível.
**Como executar:** testar larguras 320px, 768px e desktop; navegar apenas com Tab/Shift+Tab.
**Resultado esperado:** sem overflow crítico, navegação utilizável e foco visível.
**Resultado obtido:** não executado.
**Status:** PENDENTE
