# Gym Genesis — Estratégia de Branches e Controle de Desenvolvimento

## 1. Objetivo

Organizar a evolução do Gym Genesis para que cada stack seja desenvolvida separadamente, as mudanças sejam testadas antes da produção e o histórico permaneça simples, rastreável e reversível.

## 2. Estrutura de branches

```text
main
└── develop
    ├── stack/01-seguranca-emergencial
    ├── stack/02-limpeza-repositorio
    ├── stack/03-sessoes-autenticacao
    ├── stack/04-autorizacao-permissoes
    └── ...
```

## 3. Responsabilidade de cada branch

### `main`

Representa a versão estável e pronta para produção.

- Não recebe desenvolvimento direto.
- Recebe somente código revisado e testado.
- Não deve conter funcionalidades parcialmente implementadas.
- Toda publicação deve receber uma tag de versão.

### `develop`

Representa a próxima versão integrada do sistema.

- É o destino de todas as stacks.
- É utilizada em homologação e testes integrados.
- Pode receber correções de integração, mas não deve permanecer quebrada.
- Só chega à `main` após um ciclo de validação.

### `stack/*`

Cada branch deve resolver uma entrega pequena, testável e revisável.

Exemplos:

```text
stack/01-seguranca-emergencial
stack/02-limpeza-repositorio
stack/03-sessoes-autenticacao
stack/04-autorizacao-permissoes
```

Toda stack nasce da `develop` e retorna para a `develop` por Pull Request.

## 4. Fluxo principal

```text
stack/* → develop → release/* → main
```

Para correções críticas:

```text
main → hotfix/* → main
               └→ develop
```

## 5. Preparação inicial

### Preservar o estado atual

```bash
git checkout main
git pull origin main
git tag -a legacy-before-refactor -m "Estado original antes da evolução por stacks"
git push origin legacy-before-refactor
```

### Criar a `develop`

```bash
git checkout main
git pull origin main
git checkout -b develop
git push -u origin develop
```

### Proteger `main` e `develop`

Regras recomendadas:

- bloquear push direto;
- exigir Pull Request;
- impedir force push e exclusão;
- exigir branch atualizada antes do merge;
- exigir testes quando a CI estiver configurada.

## 6. Criação de uma stack

```bash
git checkout develop
git pull origin develop
git checkout -b stack/01-seguranca-emergencial
git push -u origin stack/01-seguranca-emergencial
```

Durante o trabalho:

```bash
git add <arquivos-da-stack>
git commit -m "security(config): remove tracked environment secrets"
git push
```

Depois:

1. Abrir Pull Request da stack para `develop`.
2. Revisar o escopo e os arquivos alterados.
3. Executar testes.
4. Corrigir pendências na própria branch.
5. Fazer squash merge.
6. Excluir a branch após o merge.

## 7. Regra principal de escopo

Cada branch deve resolver uma responsabilidade principal.

A branch `stack/03-sessoes-autenticacao` pode alterar login, logout, sessão, cookies e rate limiting. Ela não deve alterar loja, treinos, pagamentos ou interface sem relação direta.

Problemas não relacionados devem virar Issue e ser resolvidos em outra branch.

## 8. Divisão de stacks grandes

Stacks grandes devem ser divididas em entregas menores.

Exemplo para Multiacademia:

```text
stack/08a-entidade-academia
stack/08b-usuarios-academias
stack/08c-contexto-academia
stack/08d-isolamento-repositories
stack/08e-configuracoes-academia
stack/08f-testes-isolamento
```

Uma branch não deve permanecer aberta por semanas acumulando mudanças demais.

## 9. Ordem de execução

### Ciclo 1 — Base segura

```text
stack/01-seguranca-emergencial
stack/02-limpeza-repositorio
stack/03-sessoes-autenticacao
stack/04-autorizacao-permissoes
```

### Ciclo 2 — Base arquitetural

```text
stack/05-router-http
stack/06-consolidacao-arquitetura
stack/07-banco-migrations
stack/20-validacao-erros
```

### Ciclo 3 — Multiacademia e identidade

```text
stack/08a-entidade-academia
stack/08b-usuarios-academias
stack/08c-contexto-academia
stack/08d-isolamento-dados
stack/09-usuarios-perfis
stack/10-alunos
stack/11-professores
```

### Ciclo 4 — Núcleo operacional

```text
stack/12-treinos
stack/13-dietas
stack/14-avaliacoes
stack/15-planos-matriculas
```

### Ciclo 5 — Área comercial

```text
stack/16-loja-estoque
stack/17-pedidos-pagamentos
stack/18-uploads
stack/19-recuperacao-senha
```

### Ciclo 6 — Qualidade e produção

```text
stack/21-testes
stack/22-qualidade-codigo
stack/23-logs-auditoria
stack/24-docker
stack/25-ci-cd
```

### Ciclo 7 — Crescimento

```text
stack/26-performance
stack/27-frontend
stack/28-lgpd
stack/29-api-integracoes
stack/30-monitoramento-producao
```

## 10. Dependências

Antes de iniciar uma stack, verificar:

- quais stacks ela exige;
- se as dependências já estão na `develop`;
- se o banco está atualizado;
- se existem testes mínimos protegendo a mudança;
- se outra branch está alterando a mesma área.

Exemplo: Multiacademia depende de autorização, Router e migrations. Pedidos e pagamentos dependem de banco, multiacademia, estoque e validação.

## 11. Padrão de Pull Request

```markdown
# Stack XX — Nome da Stack

## Objetivo

## Alterações realizadas

## Arquivos principais alterados

## Banco de dados
- [ ] Não se aplica
- [ ] Migration criada
- [ ] Seeder criado
- [ ] Atualização de dados necessária

## Segurança
- [ ] Autenticação revisada
- [ ] Autorização revisada
- [ ] CSRF revisado
- [ ] Entradas validadas
- [ ] Nenhum segredo incluído

## Multiacademia
- [ ] Não se aplica
- [ ] Consultas respeitam `academia_id`
- [ ] Testes de isolamento criados

## Testes
- [ ] Automatizados
- [ ] Manuais
- [ ] Fluxo principal
- [ ] Casos de erro

## Riscos e impactos

## Checklist final
- [ ] Código revisado
- [ ] Sem debug temporário
- [ ] Sem arquivos desnecessários
- [ ] Sem código legado duplicado
- [ ] Documentação atualizada
```

## 12. Estratégia de merge

Para branches de stack, utilizar preferencialmente **Squash and merge**.

Vantagens:

- histórico mais limpo;
- uma entrega por commit na `develop`;
- reversão mais simples;
- eliminação de commits temporários.

## 13. Atualização com a `develop`

```bash
git checkout stack/XX-nome
git fetch origin
git merge origin/develop
```

Conflitos devem ser resolvidos na branch da stack, nunca diretamente na `develop`.

## 14. Release

Quando um ciclo estiver pronto:

```bash
git checkout develop
git pull origin develop
git checkout -b release/0.2.0
git push -u origin release/0.2.0
```

Na release entram somente:

- correções de bugs;
- ajustes pequenos;
- documentação;
- preparação de deploy;
- atualização de versão.

Depois da validação:

```text
release/0.2.0 → main
release/0.2.0 → develop
```

Criar tag:

```bash
git checkout main
git pull origin main
git tag -a v0.2.0 -m "Segurança e base técnica"
git push origin v0.2.0
```

## 15. Hotfix

Hotfix nasce da `main` e deve retornar para `main` e `develop`.

```bash
git checkout main
git pull origin main
git checkout -b hotfix/corrigir-falha-login
```

## 16. Convenções de nomes

```text
stack/01-seguranca-emergencial
stack/08a-entidade-academia
fix/corrigir-rota-login
hotfix/corrigir-vazamento-dados
release/0.2.0
docs/atualizar-readme
```

## 17. Convenção de commits

Utilizar Conventional Commits:

```text
feat(auth): implement secure logout
fix(router): handle numeric route parameters
security(config): remove tracked environment file
refactor(users): move persistence to repository
test(auth): add session fixation coverage
docs(setup): document development environment
chore(composer): remove tracked vendor directory
```

## 18. Controle visual

Criar um GitHub Project com estados:

```text
Backlog
Ready
Em análise
Em desenvolvimento
Em revisão
Em teste na develop
Bloqueada
Concluída na develop
Publicada na main
```

Cada stack ou sub-stack deve possuir uma Issue com objetivo, tarefas, critérios de aceite, dependências, riscos, PR e versão prevista.

## 19. Ambientes

| Ambiente | Branch | Banco sugerido |
|---|---|---|
| Desenvolvimento | `stack/*` | `gym_genesis_dev` |
| Testes | execução automatizada | `gym_genesis_test` |
| Homologação | `develop` | `gym_genesis_staging` |
| Produção | `main` | `gym_genesis_prod` |

Nunca utilizar banco de produção em desenvolvimento ou testes.

## 20. Regras para migrations

- Migration compartilhada não deve ser reescrita.
- Correções devem ser feitas por uma nova migration.
- Antes do merge, testar migrate, rollback e migrate novamente.
- Migrations devem ser executadas de forma controlada no deploy.

## 21. Trabalho paralelo

Pode ocorrer quando branches alteram módulos diferentes e não dependem da mesma migration ou do mesmo contrato.

Não deve ocorrer quando:

- uma stack depende da outra;
- ambas alteram autenticação ou Router;
- ambas alteram o mesmo esquema;
- uma remove código que a outra utiliza.

No início, trabalhar em uma stack principal por vez é mais seguro.

## 22. Definição de pronto para `develop`

- [ ] Objetivo cumprido e escopo respeitado.
- [ ] Código revisado.
- [ ] Sem credenciais ou debug.
- [ ] Entradas, autenticação e autorização revisadas.
- [ ] Isolamento por academia considerado.
- [ ] Migrations e testes executados.
- [ ] Documentação atualizada.
- [ ] Código legado substituído removido ou desativado.
- [ ] Branch atualizada com a `develop`.

## 23. Definição de pronto para `main`

- [ ] Ciclo completo integrado.
- [ ] Staging atualizado.
- [ ] Migrations executadas.
- [ ] Testes automatizados e manuais aprovados.
- [ ] Perfis e fluxos principais validados.
- [ ] Isolamento entre academias testado.
- [ ] Logs revisados.
- [ ] Sem erro crítico pendente.
- [ ] Rollback, versão e changelog preparados.

## 24. Regras resumidas

1. Nunca desenvolver diretamente na `main`.
2. Nunca desenvolver diretamente na `develop`.
3. Toda stack nasce da `develop`.
4. Toda stack retorna primeiro para a `develop`.
5. Toda mudança passa por Pull Request.
6. Toda branch deve possuir escopo pequeno.
7. Stacks grandes devem ser divididas.
8. A `develop` deve ser testada antes da `main`.
9. Hotfix deve entrar na `main` e na `develop`.
10. Migrations compartilhadas não devem ser reescritas.
11. Toda versão publicada deve receber tag.
12. A branch deve ser excluída após o merge.
13. O histórico deve permanecer simples e rastreável.

## 25. Primeira sequência prática

1. Criar tag do estado atual.
2. Criar a `develop`.
3. Proteger `main` e `develop`.
4. Criar o GitHub Project.
5. Criar Issues das quatro primeiras stacks.
6. Criar `stack/01-seguranca-emergencial`.
7. Implementar, revisar e testar.
8. Abrir PR para `develop`.
9. Testar a `develop`.
10. Iniciar a Stack 02.

Primeiro ciclo:

```text
Stacks 01–04 → develop → release/0.2.0 → main
```
