# Privacidade e proteção de dados

Este documento descreve controles técnicos; ele não substitui revisão jurídica de conformidade.

## Categorias principais
- identidade e contato: usuário/aluno/professor;
- dados operacionais: matrícula, treino e histórico de uso;
- dados potencialmente sensíveis: avaliações físicas, medidas, fotos de evolução e informações relacionadas à saúde;
- financeiros: pedidos, pagamentos e histórico contratual;
- segurança: logs, auditoria, tentativas de acesso e recuperação de senha.

## Controles implementados
- consentimento/finalidade versionados e revogáveis;
- solicitações de acesso, correção, exportação, eliminação, anonimização e revogação;
- política de retenção configurável por academia/categoria;
- isolamento multiacademia e autorização por proprietário;
- uploads privados por padrão e trilha de auditoria;
- logs sem senhas, tokens, cookies ou dados de cartão.

## Regras operacionais
Não apagar automaticamente dados com obrigação de preservação sem uma política validada. Solicitações de eliminação devem considerar vínculos contratuais, financeiros e de auditoria antes da ação. Produção deve usar dados fictícios em desenvolvimento/teste e backups com acesso restrito.
