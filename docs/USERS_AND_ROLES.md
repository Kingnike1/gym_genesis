# Gym Genesis — Usuários e papéis

## Modelo adotado

A identidade do usuário permanece global em `usuario`. O papel operacional pertence ao vínculo `academia_usuario`.

Isso permite que uma única conta tenha funções diferentes por academia.

Exemplo:

```text
usuario 42
├── Academia A → Administrador
└── Academia B → Aluno
```

## Papéis

Os papéis são representados por `App\Enums\UserRole`:

- `1` — Administrador
- `2` — Professor
- `3` — Aluno

O campo legado `usuario.tipo_usuario` é mantido temporariamente para compatibilidade e autenticação inicial. A fonte correta após a resolução da academia é `academia_usuario.papel`.

## Status

`usuario.status` controla a identidade global:

- `ativo`
- `inativo`

`academia_usuario.ativo` controla somente o vínculo com uma academia específica.

Desativar um vínculo não deve bloquear automaticamente as outras academias do usuário.

## Sessão

Ao resolver ou trocar a academia ativa, `AcademyContext` grava na sessão:

- `academia_id`
- `unidade_id`
- `user_type` correspondente a `academia_usuario.papel`

A troca de academia regenera o ID da sessão.

## DTOs

Entradas de criação e atualização utilizam:

- `CreateUserData`
- `UpdateUserData`

Isso reduz o uso de arrays soltos e mantém os contratos do serviço explícitos.

## Segurança

- hashes de senha só são carregados pelo método específico de autenticação;
- listagens e edição não retornam o campo `senha`;
- usuário global inativo não pode autenticar;
- `last_login_at` é atualizado após autenticação válida;
- remoção de usuário de uma academia remove primeiro o vínculo; se ainda existirem outros vínculos, a identidade global é preservada.

## Próximas stacks

Dados específicos de aluno e professor continuam pertencendo às Stacks 10 e 11. Validação avançada de entrada pertence à Stack 20. Esta stack não antecipa esses domínios.