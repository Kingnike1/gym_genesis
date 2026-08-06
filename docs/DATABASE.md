# Banco de dados e migrations

## Fonte oficial

A partir desta stack, `database/migrations/` é a fonte oficial da estrutura do banco.

O arquivo `0001_legacy_baseline.up.sql` preserva o esquema atual que estava em `db/banco.sql`. O migrator remove do dump as instruções fixas de criação/seleção do schema e aplica a estrutura ao banco definido por `DB_NAME`.

Novas alterações não devem editar a baseline depois que ela for compartilhada. Devem ser criados novos pares:

```text
database/migrations/0002_nome_da_mudanca.up.sql
database/migrations/0002_nome_da_mudanca.down.sql
```

## Comandos

```bash
composer migrate
composer migrate:status
composer migrate:rollback
composer seed
```

## Ambientes

Use bancos separados:

```text
gym_genesis_dev
gym_genesis_test
gym_genesis_staging
gym_genesis_prod
```

Nunca execute rollback destrutivo no banco de produção. A rollback da baseline remove todas as tabelas e existe somente para bancos descartáveis de desenvolvimento e testes.

## Variáveis necessárias

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=gym_genesis_dev
DB_USER=gym_app
DB_PASSWORD=
```

## Seeders

Seeders SQL ficam em `database/seeders/` e são executados em ordem alfabética. O dump histórico `db/inserts.sql` não foi adotado automaticamente porque precisa ser auditado para dados pessoais, credenciais e dependências de ambiente.

## Charset e compatibilidade

A baseline é normalizada para `utf8mb4` e índices `VISIBLE` do MySQL Workbench são removidos para melhorar compatibilidade com MariaDB.

## Transações

Fluxos compostos devem utilizar:

```php
Database::transaction(function (PDO $pdo) {
    // operações que precisam confirmar ou falhar juntas
});
```

DDL do MySQL/MariaDB pode realizar commit implícito; por isso migrations só são registradas após execução completa. Antes de um merge, validar em banco descartável:

```bash
composer migrate
composer migrate:status
composer migrate:rollback
composer migrate
```

## Limitações conhecidas

- a baseline preserva nomes históricos de tabelas e colunas para evitar quebrar o código atual;
- padronizações destrutivas serão feitas por migrations posteriores e com migração de dados;
- tabelas de multiacademia pertencem à Stack 8;
- o esquema precisa ser validado tanto em MySQL quanto na versão de MariaDB usada pelo Docker.
