# Gym Genesis — Setup de Desenvolvimento

Este guia descreve como outro desenvolvedor ou agente consegue preparar o projeto a partir do estado auditado em 2026-09-03.

## 1. Pré-requisitos

### Obrigatórios para desenvolvimento manual

- Git
- PHP 8.2+
- Composer 2
- MySQL 8+ ou MariaDB 11.x
- extensões PHP: `pdo_mysql`, `mbstring`; `fileinfo` é necessária para fluxos de upload

### Recomendados

- Docker
- Docker Compose v2

### Qualidade/testes

As ferramentas abaixo são instaladas pelo Composer em `require-dev`:

- PHPUnit
- PHPStan
- PHP_CodeSniffer / PHPCBF

Não há `package.json` oficial na raiz, então não existe etapa NPM/Node obrigatória no estado atual.

## 2. Clonar o estado correto

A branch `main` e a `develop` ainda estão antigas no momento desta auditoria. Para reproduzir o código analisado, use temporariamente a branch acumulada:

```bash
git clone https://github.com/Kingnike1/gym_genesis.git
cd gym_genesis
git checkout stack-30-monitoramento-producao
```

Confirme:

```bash
git branch --show-current
git log -1 --oneline
git status
```

O último commit funcional usado como base desta auditoria é:

```text
3be6c9098a669308ffebeabf7e011664776d1b0a
```

Depois que a integração Git for corrigida no roadmap, este guia deve passar a apontar para `develop`.

## 3. Atenção antes de usar segredos

O estado auditado ainda possui um `.env` rastreado no Git. Não reutilize nenhum valor sensível que tenha sido versionado.

Para uma instalação nova:

1. não copie credenciais de commits/histórico;
2. gere senhas/segredos novos;
3. use `.env.example` somente como lista de nomes;
4. não commite `.env`.

A Sprint 01 do roadmap deve corrigir o rastreamento do arquivo.

## 4. Instalar dependências

```bash
composer validate --strict
composer install
```

Validação opcional de segurança:

```bash
composer audit --locked
```

## 5. Configurar ambiente

Crie um `.env` local a partir do exemplo, mas substitua todos os valores de placeholder por valores exclusivos do seu ambiente:

```bash
cp .env.example .env
```

Nunca publique o arquivo resultante.

### Variáveis da aplicação

```text
APP_ENV
APP_DEBUG
APP_SECRET
APP_TIMEZONE       opcional
SLOW_REQUEST_MS    opcional
LOG_STREAM         opcional
PORT               cloud/container quando aplicável
```

### Variáveis de banco

```text
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD
```

Para Docker Compose local, também é usado:

```text
DB_ROOT_PASSWORD
```

Para banco remoto com TLS, como Aiven:

```text
DB_SSL_CA          usado pelo start script do Render para criar o arquivo CA
DB_SSL_CA_PATH     caminho de CA lido diretamente pela classe Database
```

### Variáveis de e-mail

```text
MAIL_HOST
MAIL_PORT
MAIL_ENCRYPTION
MAIL_USERNAME
MAIL_PASSWORD
MAIL_FROM_ADDRESS
MAIL_FROM_NAME
```

O SMTP é necessário para validar recuperação de senha com envio real.

## 6. Banco de dados

Crie um database vazio e um usuário de aplicação sem privilégios administrativos desnecessários.

Depois execute:

```bash
composer migrate
composer migrate:status
```

O schema oficial vem de `database/migrations/`, não do dump antigo citado no README.

### Rollback

Em banco descartável de desenvolvimento/teste:

```bash
composer migrate:rollback
```

Não execute rollback destrutivo em banco real sem backup e plano de recuperação.

### Seed

```bash
composer seed
```

Leia `database/seeders/README.md` antes de adicionar/rodar seeders novos.

## 7. Executar sem Docker

Com PHP e banco configurados, uma opção simples de desenvolvimento é o servidor embutido do PHP:

```bash
php -S 127.0.0.1:8080 -t public public/index.php
```

Abra:

```text
http://127.0.0.1:8080/health
http://127.0.0.1:8080/ready
http://127.0.0.1:8080/login
```

Resultados esperados:

- `/health`: HTTP 200 sem depender do banco;
- `/ready`: HTTP 200 somente quando a conexão ao banco funciona;
- `/login`: tela de autenticação.

> O servidor embutido serve para desenvolvimento. A produção usa Apache/Docker.

## 8. Executar com Docker — estado auditado

### Build isolado

O build é comprovado pela CI e pode ser reproduzido com:

```bash
docker build -t gym-genesis:audit .
```

### Docker Compose — bloqueio conhecido

No estado auditado, **não considere `docker compose up` validado** sem antes resolver a Sprint 02.

Motivo:

- `Dockerfile` configura Apache na porta `10000`;
- `docker-compose.yml` publica `8080:8080`;
- `docker-compose.production.yml` também assume `8080`.

Essa inconsistência pode fazer o container estar ativo mas inacessível pela porta publicada.

Não esconda o problema alterando portas ad hoc em cada máquina; primeiro alinhe a configuração de forma oficial em Sprint própria.

## 9. Testes e qualidade

### Gate completo

```bash
composer check
```

Esse comando executa:

```text
PHPCS
PHPStan
PHPUnit
```

### Separados

```bash
composer lint
composer analyse
composer test
composer test:unit
composer test:integration
```

### Smoke tests específicos

Com banco de teste configurado:

```bash
composer test:tenancy
composer test:user-roles
```

Use banco descartável. Não rode smoke tests mutáveis contra produção.

## 10. Build

Não existe build frontend separado.

O build de entrega é a imagem Docker:

```bash
docker build -t gym-genesis:local .
```

Na CI, Buildx constrói a imagem sem publicá-la.

## 11. CI

Workflow principal:

```text
.github/workflows/ci.yml
```

No commit funcional `3be6c909...`, a CI #67 aprovou:

- Composer validate;
- Composer install;
- migrations;
- PHPCS;
- PHPStan;
- PHPUnit;
- Composer audit;
- Docker build.

Limitação atual: a CI não sobe a imagem e não testa requests HTTP.

## 12. Staging Render + Aiven

Arquivos:

```text
render.yaml
scripts/start-render.sh
docs/STAGING_DEPLOY.md
```

### Configuração real atual do código

- serviço Render: `gym-genesis-staging`;
- runtime Docker;
- plano free;
- branch: `stack-30-monitoramento-producao`;
- porta interna: `10000`;
- health check do Render: `/ping.html`;
- banco: Aiven/MySQL externo via variáveis;
- `/ready` continua sendo a verificação funcional do banco.

### Startup atual

O `start-render.sh`:

1. grava o CA em `/tmp/aiven-ca.pem` quando `DB_SSL_CA` existe;
2. inicia Apache imediatamente;
3. tenta migrations em background até 20 vezes;
4. mantém Apache online para diagnóstico mesmo quando migration/banco falha.

Portanto:

- `/ping.html` comprova apenas Render + container + Apache + arquivo estático;
- `/health` comprova bootstrap HTTP/liveness;
- `/ready` comprova acesso ao banco.

O último deploy real após essa mudança ainda precisa ser homologado.

## 13. Comandos de desenvolvimento diário

Antes de começar:

```bash
git status
git branch --show-current
git pull --ff-only
composer install
```

Antes de commit/PR:

```bash
composer validate --strict
composer check
composer audit --locked
docker build -t gym-genesis:check .
```

Se a alteração envolver schema:

```bash
composer migrate
composer migrate:status
```

Também execute os testes manuais/integração específicos do domínio alterado.

## 14. Estrutura que outro agente deve conhecer

```text
CLAUDE.md                     entrada para agentes
docs/PROJECT_STATUS.md        estado atual e riscos
docs/ROADMAP.md               próximas Sprints
docs/ARCHITECTURE.md          arquitetura real
docs/SETUP.md                 este guia
routes/web.php                 superfície web oficial
routes/api.php                 superfície API oficial
database/migrations/          schema oficial
composer.json                  comandos e dependências
tests/MANUAL_HOMOLOGATION.md  homologação manual
```

## 15. O que não fazer durante setup

- não importar dumps antigos apenas porque o README antigo recomenda;
- não usar credenciais de exemplo em ambientes compartilhados;
- não commitar `.env`, certificado CA ou senha SMTP;
- não desenvolver diretamente em `main`;
- não adicionar feature nova no legado;
- não alterar migration já aplicada apenas para “consertar” produção; crie migration nova quando o projeto estiver em produção;
- não considerar CI verde equivalente a runtime/staging homologado.