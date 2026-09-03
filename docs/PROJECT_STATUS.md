# Gym Genesis — Project Status

## Identificação da auditoria

- **Data da auditoria:** 2026-09-03
- **Repositório:** `Kingnike1/gym_genesis`
- **Branch auditada como estado real de desenvolvimento:** `stack-30-monitoramento-producao`
- **Último commit funcional analisado:** `3be6c9098a669308ffebeabf7e011664776d1b0a` — `fix(staging): start Apache before database migrations`
- **Branch padrão do GitHub:** `main`
- **Situação de `main` e `develop`:** ambas ainda apontam para `4080292fe892589885797a59e377650e6ab8f153`, de 2026-08-05, e não contêm a evolução acumulada da Stack 30.
- **Diferença `main` → branch auditada:** branch auditada está 359 commits à frente e 0 atrás.

> Importante: o GitHub remoto não fornece o `git status` do computador de um desenvolvedor. O estado de arquivos não commitados de uma cópia local não pôde ser verificado nesta auditoria. O commit remoto auditado é imutável, mas isso não equivale a afirmar que um clone local do usuário está limpo.

## Objetivo atual do projeto

O Gym Genesis é uma plataforma PHP para gestão de academias com arquitetura multiacademia. A direção técnica atual é uma aplicação server-side em PHP 8.2, com Router próprio, Controllers, Services, Repositories, Views PHP, Middleware, PDO/MySQL/MariaDB, Composer e Docker.

O código moderno oficial vive principalmente em `app/`, `bootstrap/`, `routes/`, `public/index.php`, `database/migrations/`, `scripts/` e `tests/`. O repositório ainda preserva uma quantidade relevante de código procedural legado.

## Resumo executivo

| Área | Status | Avaliação |
|---|---|---|
| Arquitetura moderna | ✅ CONCLUÍDO | Front controller, bootstrap, container, Router, middleware, Services e Repositories existem e são usados pelas rotas modernas. |
| Composer/dependências | ✅ CONCLUÍDO | `composer.json`/lockfile foram validados pela CI no commit auditado. |
| Banco/migrations | ✅ CONCLUÍDO | Migrations `0001` a `0017` executaram com sucesso na CI sobre MariaDB 11.4. |
| Autenticação/sessão | ✅ CONCLUÍDO | Sessão centralizada, CSRF, logout POST, rate limit, rehash de senha e expiração por inatividade estão implementados. |
| Autorização/multiacademia | ✅ CONCLUÍDO no núcleo | Middleware por perfil e contexto de academia existem; smoke tests de tenancy/papéis existem. Homologação manual ainda é necessária. |
| Frontend moderno | 🟡 PARCIAL | Há layout compartilhado e Views modernas, mas o repositório ainda possui frontend/procedural legado e não há cobertura E2E. |
| API | 🟡 PARCIAL | `/api/v1/me` e `/api/v1/students` existem com Bearer token/escopo. Não há API completa para todos os módulos. |
| Integrações externas | 🟡 PARCIAL | SMTP/PHPMailer e infraestrutura de webhook existem; nenhuma integração completa com gateway de pagamento/catraca foi identificada nas rotas oficiais. |
| Loja/estoque/pedidos | 🟡 PARCIAL | Camada de domínio/repository e rotas administrativas existem; pagamento externo real não foi identificado. |
| Planos/matrículas | 🟡 PARCIAL | Modelo/repository de matrícula existe, mas não foi identificado Controller/rota moderna dedicada para administrar todo o ciclo de matrícula. |
| Professores/alunos | 🟡 PARCIAL | Domínio e consultas existem; a superfície web moderna é assimétrica e ainda convive com legado. |
| Uploads | 🟡 PARCIAL | Storage local seguro existe; staging Render Free usa filesystem efêmero e não garante persistência após restart/redeploy. |
| LGPD | 🟡 PARCIAL | Estrutura técnica de consentimento/retention/requests existe; decisões jurídicas e fluxo web completo não estão fechados. |
| Testes automatizados | 🟡 PARCIAL | CI está verde, porém a suíte automatizada é pequena: um teste unitário principal e dois smoke tests de integração identificados. |
| CI | ✅ CONCLUÍDO | No commit auditado passaram Composer validate/install, migrations, quality gate, security audit e Docker build. |
| Runtime local Docker Compose | 🐛 COM PROBLEMA | Dockerfile escuta porta 10000, enquanto `docker-compose.yml` e `docker-compose.production.yml` publicam/checavam 8080. A CI apenas constrói a imagem e não detecta isso. |
| Staging Render/Aiven | 🟡 PARCIAL | Configuração existe e houve correção para subir Apache antes das migrations, mas não há confirmação registrada de homologação bem-sucedida após o último commit. |
| Integração Git | 🐛 COM PROBLEMA | `main`/`develop` estão antigos; PRs 1–30 continuam draft/abertos e não foram mergeados. |
| Higiene do repositório | 🐛 COM PROBLEMA | `.env` e `.vscode/launch.json` ainda estão rastreados na branch auditada; `.gitignore` é inconsistente. |
| Código legado | ⚠️ DÍVIDA TÉCNICA | `controller/`, `code/`, páginas procedurais e outros artefatos antigos continuam presentes. |

## Git e histórico real

### Branches

Existem `main`, `develop` e as branches `stack-01-*` até `stack-30-*`.

A cadeia efetiva de desenvolvimento está empilhada a partir da Stack 03:

`develop -> stack-03 -> stack-04 -> ... -> stack-30`

As Stacks 01 e 02 ficaram em PRs independentes baseados em `develop` e não fazem parte da ancestralidade da Stack 30.

### Consequência importante

As alterações planejadas nas Stacks 01 e 02 não podem ser consideradas integradas. Isso é confirmado pelo estado atual da Stack 30:

- `.env` ainda aparece como arquivo rastreado;
- `.vscode/launch.json` ainda está presente;
- código e scripts legados permanecem;
- `.gitignore` ainda contém regras duplicadas e ignora `composer.json`/`composer.lock` apesar de ambos serem arquivos oficiais rastreados.

### Pull Requests

Os PRs #1 a #30 continuam **open + draft + não mergeados** no momento da auditoria. A Stack 30 aponta para a Stack 29, e assim sucessivamente. Não considerar `develop` como versão integrada atual.

## Estrutura atual relevante

```text
gym_genesis/
├── .github/workflows/       # CI e release
├── .vscode/                 # configuração local ainda rastreada
├── app/
│   ├── Container/
│   ├── Controllers/
│   ├── DTOs/
│   ├── Database/
│   ├── Enums/
│   ├── Exceptions/
│   ├── Helpers/
│   ├── Http/
│   ├── Integrations/
│   ├── Logging/
│   ├── Middleware/
│   ├── Models/
│   ├── Repositories/
│   ├── Security/
│   ├── Services/
│   ├── Storage/
│   ├── Tenancy/
│   ├── Validation/
│   └── Views/
├── bootstrap/
├── code/                    # legado / testes ad hoc / funções grandes
├── controller/              # controllers procedurais legados
├── database/
│   ├── migrations/
│   └── seeders/
├── docs/
├── public/
│   ├── assets/
│   ├── index.php
│   └── ping.html             # diagnóstico temporário de staging
├── routes/
│   ├── Router.php
│   ├── web.php
│   └── api.php
├── scripts/
├── tests/
│   ├── Integration/
│   ├── Unit/
│   ├── MANUAL_HOMOLOGATION.md
│   └── stack-08 ... stack-30/
├── composer.json
├── composer.lock
├── docker-compose.yml
├── docker-compose.production.yml
├── Dockerfile
└── render.yaml
```

Além dessa árvore moderna, ainda existem diretórios e páginas procedurais históricos preservados. Sua utilização real deve ser verificada módulo a módulo antes de remoção.

## Tecnologias verificadas

- PHP `^8.2`
- Composer
- PDO MySQL
- MySQL/MariaDB; CI usa MariaDB 11.4
- Apache
- Docker / Docker Compose
- PHPUnit 11
- PHPStan 2.x
- PHP_CodeSniffer / PSR-12
- Monolog 3 / PSR-3
- PHPMailer
- vlucas/phpdotenv
- HTML/CSS/JavaScript server-rendered; nenhum `package.json` foi encontrado na raiz
- Render para staging da aplicação
- Aiven MySQL como desenho atual de banco de staging

## Funcionalidades verificadas no código moderno

### ✅ Implementadas no núcleo

- Login e logout seguro por POST/CSRF.
- Sessão com regeneração de ID e timeout.
- Rate limiting local no login.
- Recuperação de senha com token seguro e PHPMailer.
- Papéis Admin, Professor e Aluno.
- Contexto multiacademia e seleção de academia.
- Router com GET/POST/PUT/PATCH/DELETE, grupos e middleware.
- CRUD moderno de usuários administrativos.
- Planos comerciais.
- Produtos.
- Pedidos administrativos.
- Treinos e execução de treinos pelo aluno.
- Dietas/planos alimentares e visualização do aluno.
- Perfil/progresso/avaliações físicas do aluno.
- Migrations e runner próprio.
- Logs e tratamento global de erro.
- Liveness `/health` e readiness `/ready`.
- API v1 protegida por token com `/me` e `/students`.
- Infraestrutura de armazenamento de arquivos.
- Estruturas de privacidade/LGPD.
- Backups/restores por scripts.

### 🟡 Implementação parcial ou sem superfície completa comprovada

- Administração específica de professores.
- Administração específica de alunos além do fluxo genérico de usuários/perfis.
- Ciclo web completo de matrículas.
- Processamento de pagamento com provedor real.
- Interface web para arquivos/uploads.
- Interface web para controles LGPD.
- Gestão/expedição de tokens de API por interface administrativa.
- Recepção de webhooks através de endpoint oficial; existe verificador HMAC, mas não foi encontrada rota de webhook em `routes/api.php`.
- Persistência de uploads no staging gratuito.
- Monitoramento/alertas externos.

### ❓ Funcionalidades legadas com estado funcional não comprovado

O diretório `controller/` contém módulos procedurais como alimentos, assinaturas, aulas agendadas, cupons, dietas e outros. `code/` também contém uma função monolítica grande (`funcao.php`) e testes/adapters ad hoc. O fluxo oficial moderno não expõe necessariamente todas essas capacidades.

Não remover esses arquivos antes de mapear consumidores e substituições.

## Banco de dados

A fonte oficial moderna é `database/migrations/`.

Migrations existentes e validadas pela CI:

1. `0001_legacy_baseline`
2. `0002_multiacademia`
3. `0003_tenancy_audit`
4. `0004_user_roles_status`
5. `0005_students`
6. `0006_professors`
7. `0007_workouts`
8. `0008_diets`
9. `0009_physical_assessments`
10. `0010_memberships`
11. `0011_inventory`
12. `0012_orders_payments`
13. `0013_files`
14. `0014_password_reset`
15. `0015_performance_indexes`
16. `0016_privacy_controls`
17. `0017_api_tokens`

A CI comprova que um banco de teste MariaDB 11.4 vazio aceita a cadeia atual. Isso não substitui teste de migração sobre cópia de dados reais existentes.

## Frontend

O frontend atual é server-rendered em Views PHP, com CSS compartilhado moderno em `public/assets/css/app.css` e algum JavaScript/legado. Não existe pipeline Node/NPM detectado.

### Dívida

- layouts modernos convivem com páginas legadas;
- não existe teste automatizado de navegador/E2E identificado;
- o README ainda descreve partes do frontend/rotas antigas;
- a experiência completa de todos os módulos não foi homologada.

## Backend

Fluxo oficial:

`public/index.php -> bootstrap/app.php -> routes -> Router -> Middleware -> Controller -> Service -> Repository -> Database`

O bootstrap registra container, logger e storage. Exceções são tratadas globalmente. O Router resolve controllers pelo container.

## APIs e integrações externas

### API própria

- prefixo `/api/v1`;
- autenticação via Bearer token;
- escopos;
- contexto de academia derivado do token;
- `/api/v1/me`;
- `/api/v1/students` com escopo `students:read`.

### Integrações

- SMTP via PHPMailer para recuperação de senha;
- Aiven MySQL via PDO/TLS no staging;
- Render Web Service para staging;
- HMAC SHA-256 para verificação de webhook disponível no código.

### Não comprovado

- gateway real de pagamentos;
- integração de catraca;
- webhook de provedor exposto por rota oficial;
- sistema externo de alertas/observabilidade.

## Autenticação e autorização

### ✅ Existente

- `SessionManager`;
- cookies HttpOnly/SameSite e Secure sob HTTPS;
- regeneração de sessão;
- expiração por inatividade;
- CSRF;
- `LoginRateLimiter`;
- erros de login neutros;
- `PASSWORD_DEFAULT` + rehash;
- `AuthMiddleware` por papel;
- `AcademyContextMiddleware`;
- proteção de recursos do aluno;
- tokens de API com escopos.

### ⚠️ Limitações

- rate limiter de login/API usa estratégia local e não deve ser assumido distribuído;
- homologação manual de IDOR e troca de academia continua necessária;
- código procedural legado precisa ser auditado separadamente antes de ser exposto em produção.

## Variáveis de ambiente necessárias

Não registrar valores reais nos documentos.

### Aplicação

- `APP_ENV`
- `APP_DEBUG`
- `APP_SECRET`
- `APP_TIMEZONE` (opcional; default `America/Sao_Paulo`)
- `SLOW_REQUEST_MS` (opcional)
- `LOG_STREAM` (opcional)
- `PORT` em ambientes de cloud/container quando aplicável

### Banco

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `DB_ROOT_PASSWORD` para o MariaDB do Docker Compose local/produção
- `DB_SSL_CA` no fluxo Render/Aiven
- `DB_SSL_CA_PATH` quando o certificado já estiver disponível como arquivo

### SMTP

- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_ENCRYPTION`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`

## Testes existentes

### Automatizados identificados

- PHPUnit: `tests/Unit/Validation/ValidatorTest.php`.
- Integração/smoke: `TenancyIsolationSmoke.php`.
- Integração/smoke: `UserRoleIsolationSmoke.php`.
- PHPCS.
- PHPStan.
- Composer audit.
- Docker build na CI.

### Testes manuais

- `tests/MANUAL_HOMOLOGATION.md` consolida os testes manuais atuais.
- existem registros `EXECUTED.md`, `PENDING.md` e `MANUAL.md` para stacks posteriores.

### Inconsistência documental

`tests/stack-30/EXECUTED.md` ainda registra uma falha antiga de Composer/Docker, mas a CI #67 no commit auditado está verde. O arquivo de histórico de testes está desatualizado e não deve ser usado sozinho para inferir o estado atual.

## Testes realmente comprovados nesta auditoria

### ✅ PASSOU — GitHub Actions CI #67, commit `3be6c909...`

- Composer validate --strict.
- Instalação de dependências.
- Aplicação das migrations.
- Quality gate (`composer check` = PHPCS + PHPStan + PHPUnit).
- Composer security audit.
- Docker build.

### ❓ Não executado diretamente nesta auditoria

A tentativa de obter um working tree local descartável foi bloqueada por indisponibilidade de resolução DNS para `github.com`. Por isso não foi possível executar localmente:

- `git status` do clone;
- `composer install` local;
- `docker compose up`;
- requests HTTP locais;
- runtime Render/Aiven.

A CI remota é a evidência executável utilizada para Composer, lint/análise, testes, migrations e build.

## Bugs e inconsistências detectadas

### P0

1. **`.env` está rastreado em repositório público.** O conteúdo não foi reproduzido nesta auditoria. Mesmo que contenha apenas placeholders, isso viola a política pretendida pela Stack 01. Se alguma credencial real já foi versionada, deve ser considerada comprometida e rotacionada.
2. **Stacks 01 e 02 não estão integradas na cadeia atual.** Segurança emergencial e limpeza não podem ser consideradas concluídas.
3. **Porta Docker inconsistente:** o Dockerfile atual configura Apache/EXPOSE em `10000`, mas `docker-compose.yml` e `docker-compose.production.yml` publicam `8080:8080`. CI faz build, mas não sobe a imagem. Isso pode bloquear execução local/produção via Compose.

### P1

1. `main` e `develop` não representam o estado atual e estão 359 commits atrás da branch auditada.
2. 30 PRs continuam abertos/draft; a integração Git não foi concluída.
3. `.gitignore` contém duplicações e regras incorretas para `composer.json` e `composer.lock`.
4. README está significativamente desatualizado: PHP 8.1 vs 8.2, dump SQL vs migrations, caminhos `/gym_genesis`, bcrypt vs `PASSWORD_DEFAULT`, licença MIT vs Composer `proprietary` e fluxo de PR para `main`.
5. Staging Render/Aiven não foi confirmado funcional depois do último fix; CI não executa o container/HTTP.
6. Testes automatizados têm cobertura pequena para a quantidade de domínios.
7. `tests/stack-30/EXECUTED.md` está desatualizado em relação à CI verde.
8. Homologação manual de autorização/multiacademia e fluxos críticos ainda não está registrada como concluída.

### P2

1. Grande volume de código legado permanece e pode duplicar regras atuais.
2. `public/ping.html` e lógica especial de staging são diagnósticos temporários e devem ser revistos depois que o deploy estabilizar.
3. Infraestruturas de matrícula, LGPD, arquivos, API tokens e webhooks não possuem superfície web/API completa comprovada.
4. Upload local não é persistente no Render Free.
5. Não há teste HTTP/E2E/container-runtime na CI.
6. Branch protection aparece desabilitada nas branches consultadas.

### P3

1. Atualização visual e eliminação gradual de estilos/Views legadas.
2. Expansão da API apenas depois que os fluxos web e de domínio estiverem homologados.
3. Métricas/alertas externos e otimizações adicionais após observabilidade real.

## TODO/FIXME, mocks, placeholders e temporários

Não foi possível executar um `grep` branch-aware local por causa da falha de clone/DNS, portanto não se afirma que todos os comentários `TODO`/`FIXME` foram enumerados.

Itens temporários/placeholder comprovados por estrutura e configuração:

- `public/ping.html`: diagnóstico temporário de conectividade do staging;
- `scripts/start-render.sh`: comportamento especial de staging que mantém Apache online mesmo quando migrations falham;
- `.env.example`: placeholders esperados, não segredos;
- README contém credenciais de teste de exemplo e instruções antigas;
- `code/teste_*`, `code/testes_geral.php` e `code/tests/`: testes/ad hoc legados;
- `controller/` e grande parte de `code/`: legado explicitamente congelado pela arquitetura atual.

## Segurança

### Pontos positivos

- erros públicos desativados no bootstrap;
- PDO com exceptions e prepares reais;
- CSRF e sessão centralizados;
- autenticação e papéis;
- tenancy;
- tokens de API por hash/escopo;
- audit/logging;
- Composer audit na CI;
- Docker executa como `www-data`;
- conexão TLS opcional ao Aiven.

### Riscos

- `.env` rastreado é o risco mais imediato do repositório;
- código legado procedural pode contornar middleware/router moderno se ainda for publicamente alcançável;
- rate limiting local não é adequado a múltiplas instâncias;
- homologação de isolamento entre tenants deve ser completada manualmente;
- não assumir conformidade LGPD somente pela presença de tabelas/Repositories.

## Performance

### Existente

- índices de performance na migration 0015;
- paginação e limites em partes do domínio;
- slow request logging configurável;
- OPcache no Dockerfile.

### Dívida

- ausência de benchmark/load test;
- ausência de métricas de banco/aplicação em staging real;
- legado pode conter consultas fora do padrão tenant-aware/otimizado.

## Documentação existente: confiança

A documentação deve ser tratada por nível de confiança:

- **Alta:** código atual, migrations, `composer.json`, CI e rotas.
- **Média:** docs de stacks recentes quando coincidem com o código.
- **Baixa/atualização necessária:** README, `tests/stack-30/EXECUTED.md`, partes de `docs/STAGING_DEPLOY.md` que ainda descrevem porta 8080, `/ready` como health check e migrations antes do Apache. O código atual usa porta 10000, health check `/ping.html` e Apache sobe antes das migrations.

## Próxima ação recomendada

**Sprint 01 do novo roadmap: Integração de segurança e saneamento do repositório.**

Motivo: antes de continuar funcionalidade ou homologação, é necessário resolver o estado Git incoerente, incorporar corretamente as intenções das Stacks 01/02 à linha atual, remover `.env` do rastreamento, revisar/rotacionar qualquer segredo histórico, corrigir `.gitignore` e então restabelecer uma branch de integração confiável.

Nenhuma feature nova deve começar antes dessa etapa.