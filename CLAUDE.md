# Gym Genesis — Guia para Agentes de Desenvolvimento

## Leia antes de alterar qualquer coisa

Todo agente, IA ou desenvolvedor deve ler nesta ordem:

1. `CLAUDE.md`
2. `docs/PROJECT_STATUS.md`
3. `docs/ROADMAP.md`
4. `docs/ARCHITECTURE.md`
5. `docs/SETUP.md`

Quando a tarefa envolver um domínio específico, leia também a documentação correspondente em `docs/` e os testes em `tests/`.

## O que é o projeto

Gym Genesis é uma plataforma PHP 8.2 para gestão multiacademia. A implementação moderna usa:

```text
public/index.php
→ bootstrap
→ Router
→ Middleware
→ Controller
→ Service
→ Repository
→ PDO / MySQL-MariaDB
```

O frontend é server-rendered com Views PHP e CSS/JS tradicional. O projeto não usa framework full-stack nem pipeline Node oficial.

## Estado de Git que exige atenção

Na auditoria de 2026-09-03:

- `main` e `develop` ainda estavam no commit antigo `4080292...`;
- o estado real acumulado estava em `stack-30-monitoramento-producao`;
- o último commit funcional auditado era `3be6c909...`;
- a branch acumulada estava 359 commits à frente de `main`;
- PRs #1–#30 continuavam abertos/draft;
- Stacks 01 e 02 não faziam parte da ancestralidade da Stack 30.

Antes de implementar, confirme novamente esses fatos no GitHub. Não suponha que ainda são verdadeiros depois desta documentação.

## Problemas que não devem ser ignorados

Consulte `docs/PROJECT_STATUS.md`, mas os principais no momento da auditoria são:

1. `.env` ainda rastreado na linha da Stack 30;
2. Stacks 01/02 de segurança/limpeza fora da cadeia acumulada;
3. Dockerfile na porta 10000 enquanto Compose usa 8080;
4. `main`/`develop` desatualizadas;
5. código legado volumoso;
6. staging Render/Aiven não homologado após o último fix;
7. cobertura automatizada pequena diante do tamanho do domínio.

Não comece feature nova antes de resolver a Sprint corrente do `docs/ROADMAP.md`.

## Arquitetura e convenções

### Código oficial

Novas implementações pertencem a:

- `app/`
- `bootstrap/`
- `routes/`
- `public/` apenas para front controller/assets públicos
- `database/migrations/`
- `scripts/`
- `tests/`

### Legado

Diretórios como `controller/`, `code/`, páginas procedurais e protótipos históricos devem ser considerados legado.

Regras:

- não adicionar feature nova no legado;
- não remover sem mapear consumidor;
- migrar um fluxo por vez;
- validar substituição antes da remoção.

### Dependências

- Controllers recebem Services por construtor.
- Services recebem Repositories/dependências.
- Views não fazem SQL.
- Controllers não fazem SQL.
- Repositories respeitam `AcademyContext` em dados tenant-scoped.
- Não criar abstrações ou interfaces sem necessidade concreta.

### Banco

`database/migrations/` é a fonte oficial de schema.

Não use o dump antigo como fonte principal.

Não edite migration já aplicada em ambiente real; crie migration posterior quando necessário.

### Segurança

Nunca quebrar:

- CSRF em mudanças de estado;
- logout POST;
- session regeneration;
- tenant isolation;
- autorização por papel/recurso;
- prepared statements;
- proteção de segredos;
- API scopes;
- erro público sem stack trace em produção.

Nunca escrever tokens, passwords, SMTP credentials, CA privada ou API keys em commit/documentação.

## Comandos importantes

### Dependências

```bash
composer validate --strict
composer install
```

### Banco

```bash
composer migrate
composer migrate:status
composer migrate:rollback   # somente ambiente seguro/descartável
composer seed
```

### Qualidade e testes

```bash
composer check
composer lint
composer analyse
composer test
composer test:unit
composer test:integration
composer test:tenancy
composer test:user-roles
composer audit --locked
```

### Build

```bash
docker build -t gym-genesis:check .
```

O Compose possuía inconsistência de porta na auditoria; consulte `docs/PROJECT_STATUS.md` antes de assumir que `docker compose up` funciona.

## Como validar uma alteração

1. Ler status/roadmap/arquitetura.
2. Confirmar branch e `git status`.
3. Identificar o menor escopo reversível.
4. Verificar impacto multiacademia e autorização.
5. Criar/ajustar teste proporcional.
6. Rodar `composer check`.
7. Rodar `composer audit --locked` quando dependências forem afetadas.
8. Rodar migrations em DB descartável se schema mudar.
9. Rodar Docker build quando runtime/infra mudar.
10. Para mudanças web, testar HTTP real além da CI quando possível.
11. Atualizar documentação de estado se a conclusão mudar.

## Testes e registro

A estratégia oficial está em `docs/TESTING_STRATEGY.md`.

Testes não executados não podem ser declarados aprovados.

A homologação manual consolidada está em:

```text
tests/MANUAL_HOMOLOGATION.md
```

Os arquivos históricos `tests/stack-XX/EXECUTED.md` podem estar desatualizados; compare sempre com CI e código atual.

## Documentos por assunto

- Estado real: `docs/PROJECT_STATUS.md`
- Próximas Sprints: `docs/ROADMAP.md`
- Arquitetura: `docs/ARCHITECTURE.md`
- Setup/comandos: `docs/SETUP.md`
- Banco: `docs/DATABASE.md`
- Multiacademia: `docs/MULTIACADEMIA.md`
- Routing: `docs/ROUTING.md`
- Segurança/configuração: documentação específica existente + Project Status
- CI: `docs/CI_CD.md`
- Docker: `docs/DOCKER.md`
- Staging: `docs/STAGING_DEPLOY.md`
- Testes: `docs/TESTING_STRATEGY.md`, `docs/AUTOMATED_TESTING.md`
- API: `docs/API_INTEGRATIONS.md`, `docs/openapi.yaml`

## Regra de decisão quando docs divergem

Prioridade de evidência:

```text
1. código executado + CI atual
2. migrations/rotas/composer/configuração atual
3. documentação recente validada contra código
4. README e documentos históricos
```

Não “corrija” o código para fazê-lo coincidir com documentação antiga sem primeiro confirmar a intenção.

## Próxima Sprint na auditoria de 2026-09-03

**Sprint 01 — Segurança e saneamento da linha atual.**

Não implemente a Sprint automaticamente só porque este arquivo foi lido. Primeiro confirme com o responsável que o estado Git não mudou desde a auditoria e siga `docs/ROADMAP.md`.