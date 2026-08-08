# Stack 13 — Testes manuais para o responsável

## Teste 01 — Criar plano alimentar completo
**O que valida:** criação com responsável, validade, refeições, itens e substituições.
**Como executar:** entrar com usuário autorizado, criar plano para aluno da mesma academia, adicionar pelo menos duas refeições e itens.
**Resultado esperado:** plano salvo integralmente e exibido ao aluno correto.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 02 — Acesso do aluno
**O que valida:** aluno só enxerga o próprio plano.
**Como executar:** entrar como Aluno A, abrir lista e tentar acessar pela URL o ID do plano do Aluno B.
**Resultado esperado:** lista contém somente planos do Aluno A e acesso ao B retorna 404.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 03 — Validade inválida
**O que valida:** data final anterior à inicial é rejeitada.
**Como executar:** informar `data_fim` anterior a `data_inicio`.
**Resultado esperado:** formulário retorna erro e não grava plano.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 04 — Editar e verificar versão
**O que valida:** edição preserva rastreabilidade.
**Como executar:** editar nome/refeições de plano existente e consultar banco/histórico.
**Resultado esperado:** `versao` aumenta e histórico registra snapshot anterior/atualizado.
**Resultado obtido:** preencher após teste.
**Status:** PENDENTE

## Teste 05 — Política profissional
**O que valida:** processo operacional não presume que todo professor pode prescrever alimentação.
**Como executar:** revisar com a academia quais profissionais serão autorizados e verificar se qualificação/registro apresentados correspondem ao processo definido.
**Resultado esperado:** somente profissionais formalmente autorizados pela política da operação utilizam criação/edição de planos.
**Resultado obtido:** preencher após definição da política.
**Status:** PENDENTE
