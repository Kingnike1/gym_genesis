# Gym Genesis — Arquitetura Atual

> Documento atualizado na auditoria de 2026-09-03 com base na branch `stack-30-monitoramento-producao`. Este arquivo descreve o código real atual; quando outro documento divergir, valide primeiro código, migrations, rotas e CI.

## Visão geral

O Gym Genesis é uma aplicação PHP 8.2 server-rendered, com arquitetura em camadas e suporte multiacademia. Não utiliza framework full-stack. O núcleo combina Router próprio, container simples, Controllers, Services, Repositories, Views PHP, Middleware, PDO/MySQL e Docker.

Fluxo HTTP oficial:

```text
Cliente HTTP
   ↓
Apache / DocumentRoot public/
   ↓
public/index.php
   ↓
bootstrap/app.php
   ├── Composer autoload
   ├── dotenv
   ├── ErrorHandler
   ├── Monolog / PSR-3
   ├── Container
   └── StorageInterface -> LocalStorage
   ↓
routes/web.php + routes/api.php
   ↓
Router
   ↓
Middlewares
   ├── autenticação/papel
   ├── contexto de academia
   └── token/escopo da API
   ↓
Controller
   ↓
Service
   ↓
Repository
   ↓
App\Services\Database / PDO
   ↓
MySQL/MariaDB
```

## Pontos de entrada

### Web

`public/index.php` é o front controller moderno. Ele:

- mede tempo de requisição;
- carrega `bootstrap/app.php`;
- obtém logger pelo container;
- carrega `routes/web.php` e `routes/api.php`;
- despacha pelo Router;
- registra requisições lentas.

### Diagnóstico

- `GET /health`: liveness sem consulta ao banco.
- `GET /ready`: readiness com `SELECT 1` no banco.
- `public/ping.html`: página estática temporária de diagnóstico de staging, sem bootstrap PHP.

`ping.html` não deve virar regra de negócio nem substituir `/health`/`/ready`.

## Bootstrap e container

`bootstrap/app.php`:

- exige `vendor/autoload.php`;
- carrega `.env` apenas quando o arquivo existe;
- usa `APP_ENV`/`APP_DEBUG`;
- mantém `display_errors=0`;
- define timezone;
- registra logger e `ErrorHandler`;
- instancia `Container`;
- faz binding de `LoggerInterface` e `StorageInterface`.

O container usa autowiring de tipos concretos. Interfaces e integrações precisam de binding explícito.

### Regra para novas implementações

- Controller recebe Service por construtor.
- Service recebe Repository/dependências por construtor.
- Controller não deve abrir conexão ou escrever SQL.
- View não deve acessar Repository/banco.
- Repository concentra SQL/persistência.

## Router

`routes/Router.php` suporta:

- GET, POST, PUT, PATCH e DELETE;
- parâmetros nomeados e constraints;
- grupos e prefixos;
- middleware por grupo e por rota;
- method override seguro para PUT/PATCH/DELETE vindo de POST;
- 404/405 via exceções;
- resolução de Controllers pelo container;
- geração de URLs considerando base path.

## Rotas web modernas

### Públicas

- `/`
- `/home`
- `/health`
- `/ready`
- `/login` GET/POST
- `/password/forgot` GET/POST
- `/password/reset` GET/POST

### Autenticadas

- `POST /logout`
- `POST /academy/select`

### Admin

Grupo `/admin` exige papel de administrador e academia ativa. A superfície moderna identificada cobre:

- dashboard;
- usuários;
- planos;
- produtos;
- pedidos.

### Professor

Grupo `/professor` exige papel de professor e academia ativa. A superfície moderna identificada cobre:

- dashboard;
- treinos;
- dietas.

### Aluno

Grupo `/student` exige papel de aluno e academia ativa. A superfície moderna identificada cobre:

- dashboard;
- treinos e execuções;
- dietas;
- perfil;
- progresso;
- avaliações físicas.

A existência de Repository/Service de um domínio não significa automaticamente que exista uma tela/rota moderna completa para ele.

## API

`routes/api.php` registra `/api/v1`.

Fluxo:

```text
Authorization: Bearer <token>
       ↓
ApiTokenMiddleware::authenticate()
       ↓
resolve token + academia + escopos
       ↓
Controller API
```

Endpoints comprovados atualmente:

- `GET /api/v1/me`;
- `GET /api/v1/students`, exigindo `students:read`.

A API deve ser classificada como parcial, não como API completa de todos os módulos.

## Autenticação e sessão

O fluxo moderno usa:

- `AuthController`;
- `UserService`/`UserRepository`;
- `SessionManager`;
- `SecurityHelper`;
- `LoginRateLimiter`.

Características atuais:

- CSRF no login/logout e fluxos mutáveis relevantes;
- logout somente POST;
- ID de sessão regenerado após autenticação;
- timeout por inatividade;
- cookie HttpOnly, SameSite e Secure sob HTTPS;
- rate limit de login por cliente + e-mail;
- erro de credencial sem enumeração explícita;
- `PASSWORD_DEFAULT` e rehash quando necessário.

O rate limiter atual é local e não deve ser assumido como distribuído entre múltiplas instâncias.

## Autorização e multiacademia

O sistema usa uma identidade global `usuario` e vínculos por academia.

Componentes principais:

- `AcademyContext`;
- `AcademyContextMiddleware`;
- `AuthMiddleware`;
- `AcademyRepository`;
- Repositories tenant-aware;
- auditoria de mudança de contexto.

Fluxo esperado:

```text
usuário autenticado
   ↓
vínculo ativo academia_usuario
   ↓
AcademyContext
   ↓
Repository inclui academia_id
```

A regra arquitetural é: dados operacionais não devem ser consultados sem contexto de academia quando pertencem a tenant.

## Persistência

`App\Services\Database` fornece a conexão PDO central.

Configurações relevantes:

- charset `utf8mb4`;
- `PDO::ERRMODE_EXCEPTION`;
- prepares emulados desativados;
- transações via helper;
- suporte opcional a CA TLS (`DB_SSL_CA_PATH`).

### Fonte oficial de schema

A fonte moderna é `database/migrations/`, não dumps manuais do README legado.

Cadeia atual:

```text
0001 legacy baseline
0002 multiacademia
0003 tenancy audit
0004 users/roles/status
0005 students
0006 professors
0007 workouts
0008 diets
0009 physical assessments
0010 memberships
0011 inventory
0012 orders/payments
0013 files
0014 password reset
0015 performance indexes
0016 privacy controls
0017 API tokens
```

O runner é `app/Database/Migrator.php`, exposto por `scripts/migrate.php` e scripts Composer.

## Domínios modernos

### Identidade e academias

- usuários e papéis;
- academias/unidades/vínculos;
- contexto tenant;
- auditoria.

### Operação da academia

- alunos;
- professores/vínculos;
- fichas de treino/exercícios/execuções;
- planos alimentares/refeições/itens/histórico;
- avaliações físicas;
- planos comerciais/matrículas.

### Comercial

- produtos e estoque;
- movimentação de estoque;
- pedidos e itens;
- registros de pagamento/idempotência.

Não foi identificada integração ativa com gateway financeiro real; o domínio interno não equivale a processamento financeiro externo completo.

### Plataforma

- armazenamento de arquivos;
- recuperação de senha;
- validação/erros;
- logging;
- controles técnicos de privacidade;
- API tokens/webhook verifier;
- health/readiness;
- backup/restore.

## Storage

`StorageInterface` está ligado a `LocalStorage` no bootstrap.

O desenho desacopla regra de aplicação do filesystem, mas o adapter ativo ainda é local. Isso importa para cloud: em Render Free o filesystem da aplicação é efêmero, logo uploads locais não são persistência de produção.

## Logging e erros

O bootstrap registra Monolog/PSR-3 e `ErrorHandler` global.

As respostas de exceções HTTP são centralizadas e os logs incluem contexto de requisição. O código diferencia erros de autenticação, autorização, validação, 404/405 e falhas internas.

## Frontend

O frontend moderno é HTML server-rendered por Views PHP, com CSS compartilhado em `public/assets/css/app.css` e navegação/layout comum.

Não existe `package.json` na raiz e não há pipeline Node/NPM oficial detectado.

A aplicação ainda convive com frontend/procedural legado. Portanto o frontend não deve ser descrito como completamente migrado.

## Infraestrutura

### CI

`.github/workflows/ci.yml` executa:

- PHP 8.2;
- MariaDB 11.4 de teste;
- `composer validate --strict`;
- `composer install`;
- migrations;
- `composer check` (PHPCS + PHPStan + PHPUnit);
- `composer audit --locked`;
- Docker build via Buildx.

Limitação: a CI atual constrói a imagem, mas não inicia o container nem faz smoke HTTP.

### Docker local/produção

Há `Dockerfile`, `docker-compose.yml` e `docker-compose.production.yml`.

**Inconsistência auditada:** o Dockerfile atual configura Apache para porta `10000`, enquanto os Compose ainda mapeiam/checavam porta `8080`. Isso deve ser corrigido em Sprint própria antes de tratar o Compose como runtime validado.

### Staging

`render.yaml` aponta para `stack-30-monitoramento-producao`, plano gratuito, health check estático `/ping.html`, porta `10000` e variáveis externas de banco/SMTP.

`scripts/start-render.sh`:

1. prepara CA do Aiven se fornecido;
2. inicia Apache imediatamente;
3. executa migrations em background com tentativas;
4. mantém Apache online para diagnóstico mesmo se migrations não concluírem.

Essa lógica é específica de staging/diagnóstico. `/ready`, e não `/ping.html`, é a verificação de disponibilidade funcional do banco.

## Código legado

A arquitetura moderna declara `app/` como direção oficial, mas ainda existem implementações anteriores, incluindo:

- `controller/` com diversos controllers procedurais por entidade;
- `code/`, incluindo `funcao.php` grande, scripts/listagens e testes ad hoc;
- `code/tests/`;
- páginas/protótipos históricos em outros diretórios preservados.

### Regras para o legado

1. Não adicionar feature nova nele.
2. Não remover arquivo apenas porque parece antigo.
3. Identificar consumidor/rota/link antes de remoção.
4. Validar a substituição moderna.
5. Remover em entrega pequena e reversível.

## Dívidas arquiteturais principais

1. Stacks 01 e 02 não pertencem à ancestralidade da Stack 30; segurança/limpeza ficaram parcialmente fora da linha atual.
2. `main` e `develop` não contêm a evolução real.
3. Código legado ainda é volumoso e potencialmente alcançável.
4. Camadas de alguns domínios existem sem superfície HTTP completa comprovada.
5. Suíte automatizada é pequena para o número de módulos.
6. Docker build passa, mas runtime Compose tem inconsistência de porta.
7. Staging gratuito possui filesystem efêmero para uploads.
8. Rate limit local precisa de storage compartilhado se houver escala horizontal.

## Princípios de continuidade

- Segurança e tenant isolation antes de conveniência.
- Uma fonte oficial de schema: migrations.
- Uma implementação moderna por fluxo antes de remover legado.
- CI verde não substitui smoke runtime e homologação.
- Não introduzir framework ou microserviço sem necessidade concreta.
- Não alterar migrations já aplicadas em produção futura; criar novas migrations.
- Não confiar em README antigo quando divergir do código.
- Toda alteração relevante deve ter teste e documentação proporcionais.