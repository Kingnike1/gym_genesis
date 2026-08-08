# Stack 13 — Dietas

## Objetivo

Estruturar planos alimentares com responsável identificado, validade, refeições, itens, substituições, histórico de versões e isolamento por academia.

## Fonte oficial

O módulo orientado a objetos passa a usar:

- `plano_alimentar`
- `plano_alimentar_refeicao`
- `plano_alimentar_item`
- `plano_alimentar_historico`

A tabela legada `dieta` permanece temporariamente apenas por compatibilidade histórica.

## Regras

- Todo plano pertence a uma academia e a um aluno da mesma academia.
- O responsável é sempre o usuário autenticado; IDs de responsável enviados pelo formulário são ignorados.
- Qualificação do responsável é obrigatória e registro profissional pode ser armazenado.
- A aplicação não deve presumir que o papel `Professor` autoriza prescrição alimentar. A política profissional precisa ser definida e validada antes de produção.
- Atualizações incrementam `versao` e gravam snapshot em histórico.
- Refeições e itens são atualizados dentro de transação.
- Aluno só visualiza planos ligados ao seu perfil `aluno`.

## Status

`rascunho`, `ativo`, `encerrado`.

## Limite desta stack

A Stack 13 estrutura o domínio e impede falsificação simples de responsável. Uma futura política de credenciamento profissional deve substituir a rota de compatibilidade `/professor/dietas` quando o produto definir quais categorias profissionais podem prescrever planos alimentares em cada operação/país.
